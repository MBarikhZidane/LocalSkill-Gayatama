@extends('layouts.app')
@section('title', 'Create Skill')
@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header Section --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Add New Skill
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Add new skills to your profile.</p>
            </div>
            <a href="{{ route('user.myskill.index') }}"
                class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 border-0 flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back</span>
            </a>
        </div>

        {{-- Alert Validation Errors --}}
        @if ($errors->any())
            <div
                class="mb-6 bg-rose-50 dark:bg-rose-950/30 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 p-4 rounded-2xl text-xs shadow-sm">
                <div class="flex items-center gap-2 font-bold mb-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400"></i>
                    <span>An input error occurred.:</span>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <form action="{{ route('user.myskill.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Input Nama Skill --}}
                <div x-data="skillSearch()" @click.outside="open = false" class="relative">
                    <label for="name"
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Skill Name<span class="text-rose-500">*</span>
                    </label>

                    {{-- Input yang dikirim ke controller --}}
                    <input type="hidden" name="skill_id" x-model="selectedId">

                    <input type="text" name="name" id="name" x-model="query" @input.debounce.300ms="searchSkills()"
                        @focus="open = true" autocomplete="off"
                        placeholder="Contoh: Web Development, Public Speaking, Python..." class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700
                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                       focus:outline-none focus:ring-2 focus:ring-emerald-500/20
                       focus:border-emerald-500 transition
                       @error('name') border-rose-500 @enderror" required>

                    {{-- Loading --}}
                    <div x-show="loading" x-cloak class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-slate-800
                       border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg p-3">
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>

                            Search skill...
                        </div>
                    </div>

                    {{-- Dropdown hasil pencarian --}}
                    <div x-show="open && !loading && (results.length > 0 || (query.length >= 2 && searched))" x-cloak class="absolute z-40 left-0 right-0 mt-1 bg-white dark:bg-slate-800
                       border border-slate-200 dark:border-slate-700
                       rounded-xl shadow-xl overflow-hidden">

                        {{-- Hasil skill --}}
                        <template x-for="skill in results" :key="skill.id">
                            <button type="button" @click="selectSkill(skill)" class="w-full text-left px-4 py-3 hover:bg-slate-50
                               dark:hover:bg-slate-700/50 transition border-b
                               border-slate-100 dark:border-slate-700 last:border-0">
                                <div class="flex items-center gap-3">

                                    <div class="flex-shrink-0 w-8 h-8 rounded-lg
                                        bg-emerald-100 dark:bg-emerald-900/30
                                        flex items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-slate-900 dark:text-white" x-text="skill.name">
                                        </p>

                                        <p x-show="skill.description"
                                            class="text-[11px] text-slate-500 dark:text-slate-400 truncate"
                                            x-text="skill.description"></p>
                                    </div>
                                </div>
                            </button>
                        </template>

                        {{-- Tidak ditemukan --}}
                        <div x-show="results.length === 0 && query.length >= 2 && searched" class="p-4">
                            <div class="text-center mb-3">
                                <p class="text-xs font-medium text-slate-700 dark:text-slate-300">
                                    Skill not found </p>

                                <p class="text-[11px] text-slate-400 mt-1">
You can create a new skill with this name.                                </p>
                            </div>

                            <button type="button" @click="createNewSkill()" class="w-full flex items-center justify-center gap-2
                               px-4 py-2.5 rounded-lg
                               bg-emerald-600 hover:bg-emerald-700
                               text-white text-xs font-semibold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4">
                                    </path>
                                </svg>

                                Create Skill "<span x-text="query"></span>"
                            </button>
                        </div>
                    </div>

                    {{-- Skill yang terpilih --}}
                    <div x-show="selectedId" x-cloak class="mt-2 flex items-center justify-between gap-3
                       px-3 py-2 rounded-lg
                       bg-emerald-50 dark:bg-emerald-900/20
                       border border-emerald-200 dark:border-emerald-800">
                        <div class="flex items-center gap-2 min-w-0">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>

                            <span class="text-xs font-medium text-emerald-700 dark:text-emerald-300 truncate"
                                x-text="'Skill dipilih: ' + query"></span>
                        </div>

                        <button type="button" @click="clearSkill()"
                            class="text-emerald-600 hover:text-rose-500 dark:text-emerald-400">
                            ✕
                        </button>
                    </div>

                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Input Grid (Tingkat Kemahiran & Pengalaman) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="proficiency_level"
                            class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Proficiency Level <span class="text-rose-500">*</span>
                        </label>
                        <select name="proficiency_level" id="proficiency_level"
                            class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition @error('proficiency_level') border-rose-500 @enderror"
                            required>
                            <option value="">-- Select Proficiency Level --</option>
                            <option value="1" {{ old('proficiency_level') == 'Beginner' ? 'selected' : '' }}>Beginner
                                </option>
                            <option value="2" {{ old('proficiency_level') == 'Intermediate' ? 'selected' : '' }}>
                                Intermediate </option>
                            <option value="3" {{ old('proficiency_level') == 'Advanced' ? 'selected' : '' }}>Advanced
                                </option>
                            <option value="4" {{ old('proficiency_level') == 'Expert' ? 'selected' : '' }}>Expert
                            </option>
                        </select>
                        @error('proficiency_level')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="years_experience"
                            class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Experience (Years)
                        </label>
                        <input type="number" name="years_experience" id="years_experience"
                            value="{{ old('years_experience', 0) }}" min="0" placeholder="Explain: 2"
                            class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition @error('years_experience') border-rose-500 @enderror">
                        @error('years_experience')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Input Deskripsi --}}
                <div>
                    <label for="description"
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Description <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <textarea name="description" id="description" rows="4"
                        placeholder="Tambahkan penjelasan singkat mengenai skill ini..."
                        class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <a href="{{ route('user.myskill.index') }}"
                        class="btn btn-sm bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border-0">
                        Cancel
                    </a>
                    <button type="submit"
                        class="btn btn-sm bg-emerald-600 text-white hover:bg-emerald-700 border-0 flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Save Skill</span>
                    </button>
                </div>
            </form>
        </div>
        <script>
            function skillSearch() {
                return {
                    query: @js(old('name', '')),
                    selectedId: @js(old('skill_id', '')),
                    results: [],
                    loading: false,
                    open: false,
                    searched: false,

                    async searchSkills() {

                        this.selectedId = '';
                        this.searched = false;

                        if (this.query.trim().length < 2) {
                            this.results = [];
                            this.open = false;
                            return;
                        }

                        this.loading = true;
                        this.open = true;

                        try {
                            const response = await fetch(
                                `{{ route('user.myskill.search') }}?query=${encodeURIComponent(this.query)}`
                            );

                            if (!response.ok) {
                                throw new Error('Gagal mencari skill');
                            }

                            this.results = await response.json();
                            this.searched = true;

                        } catch (error) {
                            console.error(error);
                            this.results = [];
                        } finally {
                            this.loading = false;
                        }
                    },

                    selectSkill(skill) {
                        this.query = skill.name;
                        this.selectedId = skill.id;
                        this.open = false;
                        this.searched = true;
                    },

                    createNewSkill() {
                        this.selectedId = '';
                        this.open = false;
                        this.searched = true;
                    },

                    clearSkill() {
                        this.query = '';
                        this.selectedId = '';
                        this.results = [];
                        this.open = false;
                        this.searched = false;

                        this.$nextTick(() => {
                            document.getElementById('name')?.focus();
                        });
                    }
                }
            }
        </script>
    </div>
@endsection
