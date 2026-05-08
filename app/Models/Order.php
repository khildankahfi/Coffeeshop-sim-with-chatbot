<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['order_code', 'customer_name', 'status', 'total_price', 'notes', 'created_by'];

    public function items(): HasMany {
        return $this->hasMany(OrderItem::class);
    }

    // Auto-generate order code sebelum create
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_code = 'ORD-' . now()->format('Ymd') . '-' . str_pad(
                Order::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT
            );
        });
    }
}
