<?php

namespace App\Http\Controllers;
use App\Models\Conservation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ChatController extends Controller
{
    public function index()
    {
        return view('users.messages');
    }
    public function getConversations()
    {
        $userId = Auth::id();

        $conversations = Conservation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with(['order.service', 'order.customer', 'order.provider', 'latestMessage', 'participants'])
        ->get()
        ->map(function ($conversation) use ($userId) {
            $order = $conversation->order;
            
            $isCustomer = $order ? ($order->customer_id === $userId) : false;
            
            $otherUser = $isCustomer ? $order->provider : $order->customer;

            return [
                'id' => $conversation->id,
                'role_label' => $isCustomer ? 'Provider' : 'Customer',
                'other_user_name' => $otherUser ? $otherUser->name : 'User',
                'service_title' => $order->service->title ?? 'Layanan',
                'service_url' => $order->service_id ? route('services.show', $order->service_id) : '#',
                'order_number' => $order->order_number ?? '-',
                'last_message' => $conversation->latestMessage->message ?? 'Belum ada pesan',
                'last_message_time' => $conversation->latestMessage ? $conversation->latestMessage->created_at->diffForHumans() : '',
            ];
        });

        return response()->json($conversations);
    }

    public function getMessages($id)
    {
        $userId = Auth::id();

        $conversation = Conservation::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->with(['order.service', 'order.customer', 'order.provider', 'messages.sender'])
        ->findOrFail($id);

        $order = $conversation->order;
        $isCustomer = $order ? ($order->customer_id === $userId) : false;
        $otherUser = $isCustomer ? $order->provider : $order->customer;

        $messages = $conversation->messages->map(function ($msg) use ($userId) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->name,
                'is_me' => $msg->sender_id === $userId,
                'message' => $msg->message,
                'time' => $msg->created_at->format('H:i | d M Y'),
            ];
        });

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'role_label' => $isCustomer ? 'Provider' : 'Customer',
                'other_user_name' => $otherUser ? $otherUser->name : 'User',
                'order_number' => $order->order_number ?? '-',
                'service_title' => $order->service->title ?? 'Layanan',
                'service_url' => $order->service_id ? route('services.show', $order->service_id) : '#',
            ],
            'messages' => $messages,
        ]);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userId = Auth::id();

        $conversation = Conservation::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->findOrFail($id);

        $message = Message::create([
            'conservation_id' => $conversation->id,
            'sender_id' => $userId,
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_name' => Auth::user()->name,
                'is_me' => true,
                'message' => $message->message,
                'time' => $message->created_at->format('H:i | d M Y'),
            ],
        ]);
    }
}
