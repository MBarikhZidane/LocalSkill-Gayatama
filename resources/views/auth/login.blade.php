<x-guest-layout>
    <section class="rounded-2xl border border-slate-200 bg-white p-6 mx-auto max-w-md dark:border-slate-800 dark:bg-slate-900 transition-colors duration-200">
        <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">ONE PLATFORM TO FIND AND BOOK SERVICE.</p>
        <h1 class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">Welcome back</h1>
        <p class="mb-6 mt-3 text-slate-600 dark:text-slate-400">One account to book skills and offer your own.</p>

        <!-- Session Status / Validation Errors -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Login dengan Google -->
        <div id="google-signin" class="flex min-h-11 justify-center">
            <a href="{{ route('google.login') }}" class="btn w-full border-slate-300 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                Continue with Google
            </a>
        </div>

        <div class="divider text-sm text-slate-500 dark:text-slate-400">or use email</div>

        <!-- Form Login Laravel Breeze -->
        <form method="POST" action="{{ route('login') }}" class="grid gap-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="grid gap-2 text-sm font-semibold mb-1 text-slate-700 dark:text-slate-300">Email</label>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" class="input input-bordered w-full bg-white text-slate-900 dark:bg-slate-800 dark:text-white dark:border-slate-700 @error('email') input-error @enderror">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label for="password" class="text-sm font-semibold text-slate-700 dark:text-slate-300">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-emerald-600 hover:underline dark:text-emerald-400">Forgot password?</a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="input input-bordered w-full bg-white text-slate-900 dark:bg-slate-800 dark:text-white dark:border-slate-700 @error('password') input-error @enderror">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember" class="checkbox checkbox-sm checkbox-success dark:border-slate-600">
                    <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">Remember me</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn border-0 bg-emerald-600 text-white hover:bg-emerald-700 w-full mt-2">
                Log in
            </button>
        </form>

        <p class="mt-6 text-sm text-slate-600 dark:text-slate-400">
            New to LOCALSKILL?
            <a href="{{ route('register') }}" class="font-semibold text-emerald-600 hover:underline dark:text-emerald-400">Create an account</a>
        </p>
    </section>
</x-guest-layout>