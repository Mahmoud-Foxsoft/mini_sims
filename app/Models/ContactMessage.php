<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Guarded([])]
class ContactMessage extends Model
{
    use HasFactory;

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
