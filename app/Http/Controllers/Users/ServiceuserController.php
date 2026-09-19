<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceuserController extends Controller
{
     public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Membatasi query hanya untuk user yang sedang login
        $services = Service::with('category')
            ->where('user_id', Auth::id())
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate($perPage)
            ->appends([
                'search' => $search,
                'per_page' => $perPage
            ]);

        return view('users.services.index', compact('services'));
    }

    public function create()
    {
        $categories = SkillCategory::orderBy('name', 'asc')->get();
        return view('users.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'    => 'required|exists:skill_categories,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'estimated_days' => 'required|integer|min:1',
            'status'         => 'required|in:active,inactive',
        ], [
            'category_id.required' => 'Kategori layanan wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
            'title.required'       => 'Judul layanan wajib diisi.',
            'price.required'       => 'Harga layanan wajib diisi.',
            'estimated_days.required' => 'Estimasi pengerjaan wajib diisi.',
        ]);

        // Masukkan ID user yang sedang login secara otomatis
        $validated['user_id'] = Auth::id();

        Service::create($validated);

        return redirect()->route('user.services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Service $service)
    {
        // Pengecekan otorisasi manual agar user lain tidak bisa mengedit via URL
        if ($service->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke layanan ini.');
        }

        $categories = SkillCategory::orderBy('name', 'asc')->get();
        return view('users.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        if ($service->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke layanan ini.');
        }

        $validated = $request->validate([
            'category_id'    => 'required|exists:skill_categories,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'estimated_days' => 'required|integer|min:1',
            'status'         => 'required|in:active,inactive',
        ]);

        $service->update($validated);

        return redirect()->route('user.services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $service)
    {
        if ($service->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke layanan ini.');
        }

        $service->delete();

        return redirect()->route('user.services.index')->with('success', 'Layanan berhasil dihapus.');
    }

    public function destroyBulk(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);

        if (!empty($ids) && is_array($ids)) {
            // Hanya hapus data yang dimiliki oleh user yang sedang login
            Service::whereIn('id', $ids)
                ->where('user_id', Auth::id())
                ->delete();

            return redirect()->route('user.services.index')->with('success', count($ids) . ' Layanan terpilih berhasil dihapus.');
        }

        return redirect()->route('user.services.index')->with('error', 'Tidak ada data layanan yang dipilih.');
    }
}
