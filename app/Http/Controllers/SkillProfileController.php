<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SkillProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('users.skill-profile', ['user' => $request->user()->load(['skills', 'portofolios.skill'])]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'professional_title' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'levels' => 'sometimes|array',
            'levels.*' => 'required|integer|min:0|max:100',
        ]);
        $user = $request->user();
        $ownedIds = $user->skills()->pluck('skills.id')->map(fn ($id): string => (string) $id)->all();
        foreach (array_keys($validated['levels'] ?? []) as $id) {
            if (! in_array((string) $id, $ownedIds, true)) {
                throw ValidationException::withMessages(['levels' => 'Only your own skill levels can be updated.']);
            }
        }
        DB::transaction(function () use ($user, $validated): void {
            $user->update(collect($validated)->only(['name', 'professional_title', 'bio'])->all());
            foreach ($validated['levels'] ?? [] as $id => $level) {
                $user->skills()->updateExistingPivot($id, ['proficiency_level' => max(1, (int) ceil($level / 25)), 'proficiency_percent' => $level]);
            }
        });

        return back()->with('success', 'Your skill profile has been saved.');
    }

    public function storePortfolio(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'skill_id' => 'required|integer',
            'title' => 'required|string|max:100',
            'project_type' => 'required|string|max:60',
            'description' => 'required|string|min:10|max:500',
        ]);
        $request->user()->skills()->findOrFail($validated['skill_id']);
        $request->user()->portofolios()->create($validated + ['type' => 'portfolio']);

        return back()->with('success', 'Portfolio project added.');
    }
}
