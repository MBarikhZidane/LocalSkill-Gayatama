<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Order;
use App\Services\OrderWorkflow;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderWorkflowController extends Controller
{
    public function show(Request $request, Order $order): View
    {
        OrderWorkflow::authorize($order, (int) $request->user()->id);
        $order->load(['customer', 'provider', 'service']);

        return view('users.orders.workflow.show', compact('order'));
    }

    public function history(Request $request, Order $order): JsonResponse
    {
        OrderWorkflow::authorize($order, (int) $request->user()->id);
        $request->validate(['after' => 'nullable|integer|min:0', 'before' => 'nullable|integer|min:1']);
        $query = Message::whereHas('conservation', fn ($q) => $q->where('order_id', $order->id))->with('sender:id,name');
        $before = $request->integer('before');
        if ($before) {
            $query->where('id', '<', $before)->orderByDesc('id');
        } else {
            $query->where('id', '>', $request->integer('after'))->orderBy('id');
        }
        // Start at the first unread page, not the newest page: read cursors must never skip unseen messages.
        $messages = $query->limit(50)->get();
        if ($before) {
            $messages = $messages->reverse()->values();
        }
        $attachments = DB::table('order_attachments')->whereIn('message_id', $messages->pluck('id'))->get()->groupBy('message_id');
        $max = (int) ($messages->max('id') ?? 0);
        $receipt = Crypt::encryptString(json_encode(['user' => (int) $request->user()->id, 'order' => $order->id, 'max' => $max]));

        return response()->json([
            'messages' => $messages->map(fn ($m) => ['id' => $m->id, 'sender' => $m->sender?->name ?? 'Former user', 'body' => $m->message, 'kind' => $m->workflow_kind, 'status' => $m->workflow_to ? Order::statusLabel($m->workflow_to) : null, 'time' => $m->created_at->toIso8601String(), 'attachments' => collect($attachments[$m->id] ?? [])->map(fn ($a) => ['name' => $a->name, 'url' => route('user.workflow.download', [$order, $a->id])])]),
            'cursor' => $max, 'receipt' => $receipt, 'has_more' => $messages->count() === 50,
            'status' => $order->status, 'label' => Order::statusLabel($order->status), 'writable' => OrderWorkflow::writable($order),
            'actions' => view('users.orders.workflow.actions', compact('order'))->render(),
        ])->header('Cache-Control', 'no-store');
    }

    public function write(Request $request, Order $order, string $action, OrderWorkflow $workflow): JsonResponse|RedirectResponse
    {
        OrderWorkflow::authorize($order, (int) $request->user()->id);
        abort_unless($action === 'message' || isset(OrderWorkflow::TRANSITIONS[$action]), 404);
        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:'.($action === 'decline' ? 1000 : 5000)],
            'token' => $action === 'message' ? 'required|uuid' : 'nullable|uuid',
            'confirmation' => in_array($action, ['accept', 'decline', 'confirm', 'revision', 'cancel'], true) ? 'required|accepted' : 'nullable',
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'file|max:5120|mimetypes:image/jpeg,image/png,image/webp,application/pdf,text/plain|extensions:jpg,jpeg,png,webp,pdf,txt',
        ]);
        $workflow->write($order, (int) $request->user()->id, $action, $data, $request->file('attachments', []));
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Saved.', 'status' => $order->fresh()->status]);
        }

        return redirect()->route('user.workflow.show', $order)->with('success', 'Saved.');
    }

    public function read(Request $request, Order $order): JsonResponse
    {
        OrderWorkflow::authorize($order, (int) $request->user()->id);
        $request->validate(['receipt' => 'required|string']);
        try {
            $receipt = json_decode(Crypt::decryptString($request->string('receipt')->toString()), true, flags: JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            abort(422, 'Invalid read receipt.');
        }
        abort_unless(($receipt['user'] ?? null) === (int) $request->user()->id && ($receipt['order'] ?? null) === $order->id, 403);
        DB::transaction(function () use ($order, $request, $receipt) {
            Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $key = ['order_id' => $order->id, 'user_id' => $request->user()->id];
            DB::table('order_read_cursors')->insertOrIgnore($key + ['message_id' => 0]);
            DB::table('order_read_cursors')->where($key)->where('message_id', '<', $receipt['max'])->update(['message_id' => $receipt['max']]);
        });

        return response()->json(['saved' => true]);
    }

    public function download(Request $request, Order $order, int $attachment): StreamedResponse
    {
        OrderWorkflow::authorize($order, (int) $request->user()->id);
        $file = DB::table('order_attachments as a')->join('messages as m', 'm.id', '=', 'a.message_id')->join('conservations as c', 'c.id', '=', 'm.conservation_id')->where('c.order_id', $order->id)->where('a.id', $attachment)->select('a.*')->first();
        abort_unless($file && Storage::disk('order_private')->exists($file->path), 404);

        return Storage::disk('order_private')->download($file->path, $file->name, ['Content-Type' => 'application/octet-stream', 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store']);
    }

    public function summary(Request $request): JsonResponse
    {
        $data = $request->validate(['ids' => 'nullable|array|max:50', 'ids.*' => 'integer|min:1']);
        $id = (int) $request->user()->id;
        $orders = Order::where(fn ($q) => $q->where('customer_id', $id)->orWhere('provider_id', $id))->whereIn('id', $data['ids'] ?? [])->get();
        $unread = OrderWorkflow::unread($id);

        return response()->json(['total' => array_sum($unread), 'orders' => $orders->map(fn ($order) => ['id' => $order->id, 'status' => $order->status, 'label' => Order::statusLabel($order->status), 'unread' => $unread[$order->id] ?? 0, 'actions' => view('users.orders.workflow.actions', compact('order'))->render()]), 'pending' => Order::where('provider_id', $id)->where('status', 'pending')->count(), 'completed' => Order::where('provider_id', $id)->where('status', 'completed')->count()])->header('Cache-Control', 'no-store');
    }
}
