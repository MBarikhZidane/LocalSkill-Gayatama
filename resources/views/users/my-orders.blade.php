@extends('layouts.landingpage')

@section('title', 'My Orders | LOCALSKILL')

@section('content')
<main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-6 sm:pb-8 sm:pt-2 transition-colors duration-200">

<a href="{{ route('explore.index') }}"
            class="mb-5 inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Back
        </a>
    {{-- Toast Notification --}}
    @if(session('success'))
        <div class="alert alert-success mb-6 shadow-lg text-white">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error mb-6 shadow-lg text-white">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
        {{ Auth::user()->name }}'s workspace &middot; Active User
    </p>

    <h1 class="mt-3 text-3xl font-extrabold text-slate-900 dark:text-white">My Orders</h1>
    <p class="mt-2 mb-2 max-w-3xl text-sm sm:text-base text-slate-600 dark:text-slate-400">
        One account, both sides of the work. Track the services you buy and the services you provide.
    </p>

    {{-- Filter and Search Form --}}
    <div class="mb-6 rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/60 dark:border dark:border-slate-700">
        <form action="{{ route('myorders.index') }}" method="GET" class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <input type="hidden" name="view" value="{{ $view }}" />

            {{-- Search Bar --}}
            <div class="w-full md:w-1/2">
                <label for="search" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                    Search Orders
                </label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Search by Order ID, Service title, or Person..."
                           class="input input-bordered w-full pl-10 dark:bg-slate-900 dark:text-white dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    <svg class="absolute left-3 top-3 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Status Filter & Actions --}}
            <div class="flex items-center gap-2">
                <div class="w-full md:w-auto">
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                        Status
                    </label>
                    <select id="status" name="status" class="select select-bordered w-full dark:bg-slate-900 dark:text-white dark:border-slate-700">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ $status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="submitted" {{ $status === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="disputed" {{ $status === 'disputed' ? 'selected' : '' }}>Disputed</option>
                    </select>
                </div>

                <button class="btn btn-emerald text-white bg-emerald-600 hover:bg-emerald-700 self-end" type="submit">
                    Filter
                </button>

                @if(request('search') || request('status') !== 'all')
                    <a href="{{ route('myorders.index', ['view' => $view]) }}" class="btn btn-ghost self-end dark:text-slate-300">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Order List --}}
    @if($orders->count() > 0)
        <div class="grid gap-4">
            @foreach($orders as $order)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        {{-- Info Pesanan --}}
                        <div class="space-y-1">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-mono font-semibold text-slate-500 dark:text-slate-400">
                                    #{{ $order->order_number }}
                                </span>

                                {{-- Status Badge --}}
                                @php
                                    $badgeClasses = [
                                        'pending'     => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                        'accepted'    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                        'in_progress' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
                                        'submitted'   => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
                                        'completed'   => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                                        'cancelled'   => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
                                        'disputed'    => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                    ][$order->status] ?? 'bg-slate-100 text-slate-800';
                                @endphp
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize {{ $badgeClasses }}">
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                {{ $order->service->title ?? 'Custom Service Request' }}
                            </h3>

                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                @if($view === 'buying')
                                    Provider: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $order->provider->name }}</span>
                                @else
                                    Customer: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $order->customer->name }}</span>
                                @endif
                                &middot; {{ $order->created_at->format('d M Y') }}
                            </p>
                        </div>

                        {{-- Harga & Aksi --}}
                        <div class="flex flex-col items-start gap-3 sm:items-end">
                            <span class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>

                            <div class="flex items-center gap-2">
                                {{-- Tombol Batalkan jika status masih 'pending' dan user adalah pembeli --}}
                                @if($view === 'buying' && $order->status === 'pending')
                                    <form action="{{ route('myorders.cancel', $order->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-error btn-sm text-white">
                                            Cancel Order
                                        </button>
                                    </form>
                                @endif

                                @php
    $phone = preg_replace('/^0/', '62', $order->provider->phone ?? '');

    // Pesan otomatis
    $message = "Halo " . ($order->provider->name ?? '') . ", saya ingin bertanya terkait pesanan #" . $order->order_number;
@endphp

<a href="https://wa.me/{{ $phone }}?text={{ urlencode($message) }}"
   target="_blank"
   rel="noopener noreferrer"
   class="btn btn-outline btn-sm dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">
    Chat WhatsApp
</a>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-slate-700 dark:bg-slate-800">
            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">No orders found</h3>
            <p class="mt-1 text-slate-600 dark:text-slate-400">
                We couldn't find any orders matching your search or status filter.
            </p>
            <a href="{{ route('myorders.index', ['view' => $view]) }}" class="btn btn-emerald mt-5 text-white bg-emerald-600 hover:bg-emerald-700">
                Clear Filters
            </a>
        </div>
    @endif

</main>
@endsection
