<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\WalletTransactionFactory> */
    use HasFactory;
     // Type constants
    public const TYPE_CREDIT = 'credit';

    public const TYPE_DEBIT = 'debit';

    // Source constants

    public const SOURCE_CRYPTO = 'crypto';

    public const SOURCE_PROMO = 'promo';

    public const SOURCE_WALLET = 'wallet';

    public const SOURCE_MANUAL = 'manual';

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    protected $guarded = ['id'];

    protected $casts = [
        'amount_cents' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
