@extends('layouts.app')
@section('title', 'Edit Order')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header Section --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Edit Order Status #{{ $order->order_number }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Order processing status updates for customers.</p>
        </div>
        <a href="{{ route('user.orders.index') }}" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 border-0 flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back</span>
        </a>
    </div>

    {{-- Detail Info Card --}}
    <div class="mb-6 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 text-xs grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <span class="text-slate-400 block font-semibold uppercase tracking-wider">Customer</span>
            <span class="text-slate-900 dark:text-white font-bold text-sm">{{ $order->customer->name ?? '-' }}</span>
        </div>
        <div>
            <span class="text-slate-400 block font-semibold uppercase tracking-wider">Service</span>
            <span class="text-slate-900 dark:text-white font-bold text-sm">{{ $order->service->title ?? '-' }}</span>
        </div>
        <div>
            <span class="text-slate-400 block font-semibold uppercase tracking-wider">Total payment</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-bold text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Alert Validation Errors --}}
    @if ($errors->any())
        <div class="mb-6 bg-rose-50 dark:bg-rose-950/30 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 p-4 rounded-2xl text-xs shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400"></i>
                <span>An input error occurred:</span>
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('user.orders.update', $order->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Select Status --}}
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Order Status <span class="text-rose-500">*</span>
                </label>
                <select name="status" id="status" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ old('status', $order->status) == $st ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $st)) }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('user.orders.index') }}" class="btn btn-sm bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border-0">
                    Cancel
                </a>
                <button type="submit" class="btn btn-sm bg-emerald-600 text-white hover:bg-emerald-700 border-0 flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
