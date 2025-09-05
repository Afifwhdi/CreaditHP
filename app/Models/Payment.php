<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'customer_id','installment_id','method','amount','proof_url',
        'status','verified_at','verified_by','receipt_no','notes',
    ];

    protected $casts = [
        'method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function installment(): BelongsTo {
        return $this->belongsTo(Installment::class);
    }
    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class);
    }
    public function verifier(): BelongsTo {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
