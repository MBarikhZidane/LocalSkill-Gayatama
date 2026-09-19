<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeManager()" x-init="init()"
    :data-theme="darkMode ? 'dark' : 'light'" :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Judul Default') - LocalSkill</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    {{-- DaisyUI --}}
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Lucide --}}
    <script defer src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js"></script>

    {{-- Alpine --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>

    {{-- Theme Manager --}}
    <script>
        function themeManager() {
            return {
                darkMode: false,
                sidebarOpen: false,

                init() {
                    const savedTheme = localStorage.getItem('theme');

                    if (savedTheme) {
                        this.darkMode = savedTheme === 'dark';
                    } else {
                        this.darkMode =
                            window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }

                    this.applyTheme();
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;

                    localStorage.setItem(
                        'theme',
                        this.darkMode ? 'dark' : 'light'
                    );

                    this.applyTheme();
                },

                applyTheme() {
                    document.documentElement.classList.toggle(
                        'dark',
                        this.darkMode
                    );

                    document.documentElement.setAttribute(
                        'data-theme',
                        this.darkMode ? 'dark' : 'light'
                    );
                },

                openSidebar() {
                    this.sidebarOpen = true;
                },

                closeSidebar() {
                    this.sidebarOpen = false;
                },

                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Prevent body scrolling when mobile sidebar is open */
        body:has(.sidebar-open) {
            overflow: hidden;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800
           dark:bg-slate-950 dark:text-slate-100
           antialiased transition-colors duration-200 font-sans">

    {{-- =========================================================
    HEADER
    ========================================================== --}}
    @include('layouts.navigation')


    {{-- =========================================================
    DESKTOP SIDEBAR
    ========================================================== --}}
    <aside class="hidden lg:flex fixed inset-y-0 left-0 z-40
               w-64 flex-col
               border-r border-slate-200
               bg-white
               dark:border-slate-800
               dark:bg-slate-900">

        {{-- Sidebar Header --}}
        <div class="flex h-16 shrink-0 items-center
                    border-b border-slate-200
                    px-6
                    dark:border-slate-800">

            <a href="{{ route('admin.dashboard') }}" class="text-xl font-extrabold tracking-tight
                      text-slate-900 dark:text-white">

                LOCAL<span class="text-emerald-600">SKILL</span>
            </a>
        </div>


        {{-- Sidebar Navigation --}}
        <div class="flex-1 overflow-y-auto px-4 py-6">

            <p class="mb-3 px-3 text-[11px] font-bold
                      uppercase tracking-wider
                      text-slate-400 dark:text-slate-500">
                Menu Utama
            </p>

            @include('layouts.sidebar')
        </div>
    </aside>


    {{-- =========================================================
    MOBILE SIDEBAR OVERLAY
    ========================================================== --}}
    <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="closeSidebar()"
        class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-sm lg:hidden">
    </div>


    {{-- =========================================================
    MOBILE SIDEBAR DRAWER
    ========================================================== --}}
    <aside x-cloak x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full" :class="{ 'sidebar-open': sidebarOpen }" class="fixed inset-y-0 left-0 z-50
               flex w-72 flex-col
               border-r border-slate-200
               bg-white
               shadow-2xl
               dark:border-slate-800
               dark:bg-slate-900
               lg:hidden">

        {{-- Mobile Sidebar Header --}}
        <div class="flex h-16 shrink-0 items-center
                    justify-between
                    border-b border-slate-200
                    px-5
                    dark:border-slate-800">

            <a href="{{ route('admin.dashboard') }}" class="text-xl font-extrabold tracking-tight
                      text-slate-900 dark:text-white">

                LOCAL<span class="text-emerald-600">SKILL</span>
            </a>

            <button type="button" @click="closeSidebar()" class="flex h-9 w-9 items-center justify-center
                       rounded-lg
                       text-slate-500
                       hover:bg-slate-100
                       hover:text-slate-900
                       dark:text-slate-400
                       dark:hover:bg-slate-800
                       dark:hover:text-white" aria-label="Close sidebar">

                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>


        {{-- Mobile Navigation --}}
        <div class="flex-1 overflow-y-auto px-4 py-6">

            <p class="mb-3 px-3 text-[11px] font-bold
                      uppercase tracking-wider
                      text-slate-400 dark:text-slate-500">

                Menu Utama
            </p>

            @include('layouts.sidebar')
        </div>


        {{-- Mobile Sidebar Footer --}}
        <div class="border-t border-slate-200
                    p-4
                    dark:border-slate-800">
        </div>
    </aside>


    {{-- =========================================================
    MAIN AREA
    ========================================================== --}}
    <div class="flex min-h-screen flex-col lg:pl-64">

        {{-- Header is already fixed/sticky from navigation --}}

        <main class="mx-auto w-full max-w-7xl px-4 py-6
                     sm:px-6 lg:px-8">

            @isset($header)

                <header class="mb-6 border-b border-slate-200
                               pb-5
                               dark:border-slate-800">

                    {{ $header }}

                </header>

            @endisset


            {{-- Page Content --}}
            @yield('content')

        </main>


        {{-- =====================================================
        FOOTER
        ====================================================== --}}
        <footer class="mt-auto border-t
                   border-slate-200
                   bg-white
                   dark:border-slate-800
                   dark:bg-slate-900
                   transition-colors duration-200">

            <div class="mx-auto flex max-w-7xl
                       flex-col items-center justify-between
                       gap-2 px-4 py-4
                       text-xs text-slate-500
                       sm:flex-row
                       sm:px-6
                       lg:px-8
                       dark:text-slate-400">

                <span>
                    &copy; {{ date('Y') }} LOCALSKILL
                    &middot; Built for campus life.
                </span>

                <span>
                    Dashboard Management
                </span>

            </div>
        </footer>

    </div>


    {{-- =========================================================
    LUCIDE
    ========================================================== --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (window.lucide) {
                lucide.createIcons();
            }
        });

        document.addEventListener("alpine:init", function () {
            setTimeout(() => {
                if (window.lucide) {
                    lucide.createIcons();
                }
            }, 100);
        });
    </script>

</body>

</html>
