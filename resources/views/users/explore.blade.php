@extends('layouts.landingpage')

@section('title', 'Explore Skills & Services | LOCALSKILL')

@section('content')
<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="mb-2 text-sm font-semibold text-emerald-700">YOUR CAMPUS, YOUR PEOPLE</p>
        <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">What do you need a hand with?</h1>
        <p class="mt-3 text-slate-600">Compare nearby student talent and find your match.</p>
    </div>
    <span class="badge badge-outline">Available Services</span>
</div>

{{-- Memeriksa apakah ada filter lanjutan yang aktif --}}
@php
    $hasAdvancedFilters = request()->anyFilled(['category_id', 'location', 'min_price', 'max_price', 'min_rating', 'sort']);
@endphp

<div x-data="{ showFilters: {{ $hasAdvancedFilters ? 'true' : 'false' }} }" class="mb-6 rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 dark:border-slate-800 p-6 shadow-sm">
    <form action="{{ route('explore.index') }}" method="GET">
        {{-- Baris Utama: Search Input + Tombol Toggle Filter + Tombol Search --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-700 dark:text-slate-300"></i>
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Try slides, tutoring, design..."
                    class="input input-bordered w-full pl-10 focus:border-emerald-500 focus:outline-none"
                >
            </div>

            <div class="flex items-center gap-2">
                {{-- Tombol Toggle Filter Lanjutan --}}
                <button
                    type="button"
                    @click="showFilters = !showFilters"
                    class="btn border-slate-300 bg-white dark:bg-slate-900 dark:bg-slate-900 hover:bg-slate-50 flex items-center gap-2 text-slate-700 dark:text-slate-300"
                    :class="{ 'border-emerald-600 text-emerald-700 bg-emerald-50': showFilters || {{ $hasAdvancedFilters ? 'true' : 'false' }} }"
                >
                    <i data-lucide="sliders-horizontal" class="h-4 w-4"></i>
                    <span>Filters</span>
                    @if($hasAdvancedFilters)
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-600"></span>
                    @endif
                </button>

                {{-- Tombol Cari Utama --}}
                <button type="submit" class="btn bg-emerald-600 text-white hover:bg-emerald-700 flex-1 sm:flex-none">
                    Search
                </button>
            </div>
        </div>

        {{-- Section Filter Lanjutan --}}
        <div
            x-show="showFilters"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="mt-6 border-t border-slate-100 pt-6"
        >
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Kategori --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Category</label>
                    <select name="category_id" class="select select-bordered w-full text-sm">
                        <option value="">All categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Lokasi / Kampus --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Location / Campus</label>
                    <input type="text" name="location" value="{{ request('location') }}" placeholder="e.g. Library, Building A, Surabaya" class="input input-bordered w-full text-sm">
                </div>

                {{-- Minimum Price --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Min Price (Rp)</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="10000" class="input input-bordered w-full text-sm">
                </div>

                {{-- Maximum Price --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Max Price (Rp)</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="500000" class="input input-bordered w-full text-sm">
                </div>

                {{-- Rating Minimal --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Min Rating</label>
                    <select name="min_rating" class="select select-bordered w-full text-sm">
                        <option value="">Any Rating</option>
                        <option value="4.5" {{ request('min_rating') == '4.5' ? 'selected' : '' }}>★ 4.5 & above</option>
                        <option value="4.0" {{ request('min_rating') == '4.0' ? 'selected' : '' }}>★ 4.0 & above</option>
                        <option value="3.0" {{ request('min_rating') == '3.0' ? 'selected' : '' }}>★ 3.0 & above</option>
                    </select>
                </div>

                {{-- Sort / Urutan --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Sort By</label>
                    <select name="sort" class="select select-bordered w-full text-sm">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Ordered</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rating</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ route('explore.index') }}" class="btn btn-ghost btn-sm text-slate-500 hover:text-slate-700 dark:text-slate-300">
                    Reset
                </a>
                <button type="submit" class="btn bg-emerald-600 text-white hover:bg-emerald-700 btn-sm px-6">
                    Apply Filters
                </button>
            </div>
        </div>
    </form>
</div>

<div class="mb-4 flex justify-between gap-4 text-sm">
    <p role="status" class="text-slate-600"><span class="font-bold text-slate-800">{{ $services->total() }}</span> services found</p>
    @if(request()->anyFilled(['q', 'category_id', 'location', 'min_price', 'max_price', 'min_rating', 'sort']))
        <a href="{{ route('explore.index') }}" class="font-semibold text-emerald-700 hover:underline">Clear filters</a>
    @endif
</div>

{{-- Grid Layanan --}}
@if($services->count() > 0)
<section aria-label="Available skills" class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
    @foreach($services as $service)
        <!-- Ganti article menjadi tag <a> -->
        <a href="{{ route('services.show', $service->id) }}" class="group block rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 p-6 flex flex-col gap-4 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <i data-lucide="sparkles" class="h-8 w-8 text-emerald-600"></i>
            </div>

            <h2 class="text-xl font-bold group-hover:text-emerald-700">
                {{ $service->title }}
            </h2>

            <p class="text-sm leading-6 text-slate-600 line-clamp-2">{{ $service->description }}</p>

            <div class="mt-auto flex items-center gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-slate-900 text-sm font-bold text-white">
                    {{ strtoupper(substr($service->user->name ?? 'U', 0, 2)) }}
                </span>
                <div>
                    <p class="font-semibold text-sm">{{ $service->user->name ?? 'Anonymous' }}</p>
                    <p class="text-xs text-slate-500">
                        {{ $service->user->location->address ?? $service->user->university->name ?? 'Campus Zone' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-between text-sm font-medium text-slate-700 dark:text-slate-300">
                <span>★ {{ number_format($service->average_rating ?? 0, 1) }} ({{ $service->reviews_count ?? 0 }} reviews)</span>
                <span class="text-xs text-slate-500">{{ $service->completed_orders_count ?? 0 }} completed orders</span>
            </div>

            <div class="flex items-center justify-between gap-2 border-t pt-4">
                <p class="font-bold">
                    Rp {{ number_format($service->price, 0, ',', '.') }}
                    <span class="block text-xs font-normal text-slate-500">per package</span>
                </p>
            </div>
        </a>
    @endforeach
</section>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $services->links() }}
    </div>
@else
    <div class="py-16 text-center rounded-2xl border border-dashed border-slate-200 bg-white dark:bg-slate-900">
        <h2 class="text-xl font-bold text-slate-700 dark:text-slate-300">No skills found</h2>
        <p class="mt-2 text-slate-500">Try adjusting your search keywords or clearing your filters.</p>
        <a href="{{ route('explore.index') }}" class="btn border-0 bg-emerald-600 text-white hover:bg-emerald-700 mt-5 btn-sm">
            Show all skills
        </a>
    </div>
@endif
@endsection
