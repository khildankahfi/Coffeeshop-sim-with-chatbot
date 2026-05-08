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
            'description' => 'Buat order baru setelah pelanggan konfirmasi. Hitung total otomatis.',
            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'customer_name' => ['type' => 'string', 'description' => 'Nama pelanggan.'],
                    'items'         => [
                        'type'  => 'array',
                        'items' => [
                            'type'       => 'object',
                            'properties' => [
                                'product_id' => ['type' => 'integer'],
                                'qty'        => ['type' => 'integer', 'minimum' => 1],
                            ],
                            'required' => ['product_id', 'qty'],
                        ],
                        'description' => 'Daftar item yang dipesan.',
                    ],
                    'notes' => ['type' => 'string', 'description' => 'Catatan tambahan.'],
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

            foreach ($input['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['qty'];

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'qty'        => $item['qty'],
                    'unit_price' => $product->price,
                ]);

                $total += $subtotal;
            }

            // Update total setelah semua item diinsert
            $order->update(['total_price' => $total]);

            return [
                'success'    => true,
                'order_code' => $order->order_code,
                'total'      => $total,
            ];
        });
    }
}