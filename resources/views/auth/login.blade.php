@extends('layouts.landingpage')
@section('title', 'Welcome back | LOCALSKILL')
@section('content')
<main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:py-12">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 mx-auto max-w-md"><p class="text-sm font-semibold text-emerald-700">ONE CAMPUS. MORE POSSIBILITIES.</p><h1 class="mt-3 text-3xl font-bold">Welcome back</h1><p class="mb-6 mt-3 text-slate-600">One account to book skills and offer your own.</p>
      <div id="google-signin" class="flex min-h-11 justify-center"><a href="{{ route('google.login') }}" class="btn w-full border-slate-300 bg-white text-slate-700">Continue with Google</a></div><div class="divider text-sm text-slate-500">or use email</div>
      <form action="{{ route('login') }}" method="post" class="grid gap-4">
@csrf
<x-auth-session-status :status="session('status')" />

        <label class="grid gap-2 text-sm font-semibold">Student email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required class="input input-bordered"></label>
        <label class="grid gap-2 text-sm font-semibold">Password<input type="password" name="password" autocomplete="current-password"  required class="input input-bordered"></label>
        <button type="submit" class="btn border-0 bg-emerald-600 text-white hover:bg-emerald-700">Log in</button><p data-feedback role="status" class="text-sm text-emerald-800"></p>
      <div class="flex items-center justify-between gap-3"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" class="checkbox checkbox-sm">Remember me</label><a href="{{ route('password.request') }}" class="text-sm text-emerald-700 underline">Forgot password?</a></div></form><p class="mt-6 text-sm">New to LOCALSKILL? <a href="{{ route('register') }}" class="font-semibold text-emerald-700">Create an account</a></p>
    </section>

  </main>
@endsection
