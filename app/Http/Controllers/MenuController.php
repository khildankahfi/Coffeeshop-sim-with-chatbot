<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::with(['products' => function($q) {
            $q->where('is_available', true);
        }])->where('is_active', true)->get();

        return view('menu.index', compact('categories'));
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
            'is_available' => 'boolean',
        ]);

        Product::create($data);

        return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan.');
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
            'is_available' => 'boolean',
        ]);

        $menu->update($data);

        return redirect()->route('menu.index')->with('success', 'Menu berhasil diupdate.');
    }

    public function destroy(Product $menu)
    {
        $menu->delete();
        return redirect()->route('menu.index')->with('success', 'Menu berhasil dihapus.');
    }

    public function show(Product $menu) {}
}
