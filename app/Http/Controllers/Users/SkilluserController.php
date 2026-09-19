<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkilluserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Mengambil hanya skill milik user yang sedang login
        $skills = $user->skills()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->latest('user_skills.created_at')
            ->paginate($perPage)
            ->appends([
                'search' => $search,
                'per_page' => $perPage
            ]);

        return view('users.skill.index', compact('skills'));
    }

    public function create()
    {
        return view('users.skill.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_id' => 'nullable|integer|exists:skills,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'proficiency_level' => 'required',
            'years_experience' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Nama skill wajib diisi.',
            'skill_id.exists' => 'Skill yang dipilih tidak valid.',
            'proficiency_level.required' => 'Tingkat kemahiran wajib dipilih.',
        ]);

        $user = Auth::user();

        if (!empty($validated['skill_id'])) {

            $skill = Skill::findOrFail($validated['skill_id']);

        } else {

            $skill = Skill::firstOrCreate(
                [
                    'name' => trim($validated['name'])
                ],
                [
                    'description' => $validated['description'] ?? null
                ]
            );
        }

        if ($user->skills()->where('skill_id', $skill->id)->exists()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Anda sudah memiliki skill ini.');
        }

        $user->skills()->attach($skill->id, [
            'proficiency_level' => $validated['proficiency_level'],
            'years_experience' => $validated['years_experience'] ?? 0,
            'is_verified' => false,
        ]);

        return redirect()
            ->route('user.myskill.index')
            ->with('success', 'Skill berhasil ditambahkan ke profil Anda.');
    }

    public function edit(Skill $skill)
    {
        $user = Auth::user();

        $userSkill = $user->skills()->where('skill_id', $skill->id)->firstOrFail();

        return view('users.skill.edit', [
            'skill' => $userSkill
        ]);
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'proficiency_level' => 'required|string|in:Beginner,Intermediate,Advanced,Expert',
            'years_experience' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Nama skill wajib diisi.',
        ]);

        $user = Auth::user();

        $skill->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        $user->skills()->updateExistingPivot($skill->id, [
            'proficiency_level' => $validated['proficiency_level'],
            'years_experience' => $validated['years_experience'] ?? 0,
        ]);

        return redirect()->route('user.myskill.index')->with('success', 'Skill berhasil diperbarui.');
    }

    public function destroy(Skill $skill)
    {
        $user = Auth::user();

        $user->skills()->detach($skill->id);

        return redirect()->route('user.myskill.index')->with('success', 'Skill berhasil dihapus dari profil Anda.');
    }

    public function destroyBulk(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);

        if (!empty($ids) && is_array($ids)) {
            $user = Auth::user();
            $user->skills()->detach($ids);

            return redirect()->route('user.myskill.index')->with('success', count($ids) . ' Skill terpilih berhasil dihapus.');
        }

        return redirect()->route('user.myskill.index')->with('error', 'Tidak ada data skill yang dipilih.');
    }

    public function search(Request $request)
    {
        $query = trim($request->input('query', ''));

        if ($query === '') {
            return response()->json([]);
        }

        $skills = Skill::query()
            ->where('name', 'like', '%' . $query . '%')
            ->orderBy('name')
            ->limit(10)
            ->get([
                'id',
                'name',
                'description',
            ]);

        return response()->json($skills);
    }
}
