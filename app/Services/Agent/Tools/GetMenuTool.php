<?php

namespace App\Services\Agent\Tools;

use App\Models\Product;

class GetMenuTool
{
    public function getDefinition(): array
    {
        return [
            'name'        => 'get_menu',
            'description' => 'Ambil daftar menu yang tersedia beserta ID, nama, harga, dan kategori. ID wajib dipakai saat place_order.',
            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'category_slug' => [
                        'type'        => 'string',
                        'description' => 'Slug kategori: espresso, manual-brew, non-coffee, food. Kosongkan untuk semua menu.',
                    ],
                ],
                'required' => [],
            ],
        ];
    }

    public function execute(array $input): array
    {
        $query = Product::available()->with('category');

        if (!empty($input['category_slug'])) {
            $query->whereHas('category', fn($q) =>
                $q->where('slug', $input['category_slug'])
            );
        }

        $menu = $query->get()->map(fn($p) => [
            'id'       => $p->id,           // ← ID wajib ada untuk place_order
            'name'     => $p->name,
            'category' => $p->category->name,
            'price'    => $p->price,
            'price_formatted' => 'Rp ' . number_format($p->price, 0, ',', '.'),
        ]);

        return [
            'menu'  => $menu,
            'total' => $menu->count(),
            'note'  => 'Gunakan field "id" saat memanggil place_order()',
        ];
    }
}