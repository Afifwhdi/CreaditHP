<?php

namespace App\Enums;

enum CreditStatus: string {
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case DEFAULTED = 'defaulted';
    case CANCELLED = 'cancelled';
}