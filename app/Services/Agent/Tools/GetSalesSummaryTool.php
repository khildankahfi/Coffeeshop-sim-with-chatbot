<?php

namespace App\Services\Agent\Tools;

use Illuminate\Support\Facades\DB;

class GetSalesSummaryTool
{
    public function getDefinition(): array
    {
        return [
            'name'        => 'get_sales_summary',
            'description' => 'Ambil ringkasan penjualan: total order, revenue, produk terlaris.',
            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'period' => [
                        'type'        => 'string',
                        'enum'        => ['today', 'this_week', 'this_month'],
                        'description' => 'Periode laporan.',
                    ],
                ],
                'required' => ['period'],
            ],
        ];
    }

    public function execute(array $input): array
    {
        // Tentukan rentang tanggal berdasarkan periode
        $range = match($input['period']) {
            'today'      => [now()->startOfDay(),   now()->endOfDay()],
            'this_week'  => [now()->startOfWeek(),  now()->endOfWeek()],
            'this_month' => [now()->startOfMonth(), now()->endOfMonth()],
            default      => [now()->startOfDay(),   now()->endOfDay()],
        };

        // Total order & revenue
        $summary = DB::table('orders')
            ->whereBetween('created_at', $range)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('COUNT(*) as total_orders, SUM(total_price) as total_revenue')
            ->first();

        // Top 5 produk terlaris
        $topProducts = DB::table('order_items')
            ->join('orders',   'orders.id',   '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereBetween('orders.created_at', $range)
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('products.name, SUM(order_items.qty) as total_qty, SUM(order_items.qty * order_items.unit_price) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return [
            'period'       => $input['period'],
            'total_orders' => $summary->total_orders ?? 0,
            'total_revenue'=> $summary->total_revenue ?? 0,
            'top_products' => $topProducts,
        ];
    }
}