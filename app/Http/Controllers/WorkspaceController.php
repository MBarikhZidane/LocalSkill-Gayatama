<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\SkillCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load(['university', 'skills', 'services.category']);

        return view('users.dashboard', [
            'user' => $user,
            'myServices' => $user->services,
            'categories' => SkillCategory::orderBy('name')->get(),
            'activeOrders' => $user->customerOrders()->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'completedJobs' => $user->providerOrders()->where('status', 'completed')->count(),
            'buyingCount' => $user->customerOrders()->count(),
            'providingCount' => $user->providerOrders()->count(),
        ]);
    }

    public function admin(): View
    {
        return view('admin.campus', [
            'verifiedStudents' => User::whereNotNull('email_verified_at')->count(),
            'activeSkills' => Service::where('status', 'active')->count(),
            'pendingServices' => Service::with('user')->where('status', 'draft')->latest()->paginate(10),
        ]);
    }

    public function moderate(Request $request, Service $service): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $validated = $request->validate([
            'decision' => 'required|in:approve,request_changes,reject',
            'reason' => 'required|string|max:1000',
        ]);
        $service->update([
            'status' => $validated['decision'] === 'approve' ? 'active' : 'inactive',
            'moderation_note' => $validated['reason'],
        ]);

        return back()->with('success', 'Listing decision saved.');
    }
}
