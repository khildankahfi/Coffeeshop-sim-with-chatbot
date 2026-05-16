<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table    = 'feedbacks'; // ← eksplisit
    protected $fillable = ['order_id', 'customer_name', 'rating', 'comment'];
    protected $casts    = ['rating' => 'integer'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}