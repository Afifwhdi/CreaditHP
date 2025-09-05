<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Phone extends Model
{
    /** @var array<int,string> */
    protected $fillable = [
        'brand',
        'model',
        'variant',
        'cash_price',
        'cost_price',
        'stock',
        'photo_path',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'cash_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock'      => 'integer',
    ];

    // Relasi
    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        return trim($this->brand.' '.$this->model.($this->variant ? ' '.$this->variant : ''));
    }

    public function getCashMarginAttribute(): float
    {
        return (float) $this->cash_price - (float) $this->cost_price;
    }
}
