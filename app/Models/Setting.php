<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable =[
        'store_name',
        'logo_path',
        'default_tenors',
        'reminder_days_before',
        'whatsapp_sender',
        'whatsapp_template',
        'timezone',
        'currency'
    ];

    protected $casts = [
        'default_tenors' => 'array',
    ];

    public static function singleton(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
