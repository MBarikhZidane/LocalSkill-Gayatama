<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\SkillCategory;
use Illuminate\Http\Request;

class ExplorController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input filter dari request
        $filters = $request->only([
            'q', 'category_id', 'location', 
            'min_price', 'max_price', 'min_rating', 'sort'
        ]);

        // 2. Inisialisasi Query dasar dengan Eager Loading
        $query = Service::with(['user.university', 'user.location', 'category'])
            ->where('status', 'active');

        // 3. Filter Kata Kunci (Judul, Deskripsi, atau Nama User)
        if (!empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        // 4. Filter Kategori
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // 5. Filter Range Harga
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // 6. Filter Lokasi / Kampus
        if (!empty($filters['location'])) {
            $location = $filters['location'];
            $query->where(function ($q) use ($location) {
                $q->whereHas('user.location', function ($l) use ($location) {
                    $l->where('address', 'like', "%{$location}%");
                })->orWhereHas('user.university', function ($u) use ($location) {
                    $u->where('name', 'like', "%{$location}%")
                      ->orWhere('city', 'like', "%{$location}%");
                });
            });
        }

        // 7. Hitung Agregasi (Selesai Order & Rata-rata Rating)
        $query->withCount(['orders as completed_orders_count' => function ($q) {
            $q->where('status', 'completed');
        }])
        ->withAvg('reviews as average_rating', 'rating');

        // 8. Filter Minimal Rating
        if (!empty($filters['min_rating'])) {
            $query->having('average_rating', '>=', $filters['min_rating']);
        }

        // 9. Pengurutan Data
        $sort = $filters['sort'] ?? 'latest';
        match ($sort) {
            'price_low'  => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'popular'    => $query->orderByDesc('completed_orders_count'),
            'rating'     => $query->orderByDesc('average_rating'),
            default      => $query->latest(),
        };

        // 10. Eksekusi Pagination
        $services = $query->paginate(9)->withQueryString();

        // 11. Ambil data kategori untuk pilihan dropdown di view
        $categories = SkillCategory::all();

        return view('users.explore', compact('services', 'categories', 'filters'));
    }
}
