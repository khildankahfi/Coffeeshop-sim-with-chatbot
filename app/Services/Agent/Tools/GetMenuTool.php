<?php

namespace App\Services\Agent\Tools;

use App\Models\Product;

class GetMenuTool
{
    public function getDefinition(): array
    {
        return [
            'name'        => 'get_menu',
            'description' => 'Ambil daftar menu yang tersedia. Bisa filter by slug kategori.',
            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'category_slug' => [
                        'type'        => 'string',
                        'description' => 'Slug kategori: espresso, manual-brew, non-coffee, food. Kosongkan = semua.',
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
            'id'       => $p->id,
            'name'     => $p->name,
            'category' => $p->category->name,
            'price'    => $p->price,
            'desc'     => $p->description,
        ]);

        return ['menu' => $menu];
    }
}