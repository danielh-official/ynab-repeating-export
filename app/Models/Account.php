<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;

    protected $casts = [
        'debt_interest_rates' => 'array',
        'debt_minimum_payments' => 'array',
        'debt_escrow_amounts' => 'array',
    ];
}
