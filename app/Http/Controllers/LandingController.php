<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Models\Service;
use App\Models\University;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    public function show($id)
    {
        $service = Service::with([
            'user.skills',
            'user.university',
            'user.location',
            'user.portofolios.skill',
            'category',
            'reviews.reviewer',
        ])->findOrFail($id);

        $avgRating = $service->reviews->avg('rating') ?? 0;
        $totalReviews = $service->reviews->count();

        $canReview = false;
        $userOrder = null;

        if (Auth::check()) {
            $userOrder = Order::where('service_id', $service->id)
                ->where('customer_id', Auth::id())
                ->where('status', 'completed')
                ->latest()
                ->first();

            if ($userOrder) {
                $alreadyReviewed = Review::where('order_id', $userOrder->id)
                    ->where('reviewer_id', Auth::id())
                    ->exists();

                $canReview = ! $alreadyReviewed;
            }
        }

        return view('users.detail-service', compact('service', 'avgRating', 'totalReviews', 'canReview', 'userOrder'));
    }

    public function storeReview(Request $request, Service $service)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Ganti buyer_id menjadi customer_id
        if ($order->customer_id !== Auth::id() || $order->service_id !== $service->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk memberikan ulasan pada pesanan ini.');
        }

        Review::create([
            'order_id' => $order->id,
            'reviewer_id' => Auth::id(),
            'reviewee_id' => $service->user_id ?? $service->provider_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Ulasan dan komentar berhasil ditambahkan!');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $view = in_array($request->input('view'), ['selling', 'providing'], true) ? 'selling' : 'buying';
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $query = Order::with(['service', 'provider', 'customer']);

        if ($view === 'selling') {
            $query->where('provider_id', $user->id);
        } else {
            $query->where('customer_id', $user->id);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search, $view) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('service', function ($s) use ($search) {
                        $s->where('title', 'like', "%{$search}%");
                    });

                if ($view === 'buying') {
                    $q->orWhereHas('provider', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    });
                } else {
                    $q->orWhereHas('customer', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    });
                }
            });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('users.my-orders', compact('orders', 'view', 'status', 'search'));
    }

    public function cancel(Order $order)
    {
        // Pastikan order milik user yang sedang login dan statusnya 'pending'
        if ($order->customer_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk membatalkan pesanan ini.');
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Pesanan hanya dapat dibatalkan jika statusnya masih Pending.');
        }

        $order->update([
            'status' => 'cancelled',
        ]);

        // 🔔 KIRIM NOTIFIKASI KE PROVIDER
        if ($order->provider) {
            $order->provider->notify(new OrderStatusChanged($order, 'cancelled'));
        }

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function viewprofile($id)
    {
        $user = User::with([
            'university',
            'studyProgram',
            'skills',
            'portofolios.skill',
            'services.category',
            'location',
        ])->findOrFail($id);

        $providerOrderIds = Order::where('provider_id', $user->id)->pluck('id');

        $totalReviews = Review::whereIn('order_id', $providerOrderIds)->count();
        $avgRating = Review::whereIn('order_id', $providerOrderIds)->avg('rating') ?? 0;

        $completedOrdersCount = Order::where('provider_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $reviews = Review::with(['reviewer', 'order.service'])
            ->whereIn('order_id', $providerOrderIds)
            ->latest()
            ->take(5)
            ->get();

        return view('users.profile', compact(
            'user',
            'totalReviews',
            'avgRating',
            'completedOrdersCount',
            'reviews'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'brief' => 'required|string|min:20|max:2000',
        ]);

        $service = Service::findOrFail($request->service_id);

        abort_unless($service->status === 'active' && $service->user_id !== Auth::id(), 403);

        $price = $service->price;
        $platformFee = 0;
        $totalAmount = $price + $platformFee;

        $order = Order::create([
            'order_number' => 'ORD-'.strtoupper(Str::random(8)),
            'customer_id' => Auth::id(),
            'provider_id' => $service->user_id,
            'service_id' => $service->id,
            'price' => $price,
            'platform_fee' => $platformFee,
            'total_amount' => $totalAmount,
            'scheduled_date' => $request->scheduled_date,
            'brief' => $request->brief,
            'status' => 'pending',
        ]);

        if ($order->provider) {
            $order->provider->notify(new OrderStatusChanged($order, 'new_order'));
        }

        return back()->with('success', 'Booking request sent successfully!');
    }

    public function edit()
    {
        $user = Auth::user();
        $universities = University::all();

        return view('users.setting', compact('user', 'universities'));
    }

    /**
     * Update data profil user.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update($request->only([
            'name',
            'email',
            'phone',
            'university_id',
            'study_program_id',
            'bio',
        ]));

        return redirect()->route('user.profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Mendaftar / Upgrade role menjadi provider.
     */
    public function registerProvider(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'provider') {
            return redirect()->route('user.profile.edit')->with('error', 'You are already registered as a provider.');
        }

        // Ubah role user menjadi provider
        $user->update([
            'role' => 'provider',
        ]);

        return redirect()->route('user.profile.edit')->with('success', 'Congratulations! You are now registered as a Provider.');
    }

    /**
     * Handle Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login')->with('success', 'You have been logged out successfully.');
    }
}
