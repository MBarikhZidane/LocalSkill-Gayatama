@extends('layouts.landingpage')

@section('title', $service->title . ' - LOCALSKILL')

@section('content')
    <main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-2 sm:py-3">
        {{-- Tombol Kembali --}}
        <a href="{{ route('explore.index') }}"
            class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Back
        </a>

        {{-- Notifikasi / Alert --}}
        @if(session('success'))
            <div
                class="mt-4 rounded-xl bg-emerald-100 p-4 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div
                class="mt-4 rounded-xl bg-rose-100 p-4 text-rose-800 dark:bg-rose-950 dark:text-rose-200 border border-rose-300 dark:border-rose-800">
                {{ session('error') }}
            </div>
        @endif

        <div id="service-detail" class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">

            {{-- KOLOM KIRI: Informasi Service, Skills Provider, Portfolio & Reviews --}}
            <div class="grid min-w-0 gap-6">

                {{-- Section Utama Detail Service --}}
                <section
                    class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 dark:border-slate-800 dark:bg-slate-900 shadow-sm transition-colors duration-200">
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/50 dark:text-emerald-300">
                        <i data-lucide="shield-check" class="h-3.5 w-3.5"></i>
                        {{ $service->category->name ?? 'Campus Service' }}
                    </span>

                    <h1 id="service-title" class="mt-5 text-2xl font-bold sm:text-3xl text-slate-900 dark:text-white">
                        {{ $service->title }}
                    </h1>

                    {{-- Provider Bio / Profile Card --}}
                    <div class="my-6 flex items-center gap-3">
                        <div id="provider-avatar"
                            class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-slate-900 font-bold text-white dark:bg-emerald-600">
                            {{ strtoupper(substr($service->user->name ?? 'U', 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Service Provider
                            </p>
                            <a id="provider-link" href="{{ route('profile.viewprofile', $service->user->id) }}"
                                class="font-semibold text-emerald-600 hover:underline dark:text-emerald-400">
                                <span id="provider-name">{{ $service->user->name }}</span> &rarr;
                            </a>
                        </div>
                    </div>

                    {{-- Rating Summary --}}
                    <a href="#reviews" id="provider-rating"
                        class="inline-flex items-center gap-1 font-semibold text-amber-500">
                        &#9733; {{ number_format($avgRating, 1) }}
                        <span class="text-slate-500 dark:text-slate-400 text-sm font-normal">({{ $totalReviews }}
                            ulasan)</span>
                    </a>

                    {{-- Deskripsi Service --}}
                    <h2 class="mt-8 text-xl font-bold text-slate-900 dark:text-white">Service description</h2>
                    <p id="service-description"
                        class="mt-3 leading-7 text-slate-600 dark:text-slate-300 whitespace-pre-line">
                        {{ $service->description }}
                    </p>

                    {{-- Detail Tambahan --}}
                    <dl
                        class="mt-6 grid gap-5 rounded-xl bg-slate-50 p-5 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <div>
                            <dt class="font-bold text-slate-900 dark:text-white">Estimated Completion Time</dt>
                            <dd id="service-estimate" class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                {{ $service->estimated_days ?? '1-3' }} Day.
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Section Reviews & Form Komentar --}}
                <section id="reviews"
                    class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 dark:border-slate-800 dark:bg-slate-900 shadow-sm transition-colors duration-200">
                    <h2 id="reviews-title" class="text-xl font-bold text-slate-900 dark:text-white">Reviews and Comments
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Reviews from users who have booked this service. </p>

                    {{-- FORM UPLOAD COMMENT / REVIEW --}}
                    <div
                        class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/50">
                        @auth
                            @if($canReview)
                                <h3 class="text-md font-bold text-slate-900 dark:text-white mb-3">Write Your Review</h3>
                                <form action="{{ route('user.services.reviews.store', $service->id) }}" method="POST"
                                    class="grid gap-4">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $userOrder->id }}">

                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Rating</label>
                                        <select name="rating"
                                            class="select select-bordered w-full max-w-xs bg-white dark:bg-slate-900 text-slate-900 dark:text-white border-slate-300 dark:border-slate-700"
                                            required>
                                            <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                            <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                            <option value="3">⭐⭐⭐ (3/5)</option>
                                            <option value="2">⭐⭐ (2/5)</option>
                                            <option value="1">⭐ (1/5)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Comments
                                            / Reviews</label>
                                        <textarea name="comment" rows="3" required
                                            placeholder="Bagikan pengalaman Anda menggunakan jasa ini..."
                                            class="textarea textarea-bordered w-full bg-white dark:bg-slate-900 text-slate-900 dark:text-white border-slate-300 dark:border-slate-700"></textarea>
                                    </div>

                                    <div>
                                        <button type="submit" class="btn bg-emerald-600 hover:bg-emerald-700 text-white border-0">
                                            Submit Review
                                        </button>
                                    </div>
                                </form>
                            @else
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    <i data-lucide="info" class="inline-block h-4 w-4 mr-1"></i>
                                    You can only leave a review if you have previously booked this service and have not yet
                                    submitted a review.
                                </p>
                            @endif
                        @else
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                Please <a href="{{ route('login') }}"
                                    class="font-semibold text-emerald-600 dark:text-emerald-400 underline">Log in</a> to leave a
                                review.
                            </p>
                        @endauth
                    </div>

                    {{-- LIST REVIEWS --}}
                    <div id="service-reviews" class="mt-6 grid gap-5">
                        @forelse($service->reviews as $review)
                            <article class="border-t border-slate-200 dark:border-slate-800 pt-5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <h3 class="font-bold text-slate-900 dark:text-white">
                                        {{ $review->reviewer->name ?? 'Anonim' }}
                                    </h3>
                                    <p class="text-sm font-semibold text-amber-500">
                                        ★ {{ number_format($review->rating, 1) }} / 5.0
                                    </p>
                                </div>
                                <time datetime="{{ $review->created_at }}"
                                    class="mt-1 block text-xs text-slate-500 dark:text-slate-400">
                                    {{ $review->created_at->format('d M Y') }}
                                </time>
                                <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                    {{ $review->comment }}
                                </p>
                            </article>
                        @empty
                            <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">There are no reviews for this service
                                yet.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- KOLOM KANAN: Booking Form Card (Sticky) --}}
            <aside class="grid min-w-0 gap-6 lg:sticky lg:top-6" aria-label="Booking">
                <section id="booking"
                    class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 dark:border-slate-800 dark:bg-slate-900 shadow-sm transition-colors duration-200">
                    <h2 id="booking-title" class="text-xl font-bold text-slate-900 dark:text-white">
                        Book Service
                    </h2>
                    <p class="my-5">
                        <strong id="service-price" class="text-3xl font-extrabold text-slate-900 dark:text-white">
                            Rp{{ number_format($service->price, 0, ',', '.') }}
                        </strong>
                        <span class="text-sm text-slate-500 dark:text-slate-400">/ package</span>
                    </p>

                    <form action="{{ route('user.orders-service.store') }}" method="post" class="grid gap-4">
                        @csrf

                        <input type="hidden" name="service_id" value="{{ $service->id }}" />

                        <label class="grid gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            Implementation date

                            <input type="date" name="scheduled_date" required min="{{ now()->format('Y-m-d') }}"
                                max="{{ now()->addDay()->format('Y-m-d') }}"
                                class="input input-bordered w-full min-w-0 bg-white dark:bg-slate-900 text-slate-900 dark:text-white border-slate-300 dark:border-slate-700">

                            <span class="text-xs font-normal text-slate-500 dark:text-slate-400">
                                Pilih tanggal hari ini atau besok.
                            </span>
                        </label>

                        <label class="grid gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            Notes / Brief
                            <textarea name="brief" rows="4" maxlength="2000"
                                placeholder="Jelaskan detail kebutuhan proyek Anda..."
                                class="textarea textarea-bordered w-full bg-white dark:bg-slate-900 text-slate-900 dark:text-white border-slate-300 dark:border-slate-700"></textarea>
                        </label>

                        <button type="submit"
                            class="btn border-0 bg-emerald-600 text-white hover:bg-emerald-700 w-full mt-2">
                            Send Booking Request
                        </button>
                    </form>
                </section>
            </aside>

        </div>
    </main>
@endsection
