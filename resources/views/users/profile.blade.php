@extends('layouts.landingpage')

@section('title', $user->name . ' - Service Provider Profile')

@section('content')
<main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-6 sm:py-8">
    {{-- Back Button --}}
    <a href="{{ route('explore.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors">
        <i data-lucide="arrow-left" class="h-4 w-4"></i> Back
    </a>

    {{-- Success / Error Alert --}}
    @if(session('success'))
        <div class="mt-4 rounded-xl bg-emerald-100 p-4 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]">

        {{-- LEFT COLUMN: Identity Card & Profile Summary --}}
        <aside class="grid gap-6 lg:sticky lg:top-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 shadow-sm text-center">
                {{-- Avatar --}}
                <div class="mx-auto grid h-24 w-24 place-items-center rounded-full bg-slate-900 text-2xl font-bold text-white dark:bg-emerald-600 shadow-md">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>

                {{-- Name & Role --}}
                <h1 class="mt-4 text-xl font-bold text-slate-900 dark:text-white">
                    {{ $user->name }}
                </h1>
                <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                    {{ ucfirst($user->role) }}
                </p>

                {{-- Rating Summary --}}
                <div class="mt-3 flex items-center justify-center gap-1 font-semibold text-amber-500">
                    &#9733; {{ number_format($avgRating, 1) }}
                    <span class="text-xs font-normal text-slate-500 dark:text-slate-400">({{ $totalReviews }} {{ Str::plural('review', $totalReviews) }})</span>
                </div>

                {{-- Academic Information --}}
                <div class="mt-6 border-t border-slate-100 pt-4 text-left dark:border-slate-800 space-y-2">
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <i data-lucide="graduation-cap" class="h-4 w-4 text-slate-400"></i>
                        <span>{{ $user->university->name ?? 'University not specified' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <i data-lucide="book-open" class="h-4 w-4 text-slate-400"></i>
                        <span>{{ $user->studyProgram->name ?? 'Study program not specified' }}</span>
                    </div>
                    @if($user->location)
                        <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                            <i data-lucide="map-pin" class="h-4 w-4 text-slate-400"></i>
                            <span>{{ $user->location->city ?? 'Location not specified' }}</span>
                        </div>
                    @endif
                </div>

                {{-- Bio --}}
                @if($user->bio)
                    <div class="mt-4 border-t border-slate-100 pt-4 text-left dark:border-slate-800">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">About Me</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line">
                            {{ $user->bio }}
                        </p>
                    </div>
                @endif
            </section>

            {{-- Performance Summary / Statistics --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4">Service Statistics</h3>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800/50">
                        <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $completedOrdersCount }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Orders Completed</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800/50">
                        <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $user->services->count() }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Active Services</p>
                    </div>
                </div>
            </section>
        </aside>

        {{-- RIGHT COLUMN: Skills, Portfolio, Offered Services, & Reviews --}}
        <div class="grid min-w-0 gap-6">

            {{-- SECTION: Skills --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 dark:border-slate-800 dark:bg-slate-900 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="award" class="h-5 w-5 text-emerald-600 dark:text-emerald-400"></i> Skills & Expertise
                </h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    @forelse($user->skills as $skill)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/50">
                            {{ $skill->name }}
                            @if($skill->pivot->proficiency_level)
                                <span class="text-emerald-500 dark:text-emerald-400">• {{ ucfirst($skill->pivot->proficiency_level) }}</span>
                            @endif
                        </span>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">No skills added yet.</p>
                    @endforelse
                </div>
            </section>

            {{-- SECTION: Offered Services --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 dark:border-slate-800 dark:bg-slate-900 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="briefcase" class="h-5 w-5 text-emerald-600 dark:text-emerald-400"></i> Services Offered
                </h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    @forelse($user->services as $service)
                        <div class="flex flex-col justify-between rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 p-4 dark:bg-slate-800/40">
                            <div>
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ $service->category->name ?? 'Service' }}
                                </span>
                                <h3 class="mt-1 font-bold text-slate-900 dark:text-white line-clamp-1">
                                    {{ $service->title }}
                                </h3>
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 line-clamp-2">
                                    {{ $service->description }}
                                </p>
                            </div>
                            <div class="mt-4 flex items-center justify-between border-t border-slate-200/60 pt-3 dark:border-slate-700/60">
                                <span class="font-bold text-slate-900 dark:text-white">
                                    Rp{{ number_format($service->price, 0, ',', '.') }}
                                </span>
                                <a href="{{ route('services.show', $service->id) }}" class="text-xs font-semibold text-emerald-600 hover:underline dark:text-emerald-400">
                                    View Details &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-2 text-sm text-slate-500 dark:text-slate-400">This provider has not offered any services yet.</p>
                    @endforelse
                </div>
            </section>

            {{-- SECTION: Portfolio --}}
            <section id="portfolio" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 dark:border-slate-800 dark:bg-slate-900 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="folder-git-2" class="h-5 w-5 text-emerald-600 dark:text-emerald-400"></i> Portfolio & Works
                </h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    @forelse($user->portofolios as $portfolio)
                        <article class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40">
                            <div class="flex h-28 items-center justify-center bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400">
                                <i data-lucide="image" class="h-10 w-10"></i>
                            </div>
                            <div class="p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">
                                    {{ $portfolio->skill->name ?? 'Portfolio' }}
                                </p>
                                <h3 class="mt-1 font-bold text-slate-900 dark:text-white">{{ $portfolio->type ?? 'Sample Project' }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300 truncate">
                                    {{ $portfolio->evidence }}
                                </p>
                            </div>
                        </article>
                    @empty
                        <p class="col-span-2 text-sm text-slate-500 dark:text-slate-400">No portfolio items uploaded yet.</p>
                    @endforelse
                </div>
            </section>

            {{-- SECTION: Client Reviews --}}
            <section id="reviews" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 dark:border-slate-800 dark:bg-slate-900 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="message-square" class="h-5 w-5 text-emerald-600 dark:text-emerald-400"></i> Client Reviews
                </h2>

                <div class="mt-6 grid gap-5">
                    @forelse($reviews as $review)
                        <article class="border-b border-slate-100 dark:border-slate-800 pb-5 last:border-0 last:pb-0">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h3 class="font-bold text-slate-900 dark:text-white">{{ $review->reviewer->name ?? 'Anonymous' }}</h3>
                                <p class="text-sm font-semibold text-amber-500">
                                    ★ {{ number_format($review->rating, 1) }} / 5.0
                                </p>
                            </div>
                            <time class="mt-1 block text-xs text-slate-500 dark:text-slate-400">
                                {{ $review->created_at->format('d M Y') }}
                            </time>
                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                {{ $review->comment }}
                            </p>
                        </article>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">No reviews yet for this user.</p>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</main>
@endsection
