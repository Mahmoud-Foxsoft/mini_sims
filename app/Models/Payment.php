<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded([])]
#[Hidden(['has_used'])]
#[ObservedBy('App\Observers\PaymentObserver')]
class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    // Payment Statuses
    public const WAITING_STATUS = 'waiting';
    public const CONFIRMING_STATUS  = 'confirming';
    public const SENDING_STATUS = 'sending';
    public const FINISHED_STATUS = 'finished';
    public const REFUNDED_STATUS = 'refunded';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'has_used' => 'boolean',
    ];
}
