<?php

namespace App\Services\Agent\Tools;

use App\Models\Product;

class RecommendMenuTool
{
    public function getDefinition(): array
    {
        return [
            'name'        => 'recommend_menu',
            'description' => 'Rekomendasikan menu berdasarkan budget atau preferensi kategori pelanggan.',
            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'max_price'     => ['type' => 'number',  'description' => 'Batas harga maksimal (Rupiah).'],
                    'category_slug' => ['type' => 'string',  'description' => 'Slug kategori yang diinginkan.'],
                ],
                'required' => [],
            ],
        ];
    }

    public function execute(array $input): array
    {
        $query = Product::available()->with('category');

        if (!empty($input['max_price'])) {
            $query->where('price', '<=', $input['max_price']);
        }

        if (!empty($input['category_slug'])) {
            $query->whereHas('category', fn($q) =>
                $q->where('slug', $input['category_slug'])
            );
        }

        $results = $query->orderBy('price')->limit(5)->get()->map(fn($p) => [
            'name'     => $p->name,
            'category' => $p->category->name,
            'price'    => $p->price,
        ]);

        return ['recommendations' => $results];
    }
}