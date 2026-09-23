<nav class="space-y-1">
    @if (Auth::user()->role === 'admin')
        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false" class="group relative flex items-center gap-3
                  rounded-lg px-3 py-2.5
                  text-sm font-medium
                  transition-all duration-150

                  {{ request()->routeIs('admin.dashboard.*')
            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white'
                  }}">

            {{-- Active Indicator --}}
            @if(request()->routeIs('admin.dashboard.*'))
                <span class="absolute left-0 top-1/2 h-6 w-0.5
                                 -translate-y-1/2 rounded-full
                                 bg-emerald-600 dark:bg-emerald-400">
                </span>
            @endif

            <span>{{ __('Dashboard') }}</span>
        </a>


        {{-- Pengguna --}}
        <a href="{{ route('admin.users.index') }}" @click="sidebarOpen = false" class="group relative flex items-center gap-3
                  rounded-lg px-3 py-2.5
                  text-sm font-medium
                  transition-all duration-150

                  {{ request()->routeIs('admin.users.*')
            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white'
                  }}">

            @if(request()->routeIs('admin.users.*'))
                <span class="absolute left-0 top-1/2 h-6 w-0.5
                                 -translate-y-1/2 rounded-full
                                 bg-emerald-600 dark:bg-emerald-400">
                </span>
            @endif

            <span>{{ __('Daftar Pengguna') }}</span>
        </a>


        {{-- Kategori --}}
        <a href="{{ route('admin.categories.index') }}" @click="sidebarOpen = false" class="group relative flex items-center gap-3
                  rounded-lg px-3 py-2.5
                  text-sm font-medium
                  transition-all duration-150

                  {{ request()->routeIs('admin.categories.*')
            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white'
                  }}">

            @if(request()->routeIs('admin.categories.*'))
                <span class="absolute left-0 top-1/2 h-6 w-0.5
                                 -translate-y-1/2 rounded-full
                                 bg-emerald-600 dark:bg-emerald-400">
                </span>
            @endif


            <span>{{ __('Daftar Kategori') }}</span>
        </a>


        {{-- Mata Pelajaran --}}
        <a href="{{ route('admin.skills.index') }}" @click="sidebarOpen = false" class="group relative flex items-center gap-3
                  rounded-lg px-3 py-2.5
                  text-sm font-medium
                  transition-all duration-150

                  {{ request()->routeIs('admin.skills.*')
            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white'
                  }}">

            @if(request()->routeIs('admin.skills.*'))
                <span class="absolute left-0 top-1/2 h-6 w-0.5
                                 -translate-y-1/2 rounded-full
                                 bg-emerald-600 dark:bg-emerald-400">
                </span>
            @endif


            <span>{{ __('Daftar Skill') }}</span>
        </a>

        <a href="{{ route('admin.orders.index') }}" @click="sidebarOpen = false" class="group relative flex items-center gap-3
                  rounded-lg px-3 py-2.5
                  text-sm font-medium
                  transition-all duration-150

                  {{ request()->routeIs('admin.orders.*')
            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white'
                  }}">

            @if(request()->routeIs('admin.orders.*'))
                <span class="absolute left-0 top-1/2 h-6 w-0.5
                                 -translate-y-1/2 rounded-full
                                 bg-emerald-600 dark:bg-emerald-400">
                </span>
            @endif

            <span>{{ __('Daftar Order') }}</span>
        </a>

        <a href="{{ route('admin.services.index') }}" @click="sidebarOpen = false" class="group relative flex items-center gap-3
                  rounded-lg px-3 py-2.5
                  text-sm font-medium
                  transition-all duration-150

                  {{ request()->routeIs('admin.services.*')
            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white'
                  }}">

            @if(request()->routeIs('admin.services.*'))
                <span class="absolute left-0 top-1/2 h-6 w-0.5
                                 -translate-y-1/2 rounded-full
                                 bg-emerald-600 dark:bg-emerald-400">
                </span>
            @endif

            <span>{{ __('Daftar Jasa') }}</span>
        </a>

        <a href="{{ route('admin.portofolios.index') }}" @click="sidebarOpen = false" class="group relative flex items-center gap-3
                  rounded-lg px-3 py-2.5
                  text-sm font-medium
                  transition-all duration-150

                  {{ request()->routeIs('admin.portofolios.*')
            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white'
                  }}">

            @if(request()->routeIs('admin.portofolios.*'))
                <span class="absolute left-0 top-1/2 h-6 w-0.5
                                 -translate-y-1/2 rounded-full
                                 bg-emerald-600 dark:bg-emerald-400">
                </span>
            @endif

            <span>{{ __('Daftar Portofolio') }}</span>
        </a>

    @elseif (Auth::user()->role === 'provider')
        <x-nav-link href="{{ route('user.dashboarduser') }}"
            class="w-full justify-start py-2.5 px-4 rounded transition duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 block text-sm">
            {{ __('Dashboard') }}
        </x-nav-link>
        <x-nav-link href="{{ route('user.services.index') }}"
            class="w-full justify-start py-2.5 px-4 rounded transition duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 block text-sm">
            {{ __('My Services') }}
        </x-nav-link>
        <x-nav-link href="{{ route('user.orders.index') }}"
            class="w-full justify-start py-2.5 px-4 rounded transition duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 block text-sm">
            {{ __('Orders Received') }}
        </x-nav-link>
        <x-nav-link href="{{ route('user.myskill.index') }}"
            class="w-full justify-start py-2.5 px-4 rounded transition duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 block text-sm">
            {{ __('My Skills') }}
        </x-nav-link>
        <x-nav-link href="{{ route('user.portofolios.index') }}"
            class="w-full justify-start py-2.5 px-4 rounded transition duration-200 hover:bg-gray-100 dark:hover:bg-gray-700 block text-sm">
            {{ __('My Portofolio') }}
        </x-nav-link>
    @endif
    <a href="{{ route('user.chat.index') }}" @click="sidebarOpen = false" class="block rounded px-4 py-2.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Order Messages <span data-workflow-total class="badge badge-sm" hidden></span></a>
</nav>
