@extends('layouts.landingpage')

@section('title', 'Account Settings | LOCALSKILL')

@section('content')
<main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-6 sm:pb-8 sm:pt-2 transition-colors duration-200">

    {{-- Back Button --}}
    <a href="{{ route('explore.index') }}"
       class="mb-5 inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors">
        <i data-lucide="arrow-left" class="h-4 w-4"></i> Back
    </a>

    {{-- Toast Notification --}}
    @if(session('success'))
        <div class="alert alert-success mb-6 shadow-lg text-white bg-emerald-600 border-none">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error mb-6 shadow-lg text-white bg-rose-600 border-none">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
        {{ $user->name }}'s workspace &middot; {{ ucfirst($user->role ?? 'User') }}
    </p>

    <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Account Settings</h1>
            <p class="mt-2 text-sm sm:text-base text-slate-600 dark:text-slate-400">
                Manage your personal information, role status, and account preferences.
            </p>
        </div>
    </div>

    <div class="mt-8 grid gap-8 md:grid-cols-3">

        {{-- Section Kiri: Banner Role Provider --}}
        <div class="md:col-span-1 space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Account Role</h2>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Current Status:</span>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize 
                        {{ $user->role === 'provider' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' }}">
                        {{ $user->role ?? 'Customer' }}
                    </span>
                </div>

                @if($user->role !== 'provider')
                    <div class="rounded-xl bg-emerald-50 p-4 dark:bg-emerald-950/30 dark:border dark:border-emerald-800/50 mb-4">
                        <h3 class="text-sm font-bold text-emerald-900 dark:text-emerald-300">Become a Provider!</h3>
                        <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-400">
                            Offer your skills, create services, and start earning on LOCALSKILL today.
                        </p>
                    </div>

                    <form action="{{ route('user.profile.register-provider') }}" method="POST" onsubmit="return confirm('Do you want to switch your account to Provider status?')">
                        @csrf
                        <button type="submit" class="btn btn-emerald w-full text-white bg-emerald-600 hover:bg-emerald-700 border-none">
                            <i data-lucide="briefcase" class="h-4 w-4 mr-2"></i> Register as Provider
                        </button>
                    </form>
                @else
                    <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900 dark:border dark:border-slate-700">
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                             You are currently registered as a Service Provider. You can offer services and receive client orders.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Section Kanan: Form Edit Profil --}}
        <div class="md:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Edit Profile Details</h2>

                <form action="{{ route('user.profile.update') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Full Name
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="input input-bordered w-full dark:bg-slate-900 dark:text-white dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                        @error('name')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="input input-bordered w-full dark:bg-slate-900 dark:text-white dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                        @error('email')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Phone / WhatsApp Number
                        </label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 081234567890"
                               class="input input-bordered w-full dark:bg-slate-900 dark:text-white dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                        @error('phone')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- University & Study Program --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="university_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                                University
                            </label>
                            <select id="university_id" name="university_id" class="select select-bordered w-full dark:bg-slate-900 dark:text-white dark:border-slate-700">
                                <option value="">Select University</option>
                                @foreach($universities as $university)
                                    <option value="{{ $university->id }}" {{ old('university_id', $user->university_id) == $university->id ? 'selected' : '' }}>
                                        {{ $university->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('university_id')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Bio --}}
                    <div>
                        <label for="bio" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Bio / Description
                        </label>
                        <textarea id="bio" name="bio" rows="4" placeholder="Tell us a little about yourself..."
                                  class="textarea textarea-bordered w-full dark:bg-slate-900 dark:text-white dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <button type="submit" class="btn btn-emerald text-white bg-emerald-600 hover:bg-emerald-700 border-none">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</main>
@endsection