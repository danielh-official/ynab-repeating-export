<?php

declare(strict_types=1);

namespace App\Actions\Sample;

use App\Models\ScheduledTransaction;
use App\Models\Subtransaction;

class GetSampleScheduledTransactions
{
    public function handle()
    {
        return collect(ScheduledTransaction::factory()->count(10)->raw([
            'subtransactions' => Subtransaction::factory()->count(1)->raw(),
        ]));
    }
}
