<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Installment extends Model
{
    protected $fillable = [
        'credit_id', 'installment_no', 'due_date', 'amount', 'status', 'paid_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount'   => 'decimal:2',
        'paid_at'  => 'datetime',
    ];

    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class);
    }

    public function markPaid(): void
    {
        $this->status  = 'paid';
        $this->paid_at = now();
        $this->save();
    }

    public function markPending(): void
    {
        $this->status  = 'pending';
        $this->paid_at = null;
        $this->save();
    }
}
