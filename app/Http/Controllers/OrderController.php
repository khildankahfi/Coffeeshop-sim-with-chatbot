<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $status = request('status');

        $orders = Order::with('items.product')
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        $stats = [
            'total'   => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'done'    => Order::where('status', 'done')->count(),
            'revenue' => Order::where('status', '!=', 'cancelled')
                            ->whereDate('created_at', today())
                            ->sum('total_price'),
        ];

        return view('orders.index', compact('orders', 'stats'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,done,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('admin.orders.index')->with('success', 'Status order diupdate.');
    }

    public function show(Order $order) {}
    public function create() {}
    public function store(Request $request) {}
    public function edit(Order $order) {}
    public function destroy(Order $order) {}
}