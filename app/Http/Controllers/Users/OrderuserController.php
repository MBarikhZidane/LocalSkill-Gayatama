<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderuserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $orders = Order::with(['customer', 'service'])
            ->where('provider_id', Auth::id())
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('service', function ($serviceQuery) use ($search) {
                            $serviceQuery->where('title', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->paginate($perPage)
            ->appends([
                'search' => $search,
                'per_page' => $perPage
            ]);

        return view('users.orders.index', compact('orders'));
    }

    public function edit(Order $order)
    {
        // Memastikan order yang diedit milik provider yang sedang login
        if ($order->provider_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];

        return view('users.orders.edit', compact('order', 'statuses'));
    }

    public function update(Request $request, Order $order)
    {
        if ($order->provider_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,in_progress,completed,cancelled',
        ], [
            'status.required' => 'Status pesanan wajib dipilih.',
            'status.in' => 'Status pesanan tidak valid.',
        ]);

        $oldStatus = $order->status;
        $order->update($validated);

        // 🔔 2. KIRIM NOTIFIKASI KE CUSTOMER JIKA STATUS BERUBAH
        if ($oldStatus !== $request->status && $order->customer) {
            $order->customer->notify(new OrderStatusChanged($order, $request->status));
        }

        return redirect()->route('user.orders.index')->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy(Order $order)
    {
        if ($order->provider_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $order->delete();

        return redirect()->route('user.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    public function destroyBulk(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);

        if (!empty($ids) && is_array($ids)) {
            Order::whereIn('id', $ids)
                ->where('provider_id', Auth::id())
                ->delete();

            return redirect()->route('user.orders.index')->with('success', count($ids) . ' Pesanan terpilih berhasil dihapus.');
        }

        return redirect()->route('user.orders.index')->with('error', 'Tidak ada data pesanan yang dipilih.');
    }
}
