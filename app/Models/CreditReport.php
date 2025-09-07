<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditReport extends Model
{
    protected $fillable = [
        'name','report_type','start_date','end_date','customer_status','path_file',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
}
