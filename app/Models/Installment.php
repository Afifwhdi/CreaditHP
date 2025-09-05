<?php

namespace App\Models;

use App\Enums\InstallmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Installment extends Model
{
    protected $fillable = [
        'credit_id','installment_no','due_date','amount','status','paid_at','payment_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount'   => 'decimal:2',
        'status'   => InstallmentStatus::class,
        'paid_at'  => 'datetime',
    ];

    public function credit(): BelongsTo {
        return $this->belongsTo(Credit::class);
    }

    public function payment(): BelongsTo {
        return $this->belongsTo(Payment::class);
    }

    public function scopeDueInDays(Builder $q, int $days): Builder {
        $target = Carbon::today()->addDays($days);
        return $q->whereDate('due_date', $target);
    }

    public function scopeDueToday(Builder $q): Builder {
        return $q->whereDate('due_date', Carbon::today());
    }

    public function scopePending(Builder $q): Builder {
        return $q->where('status', InstallmentStatus::PENDING);
    }
}
