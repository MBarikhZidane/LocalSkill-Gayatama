<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        return view('users.reviews', [
            'eligibleOrders' => $request->user()->customerOrders()->with('service')
                ->where('status', 'completed')->whereNotNull('service_id')
                ->whereNotIn('id', Review::select('order_id')->where('reviewer_id', $request->user()->id))
                ->latest()->get(),
            'writtenReviews' => Review::with(['reviewer', 'reviewee', 'order.service'])->where('reviewer_id', $request->user()->id)->latest()->get(),
            'receivedReviews' => Review::with(['reviewer', 'order.service'])->where('reviewee_id', $request->user()->id)->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);
        DB::transaction(function () use ($request, $validated): void {
            $order = Order::where('customer_id', $request->user()->id)->lockForUpdate()->findOrFail($validated['order_id']);
            if ($order->status !== 'completed' || ! $order->service_id) {
                throw ValidationException::withMessages(['order_id' => 'Choose a completed purchase.']);
            }
            if (Review::where('order_id', $order->id)->where('reviewer_id', $request->user()->id)->exists()) {
                throw ValidationException::withMessages(['order_id' => 'You have already reviewed this purchase.']);
            }
            Review::create([
                'order_id' => $order->id,
                'reviewer_id' => $request->user()->id,
                'reviewee_id' => $order->provider_id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]);
        });

        return back()->with('success', 'Your review has been published.');
    }
}
