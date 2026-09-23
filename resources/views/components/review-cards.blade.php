@props(['reviews'])
@forelse($reviews as $review)
<article class="border-t border-slate-200 pt-5">
<div class="flex flex-wrap items-center justify-between gap-2"><h3 class="font-bold">{{ $review->reviewer?->name ?? 'Student' }}</h3><p class="text-sm font-semibold text-emerald-700" aria-label="{{ $review->rating }} out of 5 stars">&#9733; {{ number_format($review->rating, 1) }} / 5</p></div>
<time datetime="{{ $review->created_at->toDateString() }}" class="mt-1 block text-xs text-slate-500">{{ $review->created_at->format('d M Y') }}</time><p class="mt-3 text-sm leading-6 text-slate-600">{{ $review->comment }}</p></article>
@empty<p class="text-sm text-slate-500">No reviews yet.</p>@endforelse
