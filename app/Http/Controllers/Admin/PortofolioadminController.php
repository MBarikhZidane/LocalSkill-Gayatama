<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portofolio;
use App\Models\Skill;
use Illuminate\Http\Request;

class PortofolioadminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $skillId = $request->input('skill_id');
        $status = $request->input('status');
        $perPage = $request->input('per_page', 10);

        // Ambil semua daftar skill untuk opsi dropdown filter
        $skills = Skill::orderBy('name', 'asc')->get();

        // Query data portofolio
        $portofolios = Portofolio::with(['user', 'skill', 'verifier'])
            // Filter berdasarkan skill
            ->when($skillId, function ($query, $skillId) {
                return $query->where('skill_id', $skillId);
            })
            // Filter berdasarkan status (opsional)
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            // Filter berdasarkan kata kunci pencarian (tipe, nama user, atau nama skill)
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('type', 'like', '%' . $search . '%')
                      ->orWhere('evidence', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($u) use ($search) {
                          $u->where('name', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('skill', function ($s) use ($search) {
                          $s->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->latest()
            ->paginate($perPage)
            // Mempertahankan query string saat ganti halaman pagination
            ->appends([
                'search' => $search,
                'skill_id' => $skillId,
                'status' => $status,
                'per_page' => $perPage,
            ]);

        return view('admin.portofolios.index', compact('portofolios', 'skills'));
    }

    public function destroy(Portofolio $portofolio)
    {
        $portofolio->delete();

        return redirect()->route('portofolios.index')->with('success', 'Data portofolio berhasil dihapus.');
    }

    /**
     * Hapus banyak data sekaligus (Bulk Delete)
     */
    public function destroyBulk(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);

        if (!empty($ids) && is_array($ids)) {
            Portofolio::whereIn('id', $ids)->delete();

            return redirect()->route('portofolios.index')->with('success', count($ids) . ' Portofolio terpilih berhasil dihapus.');
        }

        return redirect()->route('portofolios.index')->with('error', 'Tidak ada data portofolio yang dipilih.');
    }
}
