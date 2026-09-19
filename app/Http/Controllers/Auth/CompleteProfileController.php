<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudyProgram;
use App\Models\University;
use Illuminate\Support\Facades\Auth;

class CompleteProfileController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        // Jika user sudah melengkapi data kampus, langsung arahkan ke dashboard
        if ($user->university_id && $user->study_program_id) {
            return redirect()->intended('/explore.index');
        }

        $universities = University::all();
        $studyPrograms = StudyProgram::all();

        return view('auth.complete-profile', compact('universities', 'studyPrograms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone'            => ['nullable', 'string', 'max:20'],
            'bio'              => ['nullable', 'string', 'max:500'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'university_id'    => $request->university_id,
            'study_program_id' => $request->study_program_id,
            'phone'            => $request->phone,
            'bio'              => $request->bio,
        ]);

        return redirect()->intended('/explore');
    }
}
