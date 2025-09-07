<?php

namespace App\Models;

use App\Enums\CreditStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credit extends Model
{

    protected $appends = ['paid_count','remaining_count','total_paid','total_remaining'];

    protected $fillable = [
        'customer_id',
        'phone_name',
        'contract_date',
        'price',
        'down_payment',
        'tenor',
        'due_day',
        'first_due_date',
        'status',
        'notes',
        'principal',
        'installment_amount',
        'total_payable',
    ];

    protected $casts = [
        'contract_date'      => 'date',
        'first_due_date'     => 'date',
        'status'             => CreditStatus::class,
        'price'              => 'decimal:2',
        'down_payment'       => 'decimal:2',
        'principal'          => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'total_payable'      => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }

    public function getPaidCountAttribute(): int
    {
        return $this->installments()->where('status','paid')->count();
    }

    public function getRemainingCountAttribute(): int
    {
        return max(0, (int) $this->tenor - $this->paid_count);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->installments()->where('status','paid')->sum('amount');
    }

    public function getTotalRemainingAttribute(): float
    {
        return max(0.0, (float) $this->total_payable - $this->total_paid);
    }

}
