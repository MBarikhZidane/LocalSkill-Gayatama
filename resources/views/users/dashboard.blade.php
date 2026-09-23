@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="space-y-6">
        {{-- Header & Welcoming Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    My Dashboard
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Welcome back, <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $user->name }}</span>!
                </p>
            </div>
            <span class="badge border-emerald-200 bg-emerald-50 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800">
                {{ $user->email }}
            </span>
        </div>

        {{-- 4 User Summary Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Incoming Orders --}}
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Incoming Orders</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                        {{ number_format($totalIncomingOrders, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <i data-lucide="inbox" class="w-6 h-6"></i>
                </div>
            </div>

            {{-- Services Offered --}}
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Services</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                        {{ number_format($totalMyServices, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                </div>
            </div>

            {{-- Total Skills --}}
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Skills</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                        {{ number_format($totalCustomerOrders, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <i data-lucide="layers" class="w-6 h-6"></i>
                </div>
            </div>

            {{-- Portfolio --}}
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Portfolio</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                        {{ number_format($totalPortfolios, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i data-lucide="briefcase" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        {{-- Incoming Orders Table --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 dark:border-slate-800 shadow-sm">
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        Recent Incoming Orders
                    </h2>
                </div>
                <a href="{{ route('user.orders.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                    View All <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 uppercase tracking-wider font-semibold text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="p-4">Order No.</th>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Service</th>
                            <th class="p-4">Total Revenue</th>
                            <th class="p-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($incomingOrders as $order)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="p-4 font-bold text-slate-900 dark:text-white">
                                    {{ $order->order_number }}
                                </td>
                                <td class="p-4">
                                    <div class="font-medium text-slate-900 dark:text-white">
                                        {{ $order->customer->name ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $order->customer->email ?? '-' }}
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="font-medium text-slate-900 dark:text-white">
                                        {{ $order->service->title ?? '-' }}
                                    </div>
                                </td>
                                <td class="p-4 font-semibold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($order->price, 0, ',', '.') }}
                                </td>
                                <td class="p-4 text-center">
                                    <p class="mb-2" data-order-status="{{ $order->id }}">{{ \App\Models\Order::statusLabel($order->status) }}</p>
                                    @include('users.orders.workflow.actions', ['order' => $order])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400 dark:text-slate-500">
                                    There are no incoming orders for your services at this time.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@include('users.orders.workflow.assets')
@endsection
