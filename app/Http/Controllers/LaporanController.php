<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        // Tentukan range tanggal
        [$startDate, $endDate] = match($period) {
            'week'  => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            'month' => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            'all'   => [Carbon::create(2000), now()->endOfDay()],
            default => [now()->startOfDay(), now()->endOfDay()],
        };

        // Stats utama
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                       ->where('status', '!=', 'cancelled');

        $stats = [
            'revenue'        => (clone $orders)->sum('total_price'),
            'total_orders'   => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'done_orders'    => (clone $orders)->where('status', 'done')->count(),
            'pending_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
            'total_items'    => OrderItem::whereHas('order', fn($q) =>
                $q->whereBetween('created_at', [$startDate, $endDate])->where('status', '!=', 'cancelled')
            )->sum('qty'),
            'avg_rating'     => Feedback::avg('rating') ?? 0,
            'total_feedback' => Feedback::count(),
        ];

        // Status distribution
        $statusData = [
            'pending'    => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
            'processing' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'processing')->count(),
            'done'       => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'done')->count(),
            'cancelled'  => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'cancelled')->count(),
        ];

        // Top 5 menu terlaris
        $topMenus = DB::table('order_items')
            ->join('orders',   'orders.id',   '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('products.name, SUM(order_items.qty) as total_qty')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Data grafik revenue per hari
        $days = $period === 'today' ? 1 : ($period === 'week' ? 7 : ($period === 'month' ? 30 : 30));
        $chartLabels = [];
        $chartData   = [];

        for ($i = min($days, 30) - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $chartData[]   = Order::whereDate('created_at', $date)
                ->where('status', '!=', 'cancelled')
                ->sum('total_price');
        }

        // Rating distribution
        $ratingDist = Feedback::selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // Recent feedback
        $recentFeedback = Feedback::latest()->limit(5)->get();

        return view('laporan.index', compact(
            'stats', 'statusData', 'topMenus',
            'chartLabels', 'chartData', 'period',
            'ratingDist', 'recentFeedback'
        ));
    }
}