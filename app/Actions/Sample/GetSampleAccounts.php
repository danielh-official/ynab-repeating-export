<?php

declare(strict_types=1);

namespace App\Actions\Sample;

use App\Models\Payee;

class GetSampleAccounts
{
    public function handle()
    {
        return collect(Payee::factory(1)->raw());
    }
}
