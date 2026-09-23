@extends('layouts.app')
@section('title', 'Order Conversation')
@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    <a href="{{ route('myorders.index') }}" class="text-emerald-600 underline dark:text-emerald-400">My Orders</a>
    <section class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
        <h1 class="text-2xl font-bold">#{{ $order->order_number }}</h1>
        <h2 class="my-2 text-lg">{{ $order->service?->title ?? 'Service no longer listed' }}</h2>
        <p class="text-sm">Customer: {{ $order->customer?->name ?? 'Former user' }} · Provider: {{ $order->provider?->name ?? 'Former user' }}</p>
        <p class="my-2">Agreed total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
        @if($order->brief)<p class="my-2 whitespace-pre-wrap">Requirements: {{ $order->brief }}</p>@endif
        @if($order->scheduled_date)<p>Scheduled date: {{ $order->scheduled_date }}</p>@endif
        <p class="my-3 font-semibold" data-order-status="{{ $order->id }}">{{ \App\Models\Order::statusLabel($order->status) }}</p>
        @include('users.orders.workflow.actions', ['order' => $order])
        @if(in_array($order->status, ['accepted', 'cancelled', 'disputed']))<p class="mt-3 text-sm">This legacy status is preserved. The conversation is read-only.</p>@endif
        @if($order->status === 'completed' && (int) $order->customer_id === (int) auth()->id() && $order->service_id)
            <a class="mt-3 inline-block underline" href="{{ route('services.show', $order->service_id) }}">View service / Leave a Review</a>
        @endif
    </section>
    <section id="order-conversation" data-history-url="{{ route('user.workflow.history', $order) }}" data-read-url="{{ route('user.workflow.read', $order) }}" data-order-id="{{ $order->id }}" class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
        <h2 class="mb-4 text-lg font-bold">Conversation &amp; activity</h2>
        <p id="conversation-error" role="alert" class="text-rose-600 dark:text-rose-400"></p>
        <div id="conversation-messages" class="max-h-[32rem] space-y-3 overflow-y-auto" aria-live="polite"></div>
        <button id="conversation-more" type="button" class="btn btn-sm my-3" hidden>Load next messages</button>
        <p id="conversation-readonly" class="my-3 text-sm" hidden>This conversation is read-only.</p>
        <form id="conversation-form" @if(!\App\Services\OrderWorkflow::writable($order)) hidden @endif action="{{ route('user.workflow.write', [$order, 'message']) }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-3">
            @csrf
            <input type="hidden" name="token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
            <label class="block">Message<textarea name="body" maxlength="5000" rows="4" class="textarea textarea-bordered mt-2 w-full bg-transparent" placeholder="Write to the other participant…"></textarea></label>
            <label class="block text-sm">Attachments: JPEG, PNG, WebP, PDF or text. Up to 3 files, 5 MiB each.<input class="mt-2 block w-full" type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.txt"></label>
            <p data-form-error role="alert" class="text-rose-600 dark:text-rose-400"></p>
            <button type="submit" class="btn bg-emerald-600 text-white">Send message</button>
        </form>
    </section>
</div>
@include('users.orders.workflow.assets')
@endsection
