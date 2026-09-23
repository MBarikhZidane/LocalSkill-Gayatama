<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        return view('users.messages');
    }

    private function orders(Request $request): Builder
    {
        return Order::query()->where(function (Builder $query) use ($request): void {
            $query->where('customer_id', $request->user()->id)->orWhere('provider_id', $request->user()->id);
        })->with(['service', 'customer', 'provider']);
    }

    /** @return array{id: int, order_id: int, role_label: string, other_user_name: string, service_title: string, service_url: string, order_number: string} */
    private function conversation(Order $order, int $userId): array
    {
        $isCustomer = $order->customer_id === $userId;

        return [
            'id' => $order->id,
            'order_id' => $order->id,
            'role_label' => $isCustomer ? 'Provider · Buying' : 'Customer · Providing',
            'other_user_name' => ($isCustomer ? $order->provider : $order->customer)->name,
            'service_title' => $order->service?->title ?? 'Custom Service Request',
            'service_url' => $order->service_id ? route('services.show', $order->service_id) : route('myorders.index'),
            'order_number' => $order->order_number,
        ];
    }

    public function getConversations(Request $request): JsonResponse
    {
        return response()->json($this->orders($request)->latest()->get()
            ->map(fn (Order $order): array => $this->conversation($order, $request->user()->id)));
    }

    public function getMessages(Request $request, int $id): JsonResponse
    {
        $order = $this->orders($request)->with('conservation.messages.sender')->findOrFail($id);
        $messages = $order->conservation?->messages ?? collect();

        return response()->json([
            'conversation' => $this->conversation($order, $request->user()->id),
            'messages' => $messages->map(fn (Message $message): array => [
                'id' => $message->id,
                'sender_name' => $message->sender->name,
                'is_me' => $message->sender_id === $request->user()->id,
                'message' => $message->message,
                'time' => $message->created_at->format('H:i | d M Y'),
            ]),
        ]);
    }

    public function sendMessage(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate(['message' => 'required|string|max:1000']);
        DB::transaction(function () use ($request, $id, $validated): void {
            $order = $this->orders($request)->lockForUpdate()->findOrFail($id);
            $conversation = $order->conservation()->firstOrCreate([]);
            $conversation->participants()->syncWithoutDetaching([$order->customer_id, $order->provider_id]);
            $conversation->messages()->create([
                'sender_id' => $request->user()->id,
                'message' => $validated['message'],
            ]);
        });

        return response()->json(['status' => 'success'], 201);
    }
}
