@extends('layouts.app')
@section('title', 'Order Messages')
@section('content')
<h1 class="mb-5 text-2xl font-bold">Order Messages</h1>
<div class="space-y-4">
@forelse($orders as $order)
<section class="rounded-xl border bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
<h2 class="mb-2 font-semibold">#{{ $order->order_number }} · {{ $order->service?->title ?? 'Service no longer listed' }}</h2>
<p class="mb-3" data-order-status="{{ $order->id }}">{{ \App\Models\Order::statusLabel($order->status) }}</p>
@include('users.orders.workflow.actions', ['order' => $order])
</section>
@empty<p>No orders yet.</p>@endforelse
</div>
<div class="mt-5">{{ $orders->links() }}</div>
@include('users.orders.workflow.assets')
@endsection
