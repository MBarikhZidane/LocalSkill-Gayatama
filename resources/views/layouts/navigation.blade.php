<header
    class="sticky top-0 z-30
           h-16
           border-b border-slate-200
           bg-white/95
           backdrop-blur-md
           dark:border-slate-800
           dark:bg-slate-900/95
           transition-colors duration-200">

    <div class="flex h-full items-center justify-between
                px-4 sm:px-6 lg:px-8">

        {{-- =====================================================
            LEFT SIDE
        ====================================================== --}}
        <div class="flex items-center gap-3">

            {{-- Hamburger --}}
            <button
                type="button"
                @click="toggleSidebar()"
                class="flex h-9 w-9 items-center justify-center
                       rounded-lg
                       text-slate-600
                       hover:bg-slate-100
                       hover:text-slate-900
                       dark:text-slate-300
                       dark:hover:bg-slate-800
                       dark:hover:text-white
                       lg:hidden"
                aria-label="Open navigation menu">

                <i
                    x-show="!sidebarOpen"
                    data-lucide="menu"
                    class="h-5 w-5">
                </i>

                <i
                    x-show="sidebarOpen"
                    x-cloak
                    data-lucide="x"
                    class="h-5 w-5">
                </i>
            </button>


            {{-- Logo Mobile --}}
            <a href="{{ route('admin.dashboard') }}"
               class="text-lg font-extrabold tracking-tight
                      text-slate-900
                      dark:text-white
                      lg:hidden">

                LOCAL<span class="text-emerald-600">SKILL</span>
            </a>


            {{-- Desktop Breadcrumb / Page Context --}}
            <div class="hidden lg:flex items-center gap-2">

                <span class="text-sm font-semibold
                             text-slate-700 dark:text-slate-200">

                    Dashboard Management
                </span>

            </div>

        </div>


        {{-- =====================================================
            RIGHT SIDE
        ====================================================== --}}
        <div class="flex items-center gap-2 sm:gap-3">

            {{-- =================================================
                THEME TOGGLE
            ================================================== --}}
            <button
                type="button"
                @click="toggleTheme()"
                class="flex h-9 w-9 items-center justify-center
                       rounded-lg
                       text-slate-600
                       hover:bg-slate-100
                       hover:text-slate-900
                       dark:text-slate-300
                       dark:hover:bg-slate-800
                       dark:hover:text-white
                       transition"
                aria-label="Toggle dark mode">

                <i
                    x-show="darkMode"
                    x-cloak
                    data-lucide="sun"
                    class="h-5 w-5 text-amber-400">
                </i>

                <i
                    x-show="!darkMode"
                    data-lucide="moon"
                    class="h-5 w-5">
                </i>
            </button>

            <div class="hidden sm:block h-6 w-px
                        bg-slate-200 dark:bg-slate-700">
            </div>

            {{-- =================================================
                PROFILE
            ================================================== --}}
            <div class="dropdown dropdown-end">

                <button
                    tabindex="0"
                    type="button"
                    class="flex items-center gap-2
                           rounded-lg
                           p-1
                           hover:bg-slate-100
                           dark:hover:bg-slate-800
                           transition">

                    {{-- Avatar --}}
                    <div
                        class="flex h-9 w-9 items-center justify-center
                               rounded-full
                               bg-slate-900
                               text-xs font-bold
                               text-white
                               dark:bg-emerald-600">

                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}

                    </div>

                </button>


                {{-- Profile Dropdown --}}
                <ul
                    tabindex="0"
                    class="dropdown-content menu menu-sm z-[50]
                           mt-3 w-60
                           rounded-xl
                           border border-slate-200
                           bg-white
                           p-2
                           shadow-xl
                           dark:border-slate-800
                           dark:bg-slate-900
                           text-slate-700
                           dark:text-slate-200">

                    {{-- User Info --}}
                    <li class="pointer-events-none
                               mb-1
                               border-b border-slate-100
                               px-3 py-3
                               dark:border-slate-800">

                        <span class="block truncate
                                     font-bold
                                     text-slate-900
                                     dark:text-white">

                            {{ Auth::user()->name ?? 'User' }}

                        </span>

                        <span class="block truncate
                                     text-xs font-normal
                                     text-slate-500
                                     dark:text-slate-400">

                            {{ Auth::user()->email ?? '' }}

                        </span>

                    </li>


            


                    {{-- Logout --}}
                    <li>
                        <form method="POST"
                              action="{{ route('logout') }}">

                            @csrf

                            <button
                                type="submit"
                                class="flex w-full items-center gap-3
                                       text-left
                                       text-rose-600
                                       dark:text-rose-400">

                                <i data-lucide="log-out"
                                   class="h-4 w-4">
                                </i>

                                Log Out

                            </button>

                        </form>
                    </li>

                </ul>

            </div>

        </div>

    </div>

</header>

