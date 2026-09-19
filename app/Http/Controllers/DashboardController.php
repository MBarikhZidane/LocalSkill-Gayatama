<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\SkillCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Total Statistik Utama
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalServices = Service::count();
        $totalCategories = SkillCategory::count();

        // 2. Ringkasan Status Pesanan
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // 3. Ringkasan Keuangan Platform
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $totalPlatformFee = Order::where('status', 'completed')->sum('platform_fee');

        // 4. Ambil 5 Pesanan Terbaru beserta Relasinya
        $recentOrders = Order::with(['customer', 'provider', 'service'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalOrders',
            'totalServices',
            'totalCategories',
            'pendingOrders',
            'completedOrders',
            'cancelledOrders',
            'totalRevenue',
            'totalPlatformFee',
            'recentOrders'
        ));
    }

    public function dashboarduser()
    {
        $user = Auth::user()->load([
            'university',
            'studyProgram',
            'skills',
            'location'
        ]);

        $myServices = $user->services()
            ->latest()
            ->get();

        $totalMyServices = $myServices->count();
        $totalIncomingOrders = $user->providerOrders()->count();
        $totalCustomerOrders = $user->skills()->count();
        $totalPortfolios = $user->portofolios()->count();

        $pendingIncomingOrders = $user->providerOrders()->where('status', 'pending')->count();
        $completedIncomingOrders = $user->providerOrders()->where('status', 'completed')->count();

        $incomingOrders = $user->providerOrders()
            ->with(['customer', 'service'])
            ->latest()
            ->take(5)
            ->get();

        return view('users.dashboard', compact(
            'user',
            'myServices',
            'totalMyServices',
            'totalIncomingOrders',
            'totalCustomerOrders',
            'totalPortfolios',
            'pendingIncomingOrders',
            'completedIncomingOrders',
            'incomingOrders'
        ));
    }
}
