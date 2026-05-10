<?php

namespace App\Services\Agent\Tools;

use App\Models\Order;

class GetOrderHistoryTool
{
    public function getDefinition(): array
    {
        return [
            'name'        => 'get_order_history',
            'description' => 'Ambil riwayat pesanan pelanggan berdasarkan nama. Pelanggan bisa cek status pesanan mereka.',
            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'customer_name' => [
                        'type'        => 'string',
                        'description' => 'Nama pelanggan yang ingin cek riwayat pesanan.',
                    ],
                    'limit' => [
                        'type'        => 'integer',
                        'description' => 'Jumlah pesanan yang ditampilkan. Default 5.',
                    ],
                ],
                'required' => ['customer_name'],
            ],
        ];
    }

    public function execute(array $input): array
    {
        $limit  = $input['limit'] ?? 5;
        $name   = $input['customer_name'];

        $orders = Order::with('items.product')
            ->whereRaw('LOWER(customer_name) LIKE ?', ['%' . strtolower($name) . '%'])
            ->latest()
            ->limit($limit)
            ->get();

        if ($orders->isEmpty()) {
            return [
                'found'   => false,
                'message' => "Tidak ada riwayat pesanan untuk nama '{$name}'.",
            ];
        }

        $result = $orders->map(fn($order) => [
            'order_code'    => $order->order_code,
            'status'        => $order->status,
            'status_label'  => match($order->status) {
                'pending'    => '⏳ Menunggu',
                'processing' => '🔄 Diproses',
                'done'       => '✅ Selesai',
                'cancelled'  => '❌ Dibatalkan',
                default      => $order->status,
            },
            'total'         => $order->total_price,
            'total_formatted' => 'Rp ' . number_format($order->total_price, 0, ',', '.'),
            'items'         => $order->items->map(fn($item) => [
                'name' => $item->product->name ?? '-',
                'qty'  => $item->qty,
                'subtotal' => 'Rp ' . number_format($item->qty * $item->unit_price, 0, ',', '.'),
            ]),
            'tanggal' => $order->created_at->format('d M Y, H:i'),
        ]);

        return [
            'found'         => true,
            'customer_name' => $name,
            'total_orders'  => $orders->count(),
            'orders'        => $result,
        ];
    }
}