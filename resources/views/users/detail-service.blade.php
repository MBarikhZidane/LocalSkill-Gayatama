@extends('layouts.landingpage')
@section('title', 'Skill details | LOCALSKILL')
@section('content')
<main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:py-12">
    <a href="{{ route('explore.index') }}" class="text-sm font-semibold text-emerald-700">&larr; Back to skills</a>
    <div id="service-detail" class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
      <div class="grid min-w-0 gap-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8" aria-labelledby="service-title">
          <span class="badge border-emerald-200 bg-emerald-50 text-emerald-800">{{ $service->category?->name ?? 'Campus skill' }}</span>
          <h1 id="service-title" class="mt-5 text-3xl font-bold">{{ $service->title }}</h1>
          <div class="my-6 flex items-center gap-3">
            <span id="provider-avatar" role="img" aria-label="Provider avatar" class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-slate-900 font-bold text-white">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($service->user->name, 0, 2)) }}</span>
            <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Provider</p><a id="provider-link" href="{{ route('profile.viewprofile', $service->user_id) }}" class="font-semibold text-emerald-700 underline underline-offset-4"><span id="provider-name">{{ $service->user->name }}</span> &rarr;</a></div>
          </div>
          <a href="#reviews" id="provider-rating" class="font-semibold text-emerald-700">&#9733; {{ number_format($avgRating, 1) }} ({{ $totalReviews }} reviews for this service)</a>
          <h2 class="mt-8 text-xl font-bold">Description</h2>
          <p id="service-description" class="mt-3 leading-7 text-slate-600">{{ $service->description }}</p>
          <h2 class="mt-6 text-xl font-bold">Skills</h2>
          <ul id="service-skills" class="mt-3 flex flex-wrap gap-2">@forelse($service->user->skills as $skill)<li class="rounded-full bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ $skill->name }}</li>@empty<li class="text-sm text-slate-500">No skills added yet.</li>@endforelse</ul>
          <dl class="mt-6 grid gap-5 rounded-xl bg-slate-50 p-5">
            <div><dt class="font-bold">Estimated completion</dt><dd id="service-estimate" class="mt-1 text-sm leading-6 text-slate-600">{{ $service->estimated_days }} working day(s).</dd></div>
            <div><dt class="font-bold">Location</dt><dd id="provider-zone" class="mt-1 text-sm text-slate-600">{{ $service->user->university?->name }} {{ $service->user->location?->address }}</dd></div>
          </dl>
          <p class="mt-3 text-xs leading-5 text-slate-500">Timing starts after your provider confirms the brief and availability. Agree on the exact meeting or pickup point together.</p>
        </section>
        <section id="portfolio" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8" aria-labelledby="portfolio-title">
          <h2 id="portfolio-title" class="text-xl font-bold">Portfolio</h2>
          <p class="mt-2 text-sm text-slate-500">Selected work from this provider</p>
          <div id="service-portfolio" class="mt-5 grid gap-4 sm:grid-cols-2"><x-portfolio-cards :portfolios="$service->user->portofolios" /></div>
        </section>
        <section id="reviews" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8" aria-labelledby="reviews-title">
          <h2 id="reviews-title" class="text-xl font-bold">Reviews</h2>
          <p class="mt-2 text-sm text-slate-500">Customer reviews for this service.</p>
          <div id="service-reviews" class="mt-5 grid gap-5"><x-review-cards :reviews="$service->reviews" /> @if($canReview)<a href="{{ route('user.reviews.index', ['order' => $userOrder->id]) }}" class="btn mt-5 bg-emerald-600 text-white">Write Your Review</a>@endif</div>
        </section>
      </div>
      <aside class="grid min-w-0 gap-6 lg:sticky lg:top-6" aria-label="Booking">
        <section id="booking" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8" aria-labelledby="booking-title">
          <h2 id="booking-title" class="text-xl font-bold">Request a booking</h2>
          <p class="my-5"><strong id="service-price" class="text-3xl">Rp{{ number_format($service->price, 0, ',', '.') }}</strong> <span class="text-sm text-slate-500">/ package</span></p>
          <form action="{{ route('user.orders-service.store') }}" method="post" class="grid gap-4">
            @csrf<input type="hidden" name="service_id" value="{{ $service->id }}">
            <label class="grid gap-2 text-sm font-semibold">Preferred date<input type="date" name="scheduled_date" value="{{ old('scheduled_date') }}" min="{{ now()->toDateString() }}" required class="input input-bordered w-full min-w-0"></label>
            <label class="grid gap-2 text-sm font-semibold">Your brief<textarea name="brief" rows="4" minlength="20" maxlength="2000" required placeholder="Describe your project, expectations, and deadline (at least 20 characters)." class="textarea textarea-bordered w-full">{{ old('brief') }}</textarea></label>
            <p class="text-sm text-slate-600">Your provider will confirm availability. No payment is taken here.</p>
            @auth<button type="submit" class="btn border-0 bg-emerald-600 text-white hover:bg-emerald-700">Request / Book</button>@else<a href="{{ route('login') }}" class="btn bg-emerald-600 text-white">Log in to book</a>@endauth
            <p data-feedback role="status" class="text-sm text-emerald-800"></p>
          </form>
        </section>
        <section class="rounded-2xl bg-emerald-50 p-6" aria-labelledby="progress-title">
          <h2 id="progress-title" class="font-bold">A clear path from idea to done</h2>
          <ol class="mt-4 flex flex-wrap gap-2 text-xs" aria-label="Order progress"><li class="rounded-full bg-white px-3 py-2">1 Find</li><li aria-current="step" class="rounded-full bg-emerald-600 px-3 py-2 text-white">2 Match</li><li class="rounded-full bg-white px-3 py-2">3 Book</li><li class="rounded-full bg-white px-3 py-2">4 Complete</li><li class="rounded-full bg-white px-3 py-2">5 Review</li></ol>
          <p class="mt-4 text-sm leading-6 text-slate-600">Share your brief and preferred date. Your provider confirms availability before the booking is agreed.</p>
        </section>
      </aside>
    </div>
    <p id="missing-service" hidden class="py-16 text-xl">This skill is unavailable. <a href="{{ route('explore.index') }}" class="underline">Browse other skills</a>.</p>

  </main>
@endsection
