@extends('layouts.landingpage')
@section('title', 'Create your account | LOCALSKILL')
@section('content')
<main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:py-12">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 mx-auto max-w-md"><p class="text-sm font-semibold text-emerald-700">ONE CAMPUS. MORE POSSIBILITIES.</p><h1 class="mt-3 text-3xl font-bold">Create your account</h1><p class="mb-6 mt-3 text-slate-600">One account to book skills and offer your own.</p>
      <div id="google-signin" class="flex min-h-11 justify-center"><a href="{{ route('google.login') }}" class="btn w-full border-slate-300 bg-white text-slate-700">Continue with Google</a></div><div class="divider text-sm text-slate-500">or use email</div>
      <form action="{{ route('register') }}" method="post" class="grid gap-4">
@csrf
<x-auth-session-status :status="session('status')" />
        <label class="grid gap-2 text-sm font-semibold">Full name<input name="name" value="{{ old('name') }}" autocomplete="name" maxlength="100" required class="input input-bordered"></label>
          <label class="grid gap-2 text-sm font-semibold">University<select name="university_id" class="select select-bordered"><option value="">Choose your university (or complete later)</option>@foreach($universities as $university)<option value="{{ $university->id }}" @selected(old('university_id') == $university->id)>{{ $university->name }}</option>@endforeach</select></label>
        <label class="grid gap-2 text-sm font-semibold">Student email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required class="input input-bordered"></label>
        <label class="grid gap-2 text-sm font-semibold">Password<input type="password" name="password" autocomplete="new-password" minlength="8" required class="input input-bordered"></label>
        <label class="grid gap-2 text-sm font-semibold">Confirm password<input type="password" name="password_confirmation" required autocomplete="new-password" class="input input-bordered"></label><button type="submit" class="btn border-0 bg-emerald-600 text-white hover:bg-emerald-700">Create account</button><p data-feedback role="status" class="text-sm text-emerald-800"></p>
      </form><p class="mt-6 text-sm">Already a member? <a href="{{ route('login') }}" class="font-semibold text-emerald-700">Log in</a></p>
    </section>

  </main>
@endsection
