<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded([])]
class Service extends Model
{
    protected function casts(): array
    {
        return [
            'price_cents' => 'integer',
        ];
    }
}
