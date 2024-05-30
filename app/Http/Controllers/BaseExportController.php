<?php

namespace App\Http\Controllers;

use App\Services\YnabAccessTokenService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class BaseExportController extends Controller
{
    public function __construct(
        protected readonly YnabAccessTokenService $ynabAccessTokenService,
    ) {
    }

    /**
     * @return array|Collection|mixed
     *
     * @throws Exception
     */
    protected function getScheduledTransactions(Request $request, string $budgetId = 'default')
    {
        $response = Http::withToken(
            $this->retrieveAccessToken($request)
        )->get("https://api.ynab.com/v1/budgets/$budgetId/scheduled_transactions");

        if ($response->failed()) {
            throw new Exception('Failed to get scheduled transactions', $response->status());
        }

        $scheduledTransactions = data_get($response->json(), 'data.scheduled_transactions', collect());

        if (! $scheduledTransactions instanceof Collection) {
            $scheduledTransactions = collect($scheduledTransactions);
        }

        return $scheduledTransactions;
    }

    /**
     * @return array|Collection|mixed
     *
     * @throws Exception
     */
    protected function getAccounts(Request $request, string $budgetId = 'default')
    {
        $response = Http::withToken(
            $this->retrieveAccessToken($request)
        )->get("https://api.ynab.com/v1/budgets/$budgetId/accounts");

        if ($response->failed()) {
            throw new Exception('Failed to get accounts', $response->status());
        }

        $accounts = data_get($response->json(), 'data.accounts', collect());

        if (! $accounts instanceof Collection) {
            $accounts = collect($accounts);
        }

        return $accounts;
    }

    /**
     * @return array|Collection|mixed
     *
     * @throws Exception
     */
    protected function getPayees(Request $request, string $budgetId = 'default')
    {
        $response = Http::withToken(
            $this->retrieveAccessToken($request)
        )->get("https://api.ynab.com/v1/budgets/$budgetId/payees");

        if ($response->failed()) {
            throw new Exception('Failed to get payees', $response->status());
        }

        $payees = data_get($response->json(), 'data.payees', collect());

        if (! $payees instanceof Collection) {
            $payees = collect($payees);
        }

        return $payees;
    }

    /**
     * @return Collection
     *
     * @throws Exception
     */
    protected function getCategories(Request $request, string $budgetId = 'default')
    {
        $response = Http::withToken(
            $this->retrieveAccessToken($request)
        )->get("https://api.ynab.com/v1/budgets/$budgetId/categories");

        if ($response->failed()) {
            throw new Exception('Failed to get categories', $response->status());
        }

        $categories = data_get($response->json(), 'data.category_groups', collect());

        if (! $categories instanceof Collection) {
            $categories = collect($categories);
        }

        return $this->flattenCategories($categories);
    }

    protected function buildFileName(Request $request)
    {
        $fileExtension = $request->input('file_extension', 'csv');

        if ($fileExtension === 'csv') {
            $fileStringExtension = 'csv';
        } elseif ($fileExtension === 'excel') {
            $fileStringExtension = 'xlsx';
        } else {
            $fileStringExtension = 'csv';
        }

        $today = now()->format('Y-m-d');

        return "$today-ynab-repeating-transactions.$fileStringExtension";
    }

    private function flattenCategories(Collection|array $categories): Collection
    {
        $flattenedCategories = collect();

        if (is_array($categories)) {
            $categories = collect($categories);
        }

        foreach ($categories as $categoryGroup) {
            $flattenedCategories->push($categoryGroup);

            $categories = data_get($categoryGroup, 'categories');

            if ($categories) {
                $flattenedCategories = $flattenedCategories->merge($this->flattenCategories($categories));
            }
        }

        return $flattenedCategories;
    }

    /**
     * @throws Exception
     */
    private function retrieveAccessToken(Request $request): mixed
    {
        return $this->ynabAccessTokenService->get($request);
    }
}
