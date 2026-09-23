<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Review;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class FrontendConversionTest extends TestCase
{
    use RefreshDatabase;

    private function service(User $provider, string $status = 'active'): Service
    {
        $category = SkillCategory::firstOrCreate(['name' => 'Design'], ['description' => 'Campus design']);

        return Service::create([
            'user_id' => $provider->id, 'category_id' => $category->id,
            'title' => 'Design consultation', 'description' => 'A design consultation for campus projects.',
            'price' => 150000, 'estimated_days' => 2, 'status' => $status,
        ]);
    }

    private function order(User $customer, Service $service, string $status = 'completed'): Order
    {
        return Order::create([
            'order_number' => fake()->unique()->numerify('ORD-########'),
            'customer_id' => $customer->id, 'provider_id' => $service->user_id,
            'service_id' => $service->id, 'price' => 150000, 'platform_fee' => 0,
            'total_amount' => 150000, 'status' => $status,
        ]);
    }

    #[TestWith(['/', 'Talent, closer'])]
    #[TestWith(['/campus', 'From need to done'])]
    #[TestWith(['/login', 'Welcome back'])]
    #[TestWith(['/register', 'Create your account'])]
    #[TestWith(['/explore', 'What do you need a hand with?'])]
    public function test_public_pages_render_original_sections(string $path, string $heading): void
    {
        $this->get($path)->assertSeeText($heading)->assertDontSee('href="explore.html"', false);
    }

    #[TestWith(['/user/dashboard', 'Good to see you'])]
    #[TestWith(['/myorders', 'Services I'])]
    #[TestWith(['/user/messages', 'Choose a conversation'])]
    #[TestWith(['/user/reviews', 'Reviews I'])]
    #[TestWith(['/user/skill-profile', 'Profile & Skill Level'])]
    public function test_workspace_pages_render_for_members(string $path, string $heading): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get($path)->assertSeeText($heading)->assertSee(route('user.reviews.index'), false);
    }

    #[TestWith(['/myorders'])]
    #[TestWith(['/user/messages'])]
    #[TestWith(['/user/reviews'])]
    #[TestWith(['/user/skill-profile'])]
    public function test_workspace_pages_require_login(string $path): void
    {
        $this->get($path)->assertRedirect(route('login'));
    }

    public function test_service_and_profile_render_live_data_and_escape_user_content(): void
    {
        $provider = User::factory()->create(['name' => '<script>alert(1)</script>']);
        $service = $this->service($provider);

        $this->get(route('services.show', $service))
            ->assertSeeText('Request a booking')->assertSeeText('Portfolio')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_public_profile_keeps_skills_services_portfolio_and_reviews(): void
    {
        $provider = User::factory()->create();
        $this->service($provider);

        $this->get(route('profile.viewprofile', $provider))
            ->assertSeeText('Local talent. Real possibilities.')
            ->assertSeeText('Design consultation')->assertSee('id="portfolio"', false)->assertSee('id="reviews"', false);
    }

    public function test_homepage_category_links_filter_live_services(): void
    {
        $service = $this->service(User::factory()->create());

        $this->get('/explore?category=design')
            ->assertSee(route('services.show', $service), false)
            ->assertSee('value="'.$service->category_id.'" selected', false);
    }

    public function test_rating_filter_returns_only_matching_services(): void
    {
        $service = $this->service(User::factory()->create());
        $customer = User::factory()->create();
        $order = $this->order($customer, $service);
        Review::create(['order_id' => $order->id, 'reviewer_id' => $customer->id, 'reviewee_id' => $service->user_id, 'rating' => 5, 'comment' => 'Excellent campus design.']);
        $unrated = $this->service(User::factory()->create());
        $unrated->update(['title' => 'Unrated service']);

        $this->get('/explore?min_rating=4')->assertSeeText($service->title)->assertDontSeeText('Unrated service');
    }

    public function test_booking_saves_brief_date_and_notifies_provider(): void
    {
        $this->freezeTime();
        Notification::fake([OrderStatusChanged::class]);
        $provider = User::factory()->create();
        $service = $this->service($provider);
        $customer = User::factory()->create();
        $date = now()->addDays(7)->toDateString();

        $this->actingAs($customer)->post(route('user.orders-service.store'), [
            'service_id' => $service->id, 'scheduled_date' => $date,
            'brief' => 'Please design a campus event poster.',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('orders', ['customer_id' => $customer->id, 'service_id' => $service->id, 'scheduled_date' => $date, 'brief' => 'Please design a campus event poster.']);
        Notification::assertSentTo($provider, OrderStatusChanged::class);
    }

    public function test_booking_rejects_invalid_brief_and_past_date(): void
    {
        $service = $this->service(User::factory()->create());
        $customer = User::factory()->create();

        $this->actingAs($customer)->post(route('user.orders-service.store'), [
            'service_id' => $service->id, 'scheduled_date' => '2000-01-01', 'brief' => 'short',
        ])->assertSessionHasErrors(['scheduled_date', 'brief']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_completed_purchase_can_be_reviewed_once(): void
    {
        $customer = User::factory()->create();
        $order = $this->order($customer, $this->service(User::factory()->create()));

        $this->actingAs($customer)->post(route('user.reviews.store'), [
            'order_id' => $order->id, 'rating' => 5, 'comment' => 'Very helpful design advice.',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('reviews', ['order_id' => $order->id, 'reviewer_id' => $customer->id, 'rating' => 5]);
    }

    #[TestWith(['pending'])]
    #[TestWith(['in_progress'])]
    public function test_unfinished_orders_cannot_be_reviewed(string $status): void
    {
        $customer = User::factory()->create();
        $order = $this->order($customer, $this->service(User::factory()->create()), $status);

        $this->actingAs($customer)->post(route('user.reviews.store'), [
            'order_id' => $order->id, 'rating' => 5, 'comment' => 'Very helpful design advice.',
        ])->assertSessionHasErrors('order_id');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_duplicate_review_is_rejected(): void
    {
        $customer = User::factory()->create();
        $order = $this->order($customer, $this->service(User::factory()->create()));
        Review::create(['order_id' => $order->id, 'reviewer_id' => $customer->id, 'reviewee_id' => $order->provider_id, 'rating' => 4, 'comment' => 'Original review.']);

        $this->actingAs($customer)->post(route('user.reviews.store'), ['order_id' => $order->id, 'rating' => 5, 'comment' => 'Very helpful design advice.'])
            ->assertSessionHasErrors('order_id');

        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseHas('reviews', ['order_id' => $order->id, 'rating' => 4]);
    }

    public function test_other_customers_order_cannot_be_reviewed(): void
    {
        $order = $this->order(User::factory()->create(), $this->service(User::factory()->create()));

        $this->actingAs(User::factory()->create())->post(route('user.reviews.store'), [
            'order_id' => $order->id, 'rating' => 5, 'comment' => 'Very helpful design advice.',
        ])->assertNotFound();

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_skill_profile_saves_levels_without_granting_verification(): void
    {
        $user = User::factory()->create();
        $skill = Skill::create(['name' => 'Laravel']);
        $user->skills()->attach($skill, ['proficiency_level' => 1, 'is_verified' => false]);

        $this->actingAs($user)->put(route('user.skill-profile.update'), [
            'name' => 'Campus Developer', 'professional_title' => 'Web Developer', 'bio' => 'I build campus websites.',
            'levels' => [$skill->id => 90], 'is_verified' => true, 'role' => 'admin',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Campus Developer', 'professional_title' => 'Web Developer', 'role' => 'user']);
        $this->assertDatabaseHas('user_skills', ['user_id' => $user->id, 'skill_id' => $skill->id, 'proficiency_level' => 4, 'proficiency_percent' => 90, 'is_verified' => false]);
    }

    public function test_skill_profile_rejects_unowned_skills_without_saving_profile(): void
    {
        $user = User::factory()->create(['name' => 'Original Name']);
        $skill = Skill::create(['name' => 'Private skill']);

        $this->actingAs($user)->put(route('user.skill-profile.update'), ['name' => 'Changed', 'levels' => [$skill->id => 90]])
            ->assertSessionHasErrors('levels');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Original Name']);
        $this->assertDatabaseCount('user_skills', 0);
    }

    public function test_portfolio_summary_is_saved_for_owned_skill(): void
    {
        $user = User::factory()->create();
        $skill = Skill::create(['name' => 'Design']);
        $user->skills()->attach($skill);

        $this->actingAs($user)->post(route('user.skill-profile.portfolio.store'), [
            'skill_id' => $skill->id, 'title' => 'Campus festival', 'project_type' => 'Mood board',
            'description' => 'A coordinated palette for a campus festival.',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('portofolios', ['user_id' => $user->id, 'title' => 'Campus festival', 'status' => 'pending']);
    }

    public function test_admin_dashboard_renders_review_queue(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = $this->service(User::factory()->create(), 'draft');

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertSeeText('Campus overview')->assertSeeText($service->title);
    }

    public function test_admin_moderation_saves_decision_and_note(): void
    {
        $service = $this->service(User::factory()->create(), 'draft');

        $this->actingAs(User::factory()->create(['role' => 'admin']))->post(route('admin.services.moderate', $service), [
            'decision' => 'approve', 'reason' => 'Scope and pricing checked.',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('services', ['id' => $service->id, 'status' => 'active', 'moderation_note' => 'Scope and pricing checked.']);
    }

    public function test_non_admin_cannot_moderate(): void
    {
        $user = User::factory()->create();
        $service = $this->service($user, 'draft');

        $this->actingAs($user)->post(route('admin.services.moderate', $service), ['decision' => 'approve', 'reason' => 'Approve myself.'])
            ->assertRedirect(route('explore.index'));

        $this->assertDatabaseHas('services', ['id' => $service->id, 'status' => 'draft']);
    }

    public function test_order_participant_can_send_message(): void
    {
        $customer = User::factory()->create();
        $order = $this->order($customer, $this->service(User::factory()->create()));

        $this->actingAs($customer)->get(route('myorders.index'))->assertOk()->assertSeeText($order->order_number);

        $this->actingAs($customer)->postJson('/user/chat/conversations/'.$order->id.'/send', ['message' => 'Please use green for the poster.'])
            ->assertCreated()->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('messages', ['sender_id' => $customer->id, 'message' => 'Please use green for the poster.']);
        $this->assertDatabaseHas('conservations', ['order_id' => $order->id]);
    }

    public function test_conversation_list_only_includes_users_orders(): void
    {
        $customer = User::factory()->create();
        $service = $this->service(User::factory()->create());
        $order = $this->order($customer, $service);
        $this->order(User::factory()->create(), $service);

        $this->actingAs($customer)->getJson('/user/chat/conversations')->assertJsonCount(1)->assertJsonPath('0.order_id', $order->id);
    }

    public function test_outsider_cannot_read_order_messages(): void
    {
        $order = $this->order(User::factory()->create(), $this->service(User::factory()->create()));

        $this->actingAs(User::factory()->create())->getJson('/user/chat/conversations/'.$order->id)->assertNotFound();
    }

    public function test_outsider_cannot_send_order_messages(): void
    {
        $order = $this->order(User::factory()->create(), $this->service(User::factory()->create()));

        $this->actingAs(User::factory()->create())->postJson('/user/chat/conversations/'.$order->id.'/send', ['message' => 'Unwanted message'])->assertNotFound();

        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('conservations', 0);
    }
}
