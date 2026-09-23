@props(['portfolios'])
@forelse($portfolios as $portfolio)
<article class="overflow-hidden rounded-xl border border-slate-200">
<div class="flex h-28 items-center justify-center bg-emerald-50 text-emerald-700"><i data-lucide="panels-top-left" class="h-12 w-12" aria-hidden="true"></i></div>
<div class="p-4"><p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ $portfolio->project_type ?? $portfolio->type }}</p><h3 class="mt-2 font-bold">{{ $portfolio->title ?? $portfolio->skill?->name ?? 'Portfolio project' }}</h3><p class="mt-2 text-sm leading-6 text-slate-600">{{ $portfolio->description }}</p>
@if($portfolio->skill)<ul class="mt-3 flex flex-wrap gap-2"><li class="rounded-full bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ $portfolio->skill->name }}</li></ul>@endif
@if($portfolio->evidence)<a href="{{ asset('storage/'.$portfolio->evidence) }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-block text-sm text-emerald-700 underline">View evidence</a>@endif</div></article>
@empty<p class="text-sm text-slate-500">No portfolio projects yet.</p>@endforelse
