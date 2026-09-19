<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\University;
use App\Models\StudyProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search        = $request->input('search');
        $universityId  = $request->input('university_id');
        $perPage       = $request->input('per_page', 10);

        $users = User::with(['university', 'studyProgram'])
            // Filter Pencarian (Nama, Email, Nomor Telepon)
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('phone', 'like', '%' . $search . '%');
                });
            })
            // Filter berdasarkan Universitas
            ->when($universityId, function ($query, $universityId) {
                return $query->where('university_id', $universityId);
            })
            ->latest()
            ->paginate($perPage)
            ->appends([
                'search'        => $search,
                'university_id' => $universityId,
                'per_page'      => $perPage,
            ]);

        $universities = University::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'universities'));
    }

    public function create()
    {
        $universities   = University::orderBy('name')->get();
        $studyPrograms  = StudyProgram::orderBy('name')->get();

        return view('admin.users.create', compact('universities', 'studyPrograms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users',
            'password'         => 'required|string|min:8',
            'phone'            => 'nullable|string|max:20',
            'university_id'    => 'nullable|exists:universities,id',
            'study_program_id' => 'nullable|exists:study_programs,id',
            'bio'              => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->load(['university', 'studyProgram']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $universities  = University::orderBy('name')->get();
        $studyPrograms = StudyProgram::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'universities', 'studyPrograms'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password'         => 'nullable|string|min:8',
            'phone'            => 'nullable|string|max:20',
            'university_id'    => 'nullable|exists:universities,id',
            'study_program_id' => 'nullable|exists:study_programs,id',
            'bio'              => 'nullable|string',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Hapus banyak data sekaligus (Bulk Delete)
     */
    public function destroyBulk(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);

        if (!empty($ids) && is_array($ids)) {
            User::whereIn('id', $ids)->delete();

            return redirect()->route('admin.users.index')->with('success', count($ids) . ' User terpilih berhasil dihapus.');
        }

        return redirect()->route('admin.users.index')->with('error', 'Tidak ada data user yang dipilih.');
    }
}
