@extends('layouts.landingpage')
@section('title', 'Messages | LOCALSKILL')
@section('content')
<main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:py-12">
    <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">{{ auth()->user()->name }}'s workspace</p>
    <h1 class="mt-3 text-3xl font-bold">Messages</h1><p class="mt-3 max-w-3xl leading-7 text-slate-600">Keep your order conversations together, whether you are chatting with a provider or a customer.</p>
<x-workspace-nav />


    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]">
      <section class="min-w-0 rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="conversations-title"><h2 id="conversations-title" class="text-xl font-bold">Conversations</h2><div id="conversation-list" data-endpoint="{{ route('user.chat.conversations') }}" class="mt-4 grid max-h-80 gap-2 overflow-y-auto lg:max-h-[36rem]"></div></section>
      <section id="conversation" hidden class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white" aria-labelledby="conversation-name">
        <div class="border-b border-slate-200 p-6"><p id="conversation-role" class="text-xs font-bold uppercase tracking-wide text-emerald-700"></p><h2 id="conversation-name" class="mt-2 text-xl font-bold"></h2><p id="conversation-order" class="mt-2 text-sm text-slate-600"></p><a id="conversation-service" href="{{ route('explore.index') }}" class="mt-3 inline-block text-sm font-semibold text-emerald-700 underline">View service</a></div>
        <ol id="chat-log" role="log" aria-label="Conversation messages" aria-live="polite" aria-relevant="additions" class="flex max-h-96 min-h-64 flex-col gap-4 overflow-y-auto bg-slate-50 p-5"></ol>
        <form id="message-form" method="POST" class="grid gap-3 border-t border-slate-200 p-6"><label class="grid gap-2 text-sm font-semibold">Your message<textarea name="message" required maxlength="1000" rows="3" class="textarea textarea-bordered w-full" placeholder="Write about the brief, timing, or delivery..."></textarea></label><div class="flex flex-wrap items-center justify-between gap-3"><p class="text-xs text-slate-500">Keep communication about your order here.</p><button type="submit" class="btn border-0 bg-emerald-600 text-white hover:bg-emerald-700">Send message</button></div><p id="message-feedback" role="status" class="text-sm text-emerald-800"></p></form>
      </section>
      <section id="conversation-empty" class="rounded-2xl border border-slate-200 bg-white p-8"><h2 class="text-xl font-bold">Choose a conversation</h2><p id="conversation-empty-text" class="mt-3 text-slate-600">Select an order on the left to see its provider or customer conversation.</p></section>
    </div>

  </main>
@endsection
@push('scripts')<script defer src="{{ asset('js/messages.js') }}"></script>@endpush
