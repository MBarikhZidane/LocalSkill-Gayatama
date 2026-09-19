<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        // Ambil parameter per_page, default ke 10 jika tidak ada
        $perPage = $request->input('per_page', 10);

        $skills = Skill::when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate($perPage)
            // Mempertahankan kueri search dan per_page saat berpindah halaman
            ->appends([
                'search' => $search,
                'per_page' => $perPage
            ]);

        return view('admin.skills.index', compact('skills'));
    }

      public function edit(Skill $skill)
    {
        // Mengambil seluruh kategori untuk pilihan dropdown
        $categories = SkillCategory::orderBy('name', 'asc')->get();

        return view('admin.skills.edit', compact('skill', 'categories'));
    }

    /**
     * Memperbarui data di database.
     */
    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required'        => 'Nama mata pelajaran / skill wajib diisi.',
        ]);

        $skill->update($validated);

        return redirect()->route('admin.skills.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function create()
    {
        // Mengambil seluruh kategori untuk pilihan dropdown
        $categories = SkillCategory::orderBy('name', 'asc')->get();

        return view('admin.skills.create', compact('categories'));
    }

    /**
     * Menyimpan data baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required'        => 'Nama mata pelajaran / skill wajib diisi.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
        ]);

        Skill::create($validated);

        return redirect()->route('admin.skills.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return redirect()->route('admin.skills.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }

    /**
     * Hapus banyak data sekaligus (Bulk Delete)
     */
    public function destroyBulk(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);

        if (!empty($ids) && is_array($ids)) {
            Skill::whereIn('id', $ids)->delete();

            return redirect()->route('admin.skills.index')->with('success', count($ids) . ' Mata Pelajaran terpilih berhasil dihapus.');
        }

        return redirect()->route('admin.skills.index')->with('error', 'Tidak ada data mata pelajaran yang dipilih.');
    }
}
