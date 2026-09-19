<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Portofolio;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class PortofolioController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $portofolios = Portofolio::with(['skill', 'verifier'])
            ->where('user_id', Auth::id())
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('type', 'like', '%' . $search . '%')
                      ->orWhere('status', 'like', '%' . $search . '%')
                      ->orWhereHas('skill', function ($qSkill) use ($search) {
                          $qSkill->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->latest()
            ->paginate($perPage)
            ->appends([
                'search' => $search,
                'per_page' => $perPage,
            ]);

        return view('users.portofolio.index', compact('portofolios'));
    }

    public function create()
    {
        $skills = Skill::orderBy('name', 'asc')->get();

        return view('users.portofolio.create', compact('skills'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'type'     => 'required|in:certificate,portfolio,assessment,endorsement',
            'evidence' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip|max:5120', // Maks 5MB
        ], [
            'skill_id.required' => 'Skill/Mata pelajaran wajib dipilih.',
            'skill_id.exists'   => 'Skill yang dipilih tidak valid.',
            'type.required'     => 'Tipe portofolio wajib dipilih.',
            'type.in'           => 'Tipe portofolio tidak valid.',
            'evidence.mimes'    => 'Format bukti harus berupa PDF, JPG, JPEG, PNG, atau ZIP.',
            'evidence.max'      => 'Ukuran bukti maksimal 5MB.',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status']  = 'pending';

        if ($request->hasFile('evidence')) {
            $validated['evidence'] = $request->file('evidence')->store('portofolio_evidence', 'public');
        }

        Portofolio::create($validated);

        return redirect()->route('user.portofolios.index')
            ->with('success', 'Portofolio berhasil ditambahkan dan menunggu verifikasi.');
    }

    public function edit(Portofolio $portofolio)
    {
        if ($portofolio->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke portofolio ini.');
        }

        $skills = Skill::orderBy('name', 'asc')->get();

        return view('users.portofolio.edit', compact('portofolio', 'skills'));
    }

    public function update(Request $request, Portofolio $portofolio)
    {
        if ($portofolio->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke portofolio ini.');
        }

        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'type'     => 'required|in:certificate,portfolio,assessment,endorsement',
            'evidence' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip|max:5120',
        ], [
            'skill_id.required' => 'Skill/Mata pelajaran wajib dipilih.',
            'skill_id.exists'   => 'Skill yang dipilih tidak valid.',
            'type.required'     => 'Tipe portofolio wajib dipilih.',
            'type.in'           => 'Tipe portofolio tidak valid.',
            'evidence.mimes'    => 'Format bukti harus berupa PDF, JPG, JPEG, PNG, atau ZIP.',
            'evidence.max'      => 'Ukuran bukti maksimal 5MB.',
        ]);

        if ($request->hasFile('evidence')) {
            if ($portofolio->evidence && Storage::disk('public')->exists($portofolio->evidence)) {
                Storage::disk('public')->delete($portofolio->evidence);
            }
            $validated['evidence'] = $request->file('evidence')->store('portofolio_evidence', 'public');
        }

        $validated['status'] = 'pending';
        $validated['rejection_reason'] = null;

        $portofolio->update($validated);

        return redirect()->route('user.portofolios.index')
            ->with('success', 'Portofolio berhasil diperbarui.');
    }

    public function destroy(Portofolio $portofolio)
    {
        if ($portofolio->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke portofolio ini.');
        }

        if ($portofolio->evidence && Storage::disk('public')->exists($portofolio->evidence)) {
            Storage::disk('public')->delete($portofolio->evidence);
        }

        $portofolio->delete();

        return redirect()->route('user.portofolios.index')
            ->with('success', 'Portofolio berhasil dihapus.');
    }

    public function destroyBulk(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);

        if (!empty($ids) && is_array($ids)) {
            $portofolios = Portofolio::whereIn('id', $ids)
                ->where('user_id', Auth::id())
                ->get();

            foreach ($portofolios as $item) {
                if ($item->evidence && Storage::disk('public')->exists($item->evidence)) {
                    Storage::disk('public')->delete($item->evidence);
                }
                $item->delete();
            }

            return redirect()->route('user.portofolios.index')
                ->with('success', count($portofolios) . ' Portofolio terpilih berhasil dihapus.');
        }

        return redirect()->route('user.portofolios.index')
            ->with('error', 'Tidak ada data portofolio yang dipilih.');
    }
}
