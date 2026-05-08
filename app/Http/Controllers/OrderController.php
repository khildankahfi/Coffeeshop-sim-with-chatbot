<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $status = request('status');

        $orders = \App\Models\Order::with('items.product')
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        $stats = [
            'total'   => \App\Models\Order::count(),
            'pending' => \App\Models\Order::where('status', 'pending')->count(),
            'done'    => \App\Models\Order::where('status', 'done')->count(),
            'revenue' => \App\Models\Order::where('status', '!=', 'cancelled')
                            ->whereDate('created_at', today())
                            ->sum('total_price'),
        ];

        return view('orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load('items.product');
        return view('orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,done,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('orders.index')->with('success', 'Status order diupdate.');
    }

    // Method wajib ada karena --resource, tapi tidak dipakai
    public function create() {}
    public function store(Request $request) {}
    public function edit(Order $order) {}
    public function destroy(Order $order) {}
}