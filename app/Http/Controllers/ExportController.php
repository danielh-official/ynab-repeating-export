<?php

namespace App\Http\Controllers;

use App\Exports\RepeatingTransactionExport;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * @param Request $request
     * @param string $budgetId
     * @return array|Collection|mixed
     * @throws Exception
     */
    private function getScheduledTransactions(Request $request, string $budgetId = 'default')
    {
        $accessToken = $request->cookie('ynab_access_token');

        if ($accessToken) {
            $accessToken = decrypt($accessToken);
        } else {
            throw new Exception('No access token');
        }

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/scheduled_transactions");

        $serverKnowledge = data_get($response->json(), 'data.server_knowledge');

        if ($serverKnowledge) {
            cookie('ynab_server_knowledge', $serverKnowledge);
        }

        $scheduledTransactions = data_get($response->json(), 'data.scheduled_transactions', collect());

        if (!$scheduledTransactions instanceof Collection) {
            $scheduledTransactions = collect($scheduledTransactions);
        }

        return $scheduledTransactions;
    }

    /**
     * @param Request $request
     * @param string $budgetId
     * @return array|Collection|mixed
     * @throws Exception
     */
    private function getAccounts(Request $request, string $budgetId = 'default')
    {
        $accessToken = $request->cookie('ynab_access_token');

        if ($accessToken) {
            $accessToken = decrypt($accessToken);
        } else {
            throw new Exception('No access token');
        }

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/accounts");

        $serverKnowledge = data_get($response->json(), 'data.server_knowledge');

        if ($serverKnowledge) {
            cookie('ynab_server_knowledge', $serverKnowledge);
        }

        $accounts = data_get($response->json(), 'data.accounts', collect());

        if (!$accounts instanceof Collection) {
            $accounts = collect($accounts);
        }

        return $accounts;
    }

    /**
     * @param Request $request
     * @param string $budgetId
     * @return array|Collection|mixed
     * @throws Exception
     */
    private function getPayees(Request $request, string $budgetId = 'default')
    {
        $accessToken = $request->cookie('ynab_access_token');

        if ($accessToken) {
            $accessToken = decrypt($accessToken);
        } else {
            throw new Exception('No access token');
        }

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/payees");

        $serverKnowledge = data_get($response->json(), 'data.server_knowledge');

        if ($serverKnowledge) {
            cookie('ynab_server_knowledge', $serverKnowledge);
        }

        $payees = data_get($response->json(), 'data.payees', collect());

        if (!$payees instanceof Collection) {
            $payees = collect($payees);
        }

        return $payees;
    }

    /**
     * @param Request $request
     * @param string $budgetId
     * @return array|Collection|mixed
     * @throws Exception
     */
    private function getCategories(Request $request, string $budgetId = 'default')
    {
        $accessToken = $request->cookie('ynab_access_token');

        if ($accessToken) {
            $accessToken = decrypt($accessToken);
        } else {
            throw new Exception('No access token');
        }

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/categories");

        $serverKnowledge = data_get($response->json(), 'data.server_knowledge');

        if ($serverKnowledge) {
            cookie('ynab_server_knowledge', $serverKnowledge);
        }

        $categories = data_get($response->json(), 'data.category_groups', collect());

        if (!$categories instanceof Collection) {
            $categories = collect($categories);
        }

        return $this->flattenCategories($categories);
    }

    private function buildFileName(Request $request)
    {
        $fileExtension = $request->input('file_extension', 'csv');

        $todaysDateFileFriendlyName = now()->format('Y-m-d');

        if ($fileExtension === 'csv') {
            $fileStringExtension = 'csv';
        } else if ($fileExtension === 'excel') {
            $fileStringExtension = 'xlsx';
        } else {
            $fileStringExtension = 'csv';
        }

        return "$todaysDateFileFriendlyName-ynab-repeating-transactions.$fileStringExtension";
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
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response|BinaryFileResponse
    {
        $fileExtension = $request->input('file_extension', 'csv');

        if ($fileExtension === 'csv') {
            $writerType = Excel::CSV;
        } else if ($fileExtension === 'excel') {
            $writerType = Excel::XLSX;
        } else {
            $writerType = Excel::CSV;
        }

        try {
            $scheduledTransactions = $this->getScheduledTransactions($request);
        } catch (Exception $e) {
            return response($e->getMessage(), 404);
        }

        try {
            $accounts = $this->getAccounts($request);
        } catch (Exception $e) {
            return response($e->getMessage(), 404);
        }

        try {
            $payees = $this->getPayees($request);
        } catch (Exception $e) {
            return response($e->getMessage(), 404);
        }

        try {
            $categories = $this->getCategories($request);
        } catch (Exception $e) {
            return response($e->getMessage(), 404);
        }

        $fileName = $this->buildFileName($request);

        return (new RepeatingTransactionExport(
            scheduledTransactions: $scheduledTransactions,
            accounts: $accounts,
            payees: $payees,
            categories: $categories,
        ))->download($fileName, $writerType);
    }
}
