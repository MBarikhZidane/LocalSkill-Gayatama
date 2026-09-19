@extends('layouts.app')
@section('title', 'Users')
@section('content')
    <div x-data="{
        selectedIds: [],
        allChecked: false,
        toggleAll() {
            this.allChecked = !this.allChecked;
            const checkboxes = document.querySelectorAll('.row-checkbox');
            this.selectedIds = this.allChecked ? Array.from(checkboxes).map(el => el.value) : [];
        },
        toggleRow(id) {
            id = id.toString();
            if (this.selectedIds.includes(id)) {
                this.selectedIds = this.selectedIds.filter(item => item !== id);
                this.allChecked = false;
            } else {
                this.selectedIds.push(id);
                if (this.selectedIds.length === document.querySelectorAll('.row-checkbox').length) {
                    this.allChecked = true;
                }
            }
        }
    }">

        {{-- Header Section --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Manage Users
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">User management and account roles</p>
            </div>
            <span
                class="badge border-emerald-200 bg-emerald-50 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800">
                User
            </span>
        </div>

        {{-- Flash Notifications --}}
        @if (session('success'))
            <div
                class="mb-4 flex items-center gap-2.5 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 p-3.5 rounded-xl text-xs font-medium shadow-sm">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="mb-4 flex items-center gap-2.5 bg-rose-50 dark:bg-rose-950/30 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 p-3.5 rounded-xl text-xs font-medium shadow-sm">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Toolbar / Action & Search Card --}}
        <div
            class="mb-4 flex flex-col lg:flex-row items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">

            {{-- Action Buttons (Add & Bulk Delete) --}}
            <div class="flex items-center gap-2 w-full lg:w-auto justify-between lg:justify-start">
                <a href="{{ route('users.create') }}"
                    class="btn btn-sm bg-emerald-600 text-white hover:bg-emerald-700 border-0 flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Add User</span>
                </a>

                <form action="{{ route('users.destroy-bulk') }}" method="POST" x-show="selectedIds.length > 0" x-cloak
                    onsubmit="return confirm('Are you sure you want to delete the selected users?');">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" :value="JSON.stringify(selectedIds)">
                    <button type="submit"
                        class="btn btn-sm bg-rose-600 text-white hover:bg-rose-700 border-0 flex items-center gap-2">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        <span class="text-xs">Delete Selected (<span x-text="selectedIds.length"></span>)</span>
                    </button>
                </form>
            </div>

            {{-- Form Search, Filter & Limit --}}
            <form action="{{ route('users.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">

                {{-- Dropdown Filter University (Optional) --}}
                @if(isset($universities) && $universities->count() > 0)
                    <div class="w-full sm:w-auto">
                        <select name="university_id" onchange="this.form.submit()"
                            class="select select-bordered select-sm w-full text-xs bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- All Universities --</option>
                            @foreach($universities as $univ)
                                <option value="{{ $univ->id }}" {{ request('university_id') == $univ->id ? 'selected' : '' }}>
                                    {{ $univ->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Dropdown Per Page --}}
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <select name="per_page" id="per_page" onchange="this.form.submit()"
                        class="select select-bordered select-sm text-xs bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                {{-- Input Search --}}
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..."
                        class="input input-sm w-full pl-9 pr-8 text-xs bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    @if(request('search') || request('university_id'))
                        <a href="{{ route('users.index') }}" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Data Table Card --}}
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 dark:border-slate-800 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 uppercase tracking-wider font-semibold text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="p-4 w-10">
                                <input type="checkbox" @click="toggleAll()" :checked="allChecked"
                                    class="checkbox checkbox-xs checkbox-emerald">
                            </th>
                            <th class="p-4">User</th>
                            <th class="p-4">Contact</th>
                            <th class="p-4">University & Major</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($users ?? [] as $user)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="p-4">
                                    <input type="checkbox" value="{{ $user->id }}" @click="toggleRow('{{ $user->id }}')"
                                        :checked="selectedIds.includes('{{ $user->id }}')"
                                        class="row-checkbox checkbox checkbox-xs checkbox-emerald">
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">{{ $user->bio ?? 'No bio available' }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-slate-900 dark:text-white">{{ $user->email }}</div>
                                    <div class="text-slate-400 text-[11px]">{{ $user->phone ?? '-' }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="font-medium text-slate-900 dark:text-white">
                                        {{ $user->university->name ?? '-' }}
                                    </div>
                                    <div class="text-slate-400 text-[11px]">
                                        {{ $user->studyProgram->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="p-4 flex items-center justify-center gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="btn btn-ghost btn-xs text-slate-600 hover:text-emerald-600 dark:text-slate-400" title="Edit">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-ghost btn-xs text-rose-600 hover:text-rose-700 dark:text-rose-400" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400 dark:text-slate-500">
                                    @if(request('search') || request('university_id'))
                                        No user data found matching the search criteria.
                                    @else
                                        No user data available.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer / Pagination Section --}}
            @if(isset($users) && method_exists($users, 'hasPages') && $users->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
