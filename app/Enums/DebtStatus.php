<?php

namespace App\Enums;

enum DebtStatus: string
{
    case OPEN = 'open';
    case PAID = 'paid';
}
