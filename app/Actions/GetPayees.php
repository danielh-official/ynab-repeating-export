<?php

declare(strict_types=1);

namespace App\Actions;

use App\Services\YnabAccessTokenService;
use Illuminate\Http\Request;
use Http;

class GetPayees
{
    public function __construct(
        protected readonly YnabAccessTokenService $ynabAccessTokenService,
    ) {
    }

    public function handle(string $budgetId = 'default')
    {
        return Http::withToken(
            $this->ynabAccessTokenService->get()
        )->get("https://api.ynab.com/v1/budgets/$budgetId/payees")->throw();
    }
}
