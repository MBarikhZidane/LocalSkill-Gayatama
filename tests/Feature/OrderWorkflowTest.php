<?php

namespace Tests\Feature;

use App\Models\Conservation;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Services\OrderWorkflow;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        DB::purge('sqlite');
        // The supplied repository omits the original chat creation migration.
        // Bootstrap its existing non-chat schema, then exercise the actual additive migration.
        foreach (glob(database_path('migrations/*.php')) as $path) {
            if (str_contains($path, 'add_order_id_to_conservations')) {
                continue;
            }
            (require $path)->up();
        }
    }

    private function order(string $status = 'pending'): Order
    {
        $customer = User::factory()->create(['university_id' => null, 'study_program_id' => null]);
        $provider = User::factory()->create(['university_id' => null, 'study_program_id' => null]);
        $service = Service::factory()->create(['user_id' => $provider->id]);

        return Order::factory()->create(['customer_id' => $customer->id, 'provider_id' => $provider->id, 'service_id' => $service->id, 'status' => $status, 'started_at' => null, 'completed_at' => null]);
    }

    private function action(Order $order, string $action, array $data = []): TestResponse
    {
        return $this->postJson(route('user.workflow.write', [$order, $action]), $data + ['confirmation' => 1]);
    }

    public function test_lifecycle_requires_customer_confirmation_and_preserves_revision_history(): void
    {
        $order = $this->order();
        $this->actingAs($order->provider);
        $this->action($order, 'accept')->assertOk()->assertJsonPath('status', 'in_progress');
        $this->action($order, 'decline')->assertConflict();
        $this->action($order, 'submit', ['body' => 'First delivery'])->assertOk()->assertJsonPath('status', 'submitted');
        $this->action($order, 'confirm')->assertForbidden();
        $this->actingAs($order->customer);
        $this->action($order, 'issues', ['body' => 'Please fix the heading'])->assertOk();
        $this->actingAs($order->provider);
        $this->action($order, 'revision')->assertOk();
        $this->action($order, 'submit', ['body' => 'Revised delivery'])->assertOk();
        $this->actingAs($order->customer);
        $this->action($order, 'confirm')->assertOk()->assertJsonPath('status', 'completed');
        $this->assertNotNull($order->fresh()->completed_at);
        $this->assertDatabaseCount('messages', 6);
        $this->assertDatabaseCount('notifications', 6);
        $this->assertDatabaseHas('messages', ['message' => 'First delivery', 'workflow_kind' => 'submit']);
        $this->action($order, 'message', ['body' => 'Late', 'token' => (string) Str::uuid()])->assertConflict();
    }

    public function test_decline_keeps_order_and_reason_and_rejects_stale_accept(): void
    {
        $order = $this->order();
        $this->actingAs($order->provider);
        $this->action($order, 'decline', ['body' => 'Unavailable this week'])->assertOk();
        $this->action($order, 'accept')->assertConflict();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'declined']);
        $this->assertDatabaseHas('messages', ['message' => 'Unavailable this week', 'workflow_kind' => 'decline']);
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_participants_and_roles_are_enforced_on_every_endpoint(): void
    {
        $order = $this->order();
        $this->getJson(route('user.workflow.history', $order))->assertUnauthorized();
        $this->actingAs(User::factory()->create());
        $this->getJson(route('user.workflow.show', $order))->assertForbidden();
        $this->getJson(route('user.workflow.history', $order))->assertForbidden();
        $this->getJson(route('user.workflow.download', [$order, 1]))->assertForbidden();
        $this->postJson(route('user.workflow.read', $order), ['receipt' => 'forged'])->assertForbidden();
        $this->action($order, 'accept')->assertForbidden();
        $this->action($order, 'message', ['body' => 'forged', 'token' => (string) Str::uuid()])->assertForbidden();
        $this->actingAs($order->customer);
        $this->action($order, 'accept')->assertForbidden();
        $this->action($order, 'submit', ['body' => 'forged'])->assertForbidden();
        $this->patchJson(route('user.orders.update', $order), ['status' => 'completed'])->assertConflict();
        $this->assertDatabaseCount('messages', 0);
    }

    public function test_two_way_messages_retry_tokens_and_read_receipts_do_not_clear_later_messages(): void
    {
        $order = $this->order();
        $token = (string) Str::uuid();
        $this->actingAs($order->customer);
        $this->action($order, 'message', ['body' => 'Hello', 'token' => $token, 'sender_id' => $order->provider_id])->assertOk();
        $this->action($order, 'message', ['body' => 'Hello', 'token' => $token])->assertOk();
        $this->assertDatabaseCount('messages', 1);
        $this->assertDatabaseHas('messages', ['sender_id' => $order->customer_id]);
        $this->assertSame(1, (int) OrderWorkflow::unread($order->provider_id)[$order->id]);
        $this->assertSame([], OrderWorkflow::unread($order->customer_id));
        $this->actingAs($order->provider);
        $page = $this->getJson(route('user.workflow.history', $order))->assertOk()->json();
        $this->actingAs($order->customer);
        $this->action($order, 'message', ['body' => 'Later', 'token' => (string) Str::uuid()])->assertOk();
        $this->actingAs($order->provider);
        $this->postJson(route('user.workflow.read', $order), ['receipt' => $page['receipt']])->assertOk();
        $this->assertSame(1, (int) OrderWorkflow::unread($order->provider_id)[$order->id]);
        $this->action($order, 'message', ['body' => 'Reply', 'token' => (string) Str::uuid()])->assertOk();
        $this->assertSame(1, (int) OrderWorkflow::unread($order->customer_id)[$order->id]);
        $this->assertDatabaseCount('notifications', 3);
    }

    public function test_private_attachments_are_scoped_and_invalid_files_leave_no_message(): void
    {
        Storage::fake('order_private');
        $order = $this->order();
        $this->actingAs($order->customer);
        $this->action($order, 'message', ['token' => (string) Str::uuid(), 'attachments' => [UploadedFile::fake()->createWithContent('notes.txt', 'Service details')]])->assertOk();
        $attachment = DB::table('order_attachments')->first();
        Storage::disk('order_private')->assertExists($attachment->path);
        $this->actingAs($order->provider)->get(route('user.workflow.download', [$order, $attachment->id]))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $other = $this->order();
        $this->actingAs($other->provider)->get(route('user.workflow.download', [$other, $attachment->id]))->assertNotFound();
        $this->actingAs($order->customer);
        $html = UploadedFile::fake()->createWithContent('bad.txt', '<html><script>alert(1)</script></html>');
        $actualUpload = new UploadedFile($html->getPathname(), 'bad.txt', 'text/plain', null, true);
        $this->action($order, 'message', ['token' => (string) Str::uuid(), 'attachments' => [$actualUpload]])->assertUnprocessable();
        $this->action($order, 'message', ['token' => (string) Str::uuid(), 'attachments' => [UploadedFile::fake()->create('big.txt', 5121, 'text/plain')]])->assertUnprocessable();
        $this->assertDatabaseCount('messages', 1);
    }

    public function test_empty_notes_confirmation_and_message_limits_are_validated(): void
    {
        $order = $this->order('in_progress');
        $this->actingAs($order->provider);
        $this->action($order, 'submit', ['body' => '   '])->assertUnprocessable();
        $this->action($order, 'message', ['body' => str_repeat('a', 5001), 'token' => (string) Str::uuid()])->assertUnprocessable();
        $this->action($order, 'message', ['body' => ' ', 'token' => (string) Str::uuid()])->assertUnprocessable();
        $pending = $this->order();
        $this->actingAs($pending->provider)->postJson(route('user.workflow.write', [$pending, 'accept']))->assertUnprocessable();
        $this->assertDatabaseCount('messages', 0);
    }

    public function test_pages_render_shared_controls_and_escaped_context(): void
    {
        $order = $this->order();
        $order->update(['brief' => '<script>alert(1)</script>']);
        $this->actingAs($order->provider);
        $this->get(route('user.orders.index'))->assertOk()->assertSee('Accept')->assertSee('Decline');
        $this->get(route('user.dashboarduser'))->assertOk()->assertSee('Accept')->assertSee('Decline');
        $this->get(route('user.workflow.show', $order))->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->actingAs($order->customer)->get(route('myorders.index'))->assertOk()->assertSee('Message Provider');
        $this->get(route('user.chat.index'))->assertOk()->assertSee($order->order_number);
    }

    public function test_history_is_bounded_and_two_orders_do_not_share_a_thread(): void
    {
        $order = $this->order();
        $conversation = Conservation::create(['order_id' => $order->id]);
        for ($i = 0; $i < 55; $i++) {
            $conversation->messages()->create(['sender_id' => $order->customer_id, 'message' => 'Message '.$i]);
        }
        $other = $this->order();
        $other->update(['customer_id' => $order->customer_id, 'provider_id' => $order->provider_id]);
        $this->actingAs($order->provider);
        $page = $this->getJson(route('user.workflow.history', $order))->assertOk()->assertJsonCount(50, 'messages')->json();
        $this->getJson(route('user.workflow.history', $order).'?after='.$page['cursor'])->assertJsonCount(5, 'messages');
        $this->getJson(route('user.workflow.history', $other))->assertJsonCount(0, 'messages');
        $this->postJson(route('user.workflow.read', $other), ['receipt' => $page['receipt']])->assertForbidden();
    }

    public function test_cancellation_cannot_overwrite_an_accepted_order(): void
    {
        $order = $this->order();
        $this->actingAs($order->provider);
        $this->action($order, 'accept')->assertOk();
        $this->actingAs($order->customer)->patchJson(route('myorders.cancel', $order))->assertConflict();
        $this->assertSame('in_progress', $order->fresh()->status);
    }

    public function test_legacy_statuses_remain_unchanged_and_have_no_new_transitions(): void
    {
        foreach (['accepted', 'cancelled', 'disputed', 'completed'] as $status) {
            $order = $this->order($status);
            $this->actingAs($order->provider);
            $this->action($order, 'accept')->assertConflict();
            $this->assertSame($status, $order->fresh()->status);
        }
        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_uploads_and_transition_roll_back_when_notification_write_fails(): void
    {
        Storage::fake('order_private');
        $order = $this->order('in_progress');
        DB::unprepared("CREATE TRIGGER reject_notification BEFORE INSERT ON notifications BEGIN SELECT RAISE(ABORT, 'test failure'); END");
        $this->actingAs($order->provider);
        $this->action($order, 'submit', ['body' => 'Done', 'attachments' => [UploadedFile::fake()->createWithContent('result.txt', 'Result')]])->assertStatus(500);
        $this->assertSame('in_progress', $order->fresh()->status);
        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('order_attachments', 0);
        $this->assertSame([], Storage::disk('order_private')->allFiles());
    }

    public function test_migration_preserves_legacy_statuses_prices_and_existing_chat(): void
    {
        $ids = [];
        foreach (['accepted', 'submitted', 'completed', 'cancelled', 'disputed'] as $status) {
            $order = $this->order($status);
            $ids[$order->id] = [$status, $order->total_amount];
        }
        $conversation = Conservation::create(['order_id' => $order->id]);
        $conversation->messages()->create(['sender_id' => $order->customer_id, 'message' => 'Legacy history']);
        (require database_path('migrations/2026_09_23_000001_add_order_workflow.php'))->up();
        foreach ($ids as $id => [$status, $price]) {
            $saved = Order::findOrFail($id);
            $this->assertSame($status, $saved->status);
            $this->assertSame(number_format((float) $price, 2, '.', ''), number_format((float) $saved->total_amount, 2, '.', ''));
        }
        $this->assertDatabaseHas('messages', ['message' => 'Legacy history', 'conservation_id' => $conversation->id]);
    }
}
