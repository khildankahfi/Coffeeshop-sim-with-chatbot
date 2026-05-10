<?php
// app/Services/Agent/Tools/SubmitFeedbackTool.php

namespace App\Services\Agent\Tools;

use App\Models\Feedback;
use App\Models\Order;

class SubmitFeedbackTool
{
    public function getDefinition(): array
    {
        return [
            'name'        => 'submit_feedback',
            'description' => 'Simpan rating dan ulasan pelanggan setelah pesanan selesai.',
            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'order_code' => [
                        'type'        => 'string',
                        'description' => 'Kode order yang ingin diberi rating (format: ORD-xxx).',
                    ],
                    'customer_name' => [
                        'type'        => 'string',
                        'description' => 'Nama pelanggan.',
                    ],
                    'rating' => [
                        'type'        => 'integer',
                        'minimum'     => 1,
                        'maximum'     => 5,
                        'description' => 'Rating 1-5 bintang.',
                    ],
                    'comment' => [
                        'type'        => 'string',
                        'description' => 'Komentar/ulasan dari pelanggan (opsional).',
                    ],
                ],
                'required' => ['customer_name', 'rating'],
            ],
        ];
    }

    public function execute(array $input): array
    {
        // Cari order jika ada kode order
        $orderId = null;
        if (!empty($input['order_code'])) {
            $order   = Order::where('order_code', $input['order_code'])->first();
            $orderId = $order?->id;
        }

        // Cek apakah sudah pernah kasih feedback untuk order ini
        if ($orderId) {
            $exists = Feedback::where('order_id', $orderId)->exists();
            if ($exists) {
                return [
                    'success' => false,
                    'message' => 'Pesanan ini sudah pernah mendapat rating.',
                ];
            }
        }

        Feedback::create([
            'order_id'      => $orderId,
            'customer_name' => $input['customer_name'],
            'rating'        => $input['rating'],
            'comment'       => $input['comment'] ?? null,
        ]);

        $stars = str_repeat('⭐', $input['rating']);

        return [
            'success' => true,
            'rating'  => $input['rating'],
            'stars'   => $stars,
            'message' => 'Terima kasih atas ulasanmu!',
        ];
    }
}