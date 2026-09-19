@extends('layouts.app')
@section('title', 'Edit Portfolio')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header Section --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Edit Portfolio
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Update your portfolio information.</p>
        </div>
        <a href="{{ route('user.portofolios.index') }}" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 border-0 flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back</span>
        </a>
    </div>

    {{-- Alert Validation Errors --}}
    @if ($errors->any())
        <div class="mb-6 bg-rose-50 dark:bg-rose-950/30 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 p-4 rounded-2xl text-xs shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400"></i>
                <span>An input error occurred:</span>
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
        <form action="{{ route('user.portofolios.update', $portofolio->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Select Skill --}}
            <div>
                <label for="skill_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Skill<span class="text-rose-500">*</span>
                </label>
                <select name="skill_id" id="skill_id" required
                        class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition @error('skill_id') border-rose-500 @enderror">
                    <option value="">-- Select Skill --</option>
                    @foreach($skills as $skill)
                        <option value="{{ $skill->id }}" {{ old('skill_id', $portofolio->skill_id) == $skill->id ? 'selected' : '' }}>
                            {{ $skill->name }}
                        </option>
                    @endforeach
                </select>
                @error('skill_id')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Select Type --}}
            <div>
                <label for="type" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Portfolio Type<span class="text-rose-500">*</span>
                </label>
                <select name="type" id="type" required
                        class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition @error('type') border-rose-500 @enderror">
                    <option value="certificate" {{ old('type', $portofolio->type) == 'certificate' ? 'selected' : '' }}>Certificate</option>
                    <option value="portfolio" {{ old('type', $portofolio->type) == 'portfolio' ? 'selected' : '' }}>Portfolio</option>
                    <option value="assessment" {{ old('type', $portofolio->type) == 'assessment' ? 'selected' : '' }}>Assessment</option>
                    <option value="endorsement" {{ old('type', $portofolio->type) == 'endorsement' ? 'selected' : '' }}>Endorsement</option>
                </select>
                @error('type')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Current File & Upload New File --}}
            <div>
                <label for="evidence" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    File Evidence
                </label>

                @if($portofolio->evidence)
                    <div class="mb-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl flex items-center justify-between border border-slate-200 dark:border-slate-700">
                        <span class="text-xs text-slate-600 dark:text-slate-300 flex items-center gap-2">
                            <i data-lucide="file-check" class="w-4 h-4 text-emerald-500"></i>
                            File currently saved
                        </span>
                        <a href="{{ asset('storage/' . $portofolio->evidence) }}" target="_blank" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-semibold">
                            View File
                        </a>
                    </div>
                @endif

                {{-- Input Evidence File --}}
<div x-data="{
    isDragging: false,
    fileName: '',
    fileSize: '',
    filePreview: null,
    errorMessage: '',
    maxSizeBytes: 5 * 1024 * 1024, // 5MB

    handleFile(file) {
        if (!file) return;

        // Validasi Ukuran File
        if (file.size > this.maxSizeBytes) {
            this.errorMessage = 'The file size exceeds the 5MB limit.';
            this.removeFile();
            return;
        }

        this.errorMessage = '';
        this.fileName = file.name;
        this.fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

        // Generate Preview untuk File Gambar
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => { this.filePreview = e.target.result; };
            reader.readAsDataURL(file);
        } else {
            this.filePreview = null;
        }

        // Sinkronisasi ke Input File
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        this.$refs.fileInput.files = dataTransfer.files;
    },

    handleDrop(event) {
        this.isDragging = false;
        const file = event.dataTransfer.files[0];
        if (file) {
            this.handleFile(file);
        }
    },

    removeFile() {
        this.fileName = '';
        this.fileSize = '';
        this.filePreview = null;
        this.$refs.fileInput.value = '';
    }
}">
    <!-- Label -->
    <label for="evidence" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
        Upload Proof <span class="text-slate-400 font-normal">(PDF, Image) — Optional, max. 5MB</span>
    </label>

    <!-- Area Drag & Drop -->
    <div @dragover.prevent="isDragging = true"
         @dragleave.prevent="isDragging = false"
         @drop.prevent="handleDrop($event)"
         @click="$refs.fileInput.click()"
         :class="isDragging ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800'"
         class="relative cursor-pointer rounded-2xl border-2 border-dashed p-6 text-center transition-all duration-200 hover:border-emerald-400 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20">

        <!-- Input File Hidden -->
        <input type="file"
               name="evidence"
               id="evidence"
               x-ref="fileInput"
               accept=".pdf,image/*"
               class="hidden"
               @change="handleFile($event.target.files[0])">

        <!-- Tampilan Saat Belum Ada File -->
        <template x-if="!fileName">
            <div>
                <div class="flex justify-center mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-950">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-emerald-600 dark:text-emerald-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5h10.5a2.25 2.25 0 002.25-2.25v-10.5A2.25 2.25 0 0017.25 4.5H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Drag files here</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    or <span class="font-semibold text-emerald-600 dark:text-emerald-400">Click to select a file</span>
                </p>
                <p class="mt-2 text-[11px] text-slate-400">PDF, JPG, JPEG, PNG • Maximal 5MB</p>
            </div>
        </template>

        <!-- Tampilan Saat File Sudah Dipilih -->
        <template x-if="fileName">
            <div class="flex items-center justify-between gap-4 text-left">
                <div class="flex min-w-0 items-center gap-3">
                    <!-- Icon / Thumbnail Preview -->
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-emerald-100 dark:bg-emerald-950">
                        <template x-if="filePreview">
                            <img :src="filePreview" alt="Preview" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!filePreview">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-emerald-600 dark:text-emerald-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A2.625 2.625 0 0112 5.625v-1.5A3.375 3.375 0 008.625.75H6.75A2.25 2.25 0 004.5 3v18a2.25 2.25 0 002.25 2.25h10.5A2.25 2.25 0 0019.5 21V14.25z" />
                            </svg>
                        </template>
                    </div>

                    <!-- Informasi File -->
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-700 dark:text-slate-200" x-text="fileName"></p>
                        <p class="text-xs text-slate-400" x-text="fileSize"></p>
                    </div>
                </div>

                <!-- Tombol Hapus File -->
                <button type="button"
                        @click.stop="removeFile()"
                        class="shrink-0 rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-500 dark:hover:bg-rose-950/30"
                        title="Hapus file">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Error Client-side (Ukuran Melebihi Batas) -->
    <template x-if="errorMessage">
        <p class="mt-1.5 text-xs font-medium text-rose-500" x-text="errorMessage"></p>
    </template>

    <!-- Error Server-side (Laravel Validation) -->
    @error('evidence')
        <p class="mt-1.5 text-xs font-medium text-rose-500">{{ $message }}</p>
    @enderror
</div>Leave blank if you do not wish to replace the proof file.</p>
                @error('evidence')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('user.portofolios.index') }}" class="btn btn-sm bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border-0">
                    Cancel
                </a>
                <button type="submit" class="btn btn-sm bg-emerald-600 text-white hover:bg-emerald-700 border-0 flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
