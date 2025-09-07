<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Customer extends Model
{
    /** @var array<int,string> */
    protected $fillable = [
        'name',
        'phone',
        'national_id',
        'address',
        'ktp_path',
        'status',
        'employment',
        'monthly_income',
    ];

    public function refreshStatus(): void
    {
        if ($this->status === 'blacklist') {
            return; 
        }

        $total = $this->installments()->count();
        $paid  = $this->installments()->whereNotNull('paid_at')->count();

        if ($total > 0 && $total === $paid) {
            $this->status = 'lunas';
        } else {
            $this->status = 'active';
        }

        $this->save();
    }


    /** @var array<string,string> */
    protected $casts = [
        'monthly_income' => 'decimal:2',
    ];

    // Relasi
    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    public function installments(): HasManyThrough
    {
        return $this->hasManyThrough(Installment::class, Credit::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
