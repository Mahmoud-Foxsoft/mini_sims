<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Guarded([])]
class PhoneMessage extends Model
{
    /** @use HasFactory<\Database\Factories\PhoneMessageFactory> */
    use HasFactory;

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
