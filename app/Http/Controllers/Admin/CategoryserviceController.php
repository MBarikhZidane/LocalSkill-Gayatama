<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkillCategory;
use Illuminate\Http\Request;
use index;

class CategoryserviceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $categories = SkillCategory::when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate($perPage)
            ->appends([
                'search' => $search,
                'per_page' => $perPage
            ]);

        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama kategori skill wajib diisi.',
        ]);

        SkillCategory::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori Skill berhasil ditambahkan.');
    }

    public function edit(SkillCategory $skillCategory)
    {
        return view('admin.category.edit', compact('skillCategory'));
    }

    public function update(Request $request, SkillCategory $skillCategory)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama kategori skill wajib diisi.',
        ]);

        $skillCategory->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori Skill berhasil diperbarui.');
    }

    public function destroy(SkillCategory $skillCategory)
    {
        $skillCategory->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori Skill berhasil dihapus.');
    }

    public function destroyBulk(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);

        if (!empty($ids) && is_array($ids)) {
            SkillCategory::whereIn('id', $ids)->delete();

            return redirect()->route('admin.categories.index')->with('success', count($ids) . ' Kategori Skill terpilih berhasil dihapus.');
        }

        return redirect()->route('admin.categories.index')->with('error', 'Tidak ada data kategori skill yang dipilih.');
    }
}
