<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\SkillCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    public function index(Request $request)
    {
        $search   = $request->input('search');
        $status   = $request->input('status');
        $perPage  = $request->input('per_page', 10);

        $services = Service::with(['user', 'category'])
            // Filter Berdasarkan Kata Kunci (Judul, Deskripsi, Nama Pengguna)
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            // Filter Berdasarkan Status (draft, active, inactive)
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate($perPage)
            ->appends([
                'search'   => $search,
                'status'   => $status,
                'per_page' => $perPage,
            ]);

        return view('admin.services.index', compact('services'));
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus.');
    }

    /**
     * Hapus banyak data sekaligus (Bulk Delete)
     */
    public function destroyBulk(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);

        if (!empty($ids) && is_array($ids)) {
            Service::whereIn('id', $ids)->delete();

            return redirect()->route('admin.services.index')->with('success', count($ids) . ' Layanan terpilih berhasil dihapus.');
        }

        return redirect()->route('admin.services.index')->with('error', 'Tidak ada data layanan yang dipilih.');
    }

}
