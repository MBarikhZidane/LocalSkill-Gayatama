<?php

namespace App\Http\Controllers;

use App\Models\Conservation;
use App\Models\Order;
use App\Services\OrderWorkflow;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with(['service'])->where(fn ($q) => $q->where('customer_id', $request->user()->id)->orWhere('provider_id', $request->user()->id))->latest('id')->paginate(20);

        return view('users.orders.workflow.inbox', compact('orders'));
    }

    public function getConversations(Request $request): JsonResponse
    {
        $orders = Order::where(fn ($q) => $q->where('customer_id', $request->user()->id)->orWhere('provider_id', $request->user()->id))->latest('id')->paginate(20);

        return response()->json($orders->through(fn ($o) => ['order_id' => $o->id, 'url' => route('user.workflow.show', $o)]));
    }

    public function getMessages(Request $request, int $id, OrderWorkflowController $controller): JsonResponse
    {
        $conversation = Conservation::findOrFail($id);
        abort_unless($conversation->order, 404);

        return $controller->history($request, $conversation->order);
    }

    public function sendMessage(Request $request, int $id, OrderWorkflowController $controller, OrderWorkflow $workflow): mixed
    {
        $conversation = Conservation::findOrFail($id);
        abort_unless($conversation->order, 404);
        $request->merge(['body' => $request->input('body', $request->input('message'))]);

        return $controller->write($request, $conversation->order, 'message', $workflow);
    }
}
