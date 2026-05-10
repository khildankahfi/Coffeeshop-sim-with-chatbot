<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $products   = Product::with('category')->where('is_available', true)->get();
        $categories = Category::where('is_active', true)->get();
        return view('kasir.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'items'         => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
            'notes'         => 'nullable|string',
        ]);

        try {
            $result = DB::transaction(function () use ($request) {
                $order = Order::create([
                    'customer_name' => $request->customer_name,
                    'notes'         => $request->notes,
                    'status'        => 'pending',
                    'total_price'   => 0,
                    'created_by'    => auth()->id(),
                ]);

                $total = 0;

                foreach ($request->items as $item) {
                    $product  = Product::findOrFail($item['product_id']);
                    $qty      = (int) $item['qty'];
                    $subtotal = $product->price * $qty;

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $product->id,
                        'qty'        => $qty,
                        'unit_price' => $product->price,
                    ]);

                    $total += $subtotal;
                }

                $order->update(['total_price' => $total]);

                return [
                    'success'    => true,
                    'order_code' => $order->order_code,
                    'total'      => $total,
                ];
            });

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}