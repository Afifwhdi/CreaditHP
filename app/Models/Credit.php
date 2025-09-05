<?php

namespace App\Models;

use App\Enums\CreditStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credit extends Model
{
    /** @var array<int,string> */
    protected $fillable = [
        'customer_id',
        'phone_id',
        'contract_date',
        'price',
        'down_payment',
        'tenor',
        'due_day',
        'first_due_date',
        'status',
        'notes',

        // parameter perhitungan
        'interest_rate_year',
        'admin_fee',
        'insurance_fee',
        'other_fee',
        'commission_fee',

        // hasil perhitungan (disimpan)
        'principal',
        'monthly_interest',
        'installment_amount',
        'total_interest',
        'total_payable',
        'expected_profit',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'contract_date'       => 'date',
        'first_due_date'      => 'date',
        'status'              => CreditStatus::class,

        'price'               => 'decimal:2',
        'down_payment'        => 'decimal:2',
        'interest_rate_year'  => 'decimal:2',
        'admin_fee'           => 'decimal:2',
        'insurance_fee'       => 'decimal:2',
        'other_fee'           => 'decimal:2',
        'commission_fee'      => 'decimal:2',

        'principal'           => 'decimal:2',
        'monthly_interest'    => 'decimal:2',
        'installment_amount'  => 'decimal:2',
        'total_interest'      => 'decimal:2',
        'total_payable'       => 'decimal:2',
        'expected_profit'     => 'decimal:2',
    ];

    // Relasi
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function phone(): BelongsTo
    {
        return $this->belongsTo(Phone::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }
}
