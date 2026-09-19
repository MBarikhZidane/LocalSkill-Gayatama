<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="themeManager()"
      x-init="init()"
      :data-theme="darkMode ? 'dark' : 'light'"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LOCALSKILL') }}</title>

    <!-- DaisyUI & Tailwind CDN -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts (Vite / Lucide) -->
    <script defer src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Tailwind Dark Mode Configuration -->
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>

    <!-- Theme Manager Script -->
    <script>
        function themeManager() {
            return {
                darkMode: false,

                init() {
                    const savedTheme = localStorage.getItem('theme');

                    if (savedTheme) {
                        this.darkMode = savedTheme === 'dark';
                    } else {
                        this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }

                    this.applyTheme();
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                    this.applyTheme();
                },

                applyTheme() {
                    document.documentElement.classList.toggle('dark', this.darkMode);
                    document.documentElement.setAttribute('data-theme', this.darkMode ? 'dark' : 'light');
                }
            }
        }
    </script>

    <!-- Prevent Alpine Flash -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 antialiased transition-colors duration-200">
    <a href="#content" class="sr-only focus:not-sr-only focus:p-4">Skip to content</a>

    <!-- Header -->
    <header class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 transition-colors duration-200">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4" aria-label="Main navigation">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                LOCAL<span class="text-emerald-600">SKILL</span>
            </a>

            <!-- Nav Right Menu -->
            <div class="flex items-center gap-4">
                <a href="#" class="text-sm font-semibold text-slate-700 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400">Explore</a>
                <a href="#" class="text-sm font-semibold text-slate-700 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400">Workspace</a>

                <!-- Dark Mode Toggle Button -->
                <button type="button" @click="toggleTheme()" class="btn btn-ghost btn-circle text-slate-700 dark:text-slate-300" aria-label="Toggle dark mode" title="Toggle dark mode">
                    <!-- Icon Sun -->
                    <i x-show="darkMode" x-cloak data-lucide="sun" class="h-5 w-5"></i>
                    <!-- Icon Moon -->
                    <i x-show="!darkMode" x-cloak data-lucide="moon" class="h-5 w-5"></i>
                </button>

                <!-- Auth Buttons -->
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm bg-emerald-600 text-white hover:bg-emerald-700 border-none">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm bg-emerald-600 text-white hover:bg-emerald-700 border-none">Log in</a>
                @endauth
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:py-12">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 transition-colors duration-200">
        <div class="mx-auto flex max-w-6xl flex-wrap justify-between gap-3 px-4 py-6 text-sm text-slate-600 dark:text-slate-400">
            <span>&copy; {{ date('Y') }} LOCALSKILL &middot; Built for campus life.</span>
            <a href="#how" class="hover:underline hover:text-emerald-600 dark:hover:text-emerald-400">Find &rarr; Match &rarr; Book &rarr; Complete &rarr; Review</a>
        </div>
    </footer>

    <!-- Lucide Icons Initialization -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
