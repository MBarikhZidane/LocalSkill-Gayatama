<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8"><meta name="csrf-token" content="{{ csrf_token() }}"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>[hidden]{display:none!important}</style>
  <meta name="description" content="Find, book, and share trusted student skills around your campus."><title>@yield('title', 'LOCALSKILL')</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet"><script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js"></script><script defer src="{{ asset('js/frontend.js') }}"></script><script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
[hidden],[x-cloak]{display:none!important}textarea,p,h2,h3{overflow-wrap:anywhere}progress{accent-color:#059669}
a:focus-visible,button:focus-visible,input:focus-visible,textarea:focus-visible,select:focus-visible{outline:3px solid #059669;outline-offset:3px}
html.dark .bg-white{background-color:#0f172a}html.dark .bg-slate-50{background-color:#020617}html.dark .text-slate-800,html.dark .text-slate-900{color:#f1f5f9}html.dark .text-slate-600,html.dark .text-slate-500{color:#94a3b8}html.dark .border-slate-200{border-color:#334155}html.dark .bg-emerald-50{background-color:#064e3b}html.dark .text-emerald-700,html.dark .text-emerald-800,html.dark .text-emerald-900{color:#6ee7b7}
</style>
<script>tailwind.config = {darkMode: 'class'};</script>
@stack('styles')
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 antialiased">
  <a href="#content" class="sr-only focus:not-sr-only focus:p-4">Skip to content</a>
  <header class="border-b border-slate-200 bg-white"><nav class="mx-auto flex max-w-6xl flex-wrap items-center gap-4 px-4 py-4" aria-label="Main navigation">
    <a href="{{ route('home') }}" class="mr-auto text-xl font-extrabold tracking-tight">LOCAL<span class="text-emerald-600">SKILL</span></a><a href="{{ route('explore.index') }}" class="text-sm font-semibold">Explore</a><a href="{{ route('user.dashboarduser') }}" class="text-sm font-semibold">Workspace</a><button type="button" data-theme-toggle class="btn btn-ghost btn-sm" aria-label="Toggle dark mode">Theme</button>
@auth
<details class="dropdown dropdown-end"><summary class="btn btn-sm">{{ auth()->user()->name }}</summary><ul class="menu dropdown-content z-30 w-52 rounded-xl bg-white p-2 shadow">
<li><a href="{{ route('user.profile.edit') }}">Settings</a></li><li><a href="{{ route('user.notifications.index') }}">Notifications</a></li>
@if(auth()->user()->role === 'admin')<li><a href="{{ route('admin.dashboard') }}">Administration</a></li>@endif
<li><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log out</button></form></li></ul></details>
@else
<a href="{{ route('login') }}" class="btn btn-sm bg-emerald-600 text-white hover:bg-emerald-700">Log in</a>
@endauth
  </nav></header>
  @if(session('success') || session('error') || $errors->any())
<div class="mx-auto w-full max-w-6xl px-4 pt-4" role="status">
@if(session('success'))<p class="rounded-xl bg-emerald-50 p-4 text-emerald-900">{{ session('success') }}</p>@endif
@if(session('error'))<p class="rounded-xl bg-red-50 p-4 text-red-900">{{ session('error') }}</p>@endif
@if($errors->any())<ul class="rounded-xl bg-red-50 p-4 text-red-900">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
</div>
@endif
@yield('content')
<footer class="border-t border-slate-200 bg-white"><div class="mx-auto flex max-w-6xl flex-wrap justify-between gap-3 px-4 py-6 text-sm text-slate-600"><span>&copy; 2026 LOCALSKILL &middot; Built for campus life.</span><a href="{{ route('home') }}#how">Find &rarr; Match &rarr; Book &rarr; Complete &rarr; Review</a></div></footer>
@stack('scripts')
</body>
</html>
