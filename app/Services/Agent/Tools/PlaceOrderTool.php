<?php

namespace App\Services\Agent\Tools;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PlaceOrderTool
{
    public function getDefinition(): array
    {
        return [
            'name'        => 'place_order',
            'description' => 'Buat order baru setelah pelanggan konfirmasi. Hitung total otomatis berdasarkan harga x qty.',
            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'customer_name' => [
                        'type'        => 'string',
                        'description' => 'Nama pelanggan.',
                    ],
                    'items' => [
                        'type'  => 'array',
                        'items' => [
                            'type'       => 'object',
                            'properties' => [
                                'product_id' => ['type' => 'integer', 'description' => 'ID produk dari get_menu()'],
                                'qty'        => ['type' => 'integer', 'minimum' => 1, 'description' => 'Jumlah item'],
                            ],
                            'required' => ['product_id', 'qty'],
                        ],
                        'description' => 'Daftar item yang dipesan.',
                    ],
                    'notes' => [
                        'type'        => 'string',
                        'description' => 'Catatan tambahan dari pelanggan.',
                    ],
                ],
                'required' => ['customer_name', 'items'],
            ],
        ];
    }

    public function execute(array $input): array
    {
        return DB::transaction(function () use ($input) {
            $order = Order::create([
                'customer_name' => $input['customer_name'],
                'notes'         => $input['notes'] ?? null,
                'status'        => 'pending',
                'total_price'   => 0,
            ]);

            $total = 0;
            $orderDetails = [];

            foreach ($input['items'] as $item) {
                // Validasi product exists
                $product = Product::find($item['product_id']);

                if (!$product) {
                    throw new \Exception("Produk ID {$item['product_id']} tidak ditemukan.");
                }

                $qty      = (int) $item['qty'];
                $price    = (float) $product->price;
                $subtotal = $price * $qty; // ← fix: harga x qty yang benar

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'qty'        => $qty,
                    'unit_price' => $price,
                ]);

                $total += $subtotal;

                $orderDetails[] = [
                    'name'     => $product->name,
                    'qty'      => $qty,
                    'price'    => $price,
                    'subtotal' => $subtotal,
                ];
            }

            // Update total yang benar
            $order->update(['total_price' => $total]);

            return [
                'success'    => true,
                'order_code' => $order->order_code,
                'customer'   => $input['customer_name'],
                'items'      => $orderDetails,
                'total'      => $total,
                'message'    => "Pesanan berhasil dibuat dengan total Rp " . number_format($total, 0, ',', '.'),
            ];
        });
    }
}