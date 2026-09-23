@extends('layouts.landingpage')
@section('title', 'My Orders | LOCALSKILL')
@section('content')
<main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:py-12">
    <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">{{ auth()->user()->name }}'s workspace</p>
    <h1 class="mt-3 text-3xl font-bold">My Orders</h1><p class="mt-3 max-w-3xl leading-7 text-slate-600">One account, both sides of the work. Track the services you buy and the services you provide.</p>
<x-workspace-nav />


    <nav aria-label="Order views" class="mb-6 flex flex-wrap gap-3">@foreach(['buying' => 'Buying', 'selling' => 'Providing'] as $value => $label)<a href="{{ route('myorders.index', ['view' => $value]) }}" class="btn {{ $view === $value ? 'border-0 bg-emerald-600 text-white' : '' }}" @if($view === $value) aria-current="page" @endif>{{ $label }}</a>@endforeach</nav>
    <div class="mb-5 flex flex-wrap items-end justify-between gap-4"><div><h2 id="orders-heading" class="text-2xl font-bold">Services I'm {{ $view === 'selling' ? 'Providing' : 'Buying' }}</h2><p id="orders-count" role="status" class="mt-2 text-sm text-slate-500">{{ $orders->total() }} orders found</p></div>
    <form id="order-filter" action="{{ route('myorders.index') }}" method="get" class="flex flex-wrap items-end gap-2"><input type="hidden" name="view" value="{{ $view }}"><label class="grid gap-2 text-sm font-semibold">Order status<select name="status" class="select select-bordered">@foreach(['all','pending','accepted','in_progress','submitted','completed','cancelled','disputed'] as $value)<option value="{{ $value }}" @selected($status === $value)>{{ ucfirst(str_replace('_', ' ', $value)) }}</option>@endforeach</select></label><label class="grid gap-2 text-sm font-semibold">Search<input name="search" value="{{ $search }}" class="input input-bordered" placeholder="Order, service, or person"></label><button class="btn" type="submit">Filter</button></form></div>
    <div id="order-list" class="grid gap-5">@foreach($orders as $order)
@php($person = $view === 'buying' ? $order->provider : $order->customer)
<article class="rounded-2xl border border-slate-200 bg-white p-6">
<div class="flex flex-wrap items-start justify-between gap-4"><div><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $order->order_number }}</p><h3 class="mt-2 text-xl font-bold">{{ $order->service?->title ?? 'Custom Service Request' }}</h3></div><span class="rounded-full bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></div>
<p class="mt-3 text-sm text-slate-600">{{ $view === 'buying' ? 'Provider' : 'Customer' }}: <a href="{{ route('profile.viewprofile', $person) }}" class="font-semibold text-emerald-700 underline">{{ $person->name }}</a></p>
<p class="mt-3 leading-6 text-slate-600">{{ $order->brief }}</p><div class="mt-4 flex flex-wrap gap-x-8 gap-y-2 text-sm"><p class="font-bold">Rp{{ number_format($order->total_amount, 0, ',', '.') }} / package</p><p>Scheduled: {{ $order->scheduled_date ?? 'To be agreed' }}</p></div>
<ol aria-label="Order progress" class="mt-5 flex flex-wrap gap-2 text-xs">@foreach(['Find', 'Match', 'Book', 'Complete', 'Review'] as $step)<li class="rounded-full px-3 py-2 {{ ($order->status === 'completed' ? $step === 'Complete' : $step === 'Book') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $loop->iteration }} {{ $step }}</li>@endforeach</ol>
<div class="mt-5 flex flex-wrap items-center gap-3 border-t border-slate-200 pt-5"><a href="{{ route('user.chat.index', ['order' => $order->id]) }}" class="btn btn-sm">Message {{ $view === 'buying' ? 'provider' : 'customer' }}</a>
@if($order->service_id)<a href="{{ route('services.show', $order->service_id) }}" class="btn btn-sm">View service</a>@endif
@if($order->status === 'completed')<a href="{{ route('user.reviews.index', ['order' => $order->id]) }}" class="btn btn-sm">Reviews</a>@else<p class="text-xs text-slate-500">Reviews unlock after completion.</p>@endif
@if($view === 'buying' && $order->status === 'pending')<form action="{{ route('myorders.cancel', $order) }}" method="POST">@csrf @method('PATCH')<button class="btn btn-error btn-sm" type="submit">Cancel Order</button></form>@endif
@if($view === 'selling')<a href="{{ route('user.orders.edit', $order) }}" class="btn btn-sm">Manage order</a>@endif
@if($person->phone)<a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $person->phone)) }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm">Chat WhatsApp</a>@endif
</div></article>@endforeach<div class="mt-6">{{ $orders->links() }}</div></div>
    <div id="orders-empty" @if($orders->isNotEmpty()) hidden @endif class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center"><h3 class="text-xl font-bold">No orders with this status</h3><p class="mt-2 text-slate-600">Try a different filter to see your orders.</p><a id="orders-clear" href="{{ route('myorders.index', ['view' => $view]) }}" class="btn mt-5">Show all statuses</a></div>

  </main>
@endsection
