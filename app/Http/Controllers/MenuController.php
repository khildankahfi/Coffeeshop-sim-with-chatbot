<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // Publik — halaman menu pelanggan
    public function index()
    {
        $categories = Category::with(['products' => function($q) {
            $q->where('is_available', true);
        }])->where('is_active', true)->get();

        return view('menu.index', compact('categories'));
    }

    // Admin — list semua menu
    public function adminIndex()
    {
        $products = Product::with('category')->latest()->get();
        return view('menu.admin-index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('menu.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:120',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        $data['is_available'] = $request->has('is_available');
        Product::create($data);

        return redirect()->route('admin.menu.adminIndex')
                         ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Product $menu)
    {
        $categories = Category::where('is_active', true)->get();
        return view('menu.form', compact('menu', 'categories'));
    }

    public function update(Request $request, Product $menu)
    {
        $data = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:120',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        $data['is_available'] = $request->has('is_available');
        $menu->update($data);

        return redirect()->route('admin.menu.adminIndex')
                         ->with('success', 'Menu berhasil diupdate.');
    }

    public function destroy(Product $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menu.adminIndex')
                         ->with('success', 'Menu berhasil dihapus.');
    }

    public function show(Product $menu) {}
}