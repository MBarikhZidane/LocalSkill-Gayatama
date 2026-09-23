@extends('layouts.landingpage')
@section('title', 'Student profile | LOCALSKILL')
@section('content')
<main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:py-12">
    <a href="{{ route('explore.index') }}" class="text-sm font-semibold text-emerald-700">&larr; Explore student skills</a>
    <div id="student-profile" class="mt-6 grid gap-6">
      <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white" aria-labelledby="profile-name">
        <div class="bg-emerald-950 px-6 py-8 text-emerald-200 sm:px-8"><p class="text-xs font-bold uppercase tracking-widest">Student profile</p><p class="mt-3 text-2xl font-semibold text-white">Local talent. Real possibilities.</p></div>
        <div class="grid gap-6 p-6 sm:p-8 md:grid-cols-[1fr_auto]">
          <div class="flex items-start gap-4"><span id="profile-avatar" role="img" aria-label="Student avatar" class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-emerald-100 text-2xl font-bold text-emerald-900">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 2)) }}</span><div><h1 id="profile-name" class="text-3xl font-bold">{{ $user->name }}</h1><p id="profile-role" class="mt-1 text-lg text-slate-600">{{ $user->professional_title ?? $user->studyProgram?->name ?? 'Student provider' }}</p><p id="profile-location" class="mt-3 text-sm text-slate-500">{{ $user->university?->name }} {{ $user->location?->address }}</p></div></div>
          <div class="flex flex-wrap items-center gap-6 md:text-right"><a href="#reviews" class="text-emerald-700"><strong id="profile-rating" class="text-2xl">&#9733; {{ number_format($avgRating, 1) }}</strong><span class="block text-sm">Overall rating</span></a><div><strong id="profile-jobs" class="text-2xl">{{ $completedOrdersCount }}</strong><p class="text-sm text-slate-600">Jobs Completed</p></div></div>
          <p id="profile-bio" class="max-w-3xl leading-7 text-slate-600 md:col-span-2">{{ $user->bio }}</p>
        </div>
      </section>
      <nav aria-label="Profile sections" class="flex flex-wrap gap-2"><a href="#skills" class="btn btn-sm">Skills</a><a href="#portfolio" class="btn btn-sm">Portfolio</a><a href="#reviews" class="btn btn-sm">Reviews</a><a href="#services" class="btn btn-sm">Services</a></nav>
      <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]">
        <div class="grid min-w-0 gap-6">
          <section id="skills" class="rounded-2xl border border-slate-200 bg-white p-6" aria-labelledby="skills-title"><h2 id="skills-title" class="text-xl font-bold">Skills</h2><ul id="profile-skills" class="mt-4 flex flex-wrap gap-2">@forelse($user->skills as $skill)<li class="rounded-full bg-emerald-50 px-3 py-2 text-sm text-emerald-800">@if($skill->pivot->is_verified)<span aria-label="Verified">&#10003;</span> @endif{{ $skill->name }}</li>@empty<li class="text-sm text-slate-500">No skills added yet.</li>@endforelse</ul></section>
          <section id="services" class="rounded-2xl border border-slate-200 bg-white p-6" aria-labelledby="services-title"><h2 id="services-title" class="text-xl font-bold">Services</h2><p class="mt-2 text-sm text-slate-500">Choose a service to see its scope and request a booking.</p><div id="profile-services" class="mt-5 grid gap-4">@forelse($user->services->where('status', 'active') as $service)<article class="border-t border-slate-200 pt-4"><h3 class="font-bold">{{ $service->title }}</h3><p class="mt-2 text-sm leading-6 text-slate-600">{{ $service->description }}</p><p class="mt-3 font-bold">Rp{{ number_format($service->price, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">/ package</span></p><a href="{{ route('services.show', $service) }}" class="btn mt-4 w-full border-0 bg-emerald-600 text-white hover:bg-emerald-700">View service / Book</a></article>@empty<p class="text-sm text-slate-500">No services published yet.</p>@endforelse</div></section>
        </div>
        <div class="grid min-w-0 gap-6">
          <section id="portfolio" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8" aria-labelledby="portfolio-title"><h2 id="portfolio-title" class="text-xl font-bold">Portfolio</h2><p class="mt-2 text-sm text-slate-500">Selected work</p><div id="profile-portfolio" class="mt-5 grid gap-4 sm:grid-cols-2"><x-portfolio-cards :portfolios="$user->portofolios" /></div></section>
          <section id="reviews" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8" aria-labelledby="reviews-title"><h2 id="reviews-title" class="text-xl font-bold">Reviews</h2><p class="mt-2 text-sm text-slate-500">Customer reviews. Overall rating covers all reviewed jobs.</p><div id="profile-reviews" class="mt-5 grid gap-5"><x-review-cards :reviews="$reviews" /></div></section>
        </div>
      </div>
    </div>
    <p id="missing-profile" hidden class="py-16 text-xl">This student profile is unavailable. <a href="{{ route('explore.index') }}" class="underline">Explore other students</a>.</p>

  </main>
@endsection
