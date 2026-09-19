<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $status   = $request->input('status');
        $perPage  = $request->input('per_page', 10);

        $orders = Order::with(['customer', 'provider', 'service', 'serviceRequest'])
            // Filter Berdasarkan Pencarian Nomor Order atau Nama Customer/Provider
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', '%' . $search . '%')
                      ->orWhereHas('customer', function ($cq) use ($search) {
                          $cq->where('name', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('provider', function ($pq) use ($search) {
                          $pq->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            // Filter Berdasarkan Status
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate($perPage)
            ->appends([
                'search'   => $search,
                'status'   => $status,
                'per_page' => $perPage,
            ]);

        // Daftar opsi status yang tersedia sesuai skema migrasi
        $statuses = [
            'pending'     => 'Pending',
            'accepted'    => 'Accepted',
            'in_progress' => 'In Progress',
            'submitted'   => 'Submitted',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
            'disputed'    => 'Disputed',
        ];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'provider', 'service', 'serviceRequest']);

        return view('admin.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    /**
     * Hapus banyak data sekaligus (Bulk Delete)
     */
    public function destroyBulk(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);

        if (!empty($ids) && is_array($ids)) {
            Order::whereIn('id', $ids)->delete();

            return redirect()->route('admin.orders.index')->with('success', count($ids) . ' Pesanan terpilih berhasil dihapus.');
        }

        return redirect()->route('admin.orders.index')->with('error', 'Tidak ada data pesanan yang dipilih.');
    }
}
