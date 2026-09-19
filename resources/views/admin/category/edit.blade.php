@extends('layouts.app')
@section('title', 'Edit Category')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header Section --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Edit Skill Category
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Modify existing skill category data.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 border-0 flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back</span>
        </a>
    </div>

    {{-- Alert Validation Errors --}}
    @if ($errors->any())
        <div class="mb-6 bg-rose-50 dark:bg-rose-950/30 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 p-4 rounded-2xl text-xs shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400"></i>
                <span>Input errors occurred:</span>
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
        <form action="{{ route('admin.categories.update', $skillCategory->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Input Nama Kategori --}}
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Skill Category Name <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name', $skillCategory->name) }}"
                       placeholder="e.g., Programming, Graphic Design, Languages..."
                       class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition @error('name') border-rose-500 @enderror"
                       required>
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Deskripsi --}}
            <div>
                <label for="description" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Description <span class="text-slate-400 font-normal">(Optional)</span>
                </label>
                <textarea name="description"
                          id="description"
                          rows="4"
                          placeholder="Add a brief description regarding this category..."
                          class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">{{ old('description', $skillCategory->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-sm bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 border-0">
                    Cancel
                </a>
                <button type="submit" class="btn btn-sm bg-emerald-600 text-white hover:bg-emerald-700 border-0 flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Update Data</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
