<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderuserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = min(50, max(1, (int) $request->input('per_page', 10)));

        $orders = Order::with(['customer', 'service'])
            ->where('provider_id', Auth::id())
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', '%'.$search.'%')
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('service', function ($serviceQuery) use ($search) {
                            $serviceQuery->where('title', 'like', '%'.$search.'%');
                        });
                });
            })
            ->latest()
            ->paginate($perPage)
            ->appends([
                'search' => $search,
                'per_page' => $perPage,
            ]);

        return view('users.orders.index', compact('orders'));
    }

    public function edit(Order $order)
    {
        OrderWorkflow::authorize($order, (int) Auth::id());

        return redirect()->route('user.workflow.show', $order);
    }

    public function update(Request $request, Order $order)
    {
        OrderWorkflow::authorize($order, (int) Auth::id());
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Use the order workflow actions.'], 409);
        }

        return redirect()->route('user.workflow.show', $order)->with('error', 'Use the order workflow actions.');
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

        if (! empty($ids) && is_array($ids)) {
            Order::whereIn('id', $ids)
                ->where('provider_id', Auth::id())
                ->delete();

            return redirect()->route('user.orders.index')->with('success', count($ids).' Pesanan terpilih berhasil dihapus.');
        }

        return redirect()->route('user.orders.index')->with('error', 'Tidak ada data pesanan yang dipilih.');
    }
}
