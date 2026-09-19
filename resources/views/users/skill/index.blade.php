@extends('layouts.app')
@section('title', 'Skills')

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
                    My Skills
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">List of skills and expertise you possess</p>
            </div>
            <span class="badge border-emerald-200 bg-emerald-50 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800">
                Skills
            </span>
        </div>

        {{-- Flash Notifications --}}
        @if (session('success'))
            <div class="mb-4 flex items-center gap-2.5 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 p-3.5 rounded-xl text-xs font-medium shadow-sm">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 flex items-center gap-2.5 bg-rose-50 dark:bg-rose-950/30 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 p-3.5 rounded-xl text-xs font-medium shadow-sm">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Toolbar / Action & Search Card --}}
        <div class="mb-4 flex flex-col lg:flex-row items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">

            <div class="flex items-center gap-2 w-full lg:w-auto justify-between lg:justify-start">
                <a href="{{ route('user.myskill.create') }}"
                    class="btn btn-sm bg-emerald-600 text-white hover:bg-emerald-700 border-0 flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Add Skill</span>
                </a>

                <form action="{{ route('user.myskill.destroy-bulk') }}" method="POST" x-show="selectedIds.length > 0" x-cloak
                    onsubmit="return confirm('Are you sure you want to delete the selected items?');">
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

            {{-- Form Search & Limit --}}
            <form action="{{ route('user.myskill.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <select name="per_page" id="per_page" onchange="this.form.submit()"
                        class="select select-bordered select-sm text-xs bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name / description..."
                        class="input input-sm w-full pl-9 pr-8 text-xs bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('user.myskill.index') }}" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Data Table Card --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 dark:border-slate-800 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 uppercase tracking-wider font-semibold text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="p-4 w-10">
                                <input type="checkbox" @click="toggleAll()" :checked="allChecked"
                                    class="checkbox checkbox-xs checkbox-emerald">
                            </th>
                            <th class="p-4">Skill Name</th>
                            <th class="p-4">Proficiency Level</th>
                            <th class="p-4">Experience</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($skills ?? [] as $skill)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="p-4">
                                    <input type="checkbox" value="{{ $skill->id }}" @click="toggleRow('{{ $skill->id }}')"
                                        :checked="selectedIds.includes('{{ $skill->id }}')"
                                        class="row-checkbox checkbox checkbox-xs checkbox-emerald">
                                </td>
                                <td class="p-4 font-bold text-slate-900 dark:text-white">
                                    <div>{{ $skill->name }}</div>
                                    @if($skill->description)
                                        <div class="text-[11px] font-normal text-slate-400 mt-0.5">{{ $skill->description }}</div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ $skill->pivot->proficiency_level ?? 'Not set' }}
                                    </span>
                                </td>
                                <td class="p-4 text-slate-900 dark:text-white font-medium">
                                    {{ $skill->pivot->years_experience ?? 0 }} {{ Str::plural('Year', $skill->pivot->years_experience ?? 0) }}
                                </td>
                                <td class="p-4 flex items-center justify-center gap-2">
                                    <a href="{{ route('user.myskill.edit', $skill->id) }}"
                                        class="btn btn-ghost btn-xs text-slate-600 hover:text-emerald-600 dark:text-slate-400">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <form action="{{ route('user.myskill.destroy', $skill->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to remove this skill from your profile?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-ghost btn-xs text-rose-600 hover:text-rose-700 dark:text-rose-400">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 dark:text-slate-500">
                                    @if(request('search'))
                                        No skills found with the keyword "<span class="font-semibold text-slate-600 dark:text-slate-300">{{ request('search') }}</span>".
                                    @else
                                        No skills have been added to your profile yet.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer / Pagination Section --}}
            @if(isset($skills) && method_exists($skills, 'hasPages') && $skills->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                    {{ $skills->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
