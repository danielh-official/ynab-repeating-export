<?php

namespace App\Http\Controllers;

use App\Exports\RepeatingTransactionExport;
use App\Services\YnabAccessTokenService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct(
        private readonly YnabAccessTokenService $ynabAccessTokenService,
    )
    {
    }

    /**
     * @param Request $request
     * @param string $budgetId
     * @return array|Collection|mixed
     * @throws Exception
     */
    private function getScheduledTransactions(Request $request, string $budgetId = 'default')
    {
        $accessToken = $this->retrieveAccessToken($request);

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/scheduled_transactions");

        if ($response->failed()) {
            throw new Exception('Failed to get scheduled transactions', $response->status());
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
        $accessToken = $this->retrieveAccessToken($request);

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/accounts");

        if ($response->failed()) {
            throw new Exception('Failed to get accounts', $response->status());
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
        $accessToken = $this->retrieveAccessToken($request);

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/payees");

        if ($response->failed()) {
            throw new Exception('Failed to get payees', $response->status());
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
     * @return Collection
     * @throws Exception
     */
    private function getCategories(Request $request, string $budgetId = 'default')
    {
        $accessToken = $this->retrieveAccessToken($request);

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/categories");

        if ($response->failed()) {
            throw new Exception('Failed to get categories', $response->status());
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
    public function __invoke(Request $request): Response|BinaryFileResponse|RedirectResponse
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
            if ($e->getCode() === 401) {
                $this->ynabAccessTokenService->delete($request);

                return redirect()->route('home')->with('error', 'Failed to get scheduled transactions. Please re-authenticate.');
            }

            return response($e->getMessage(), 404);
        }

        try {
            $accounts = $this->getAccounts($request);
        } catch (Exception $e) {
            if ($e->getCode() === 401) {
                $this->ynabAccessTokenService->delete($request);

                return redirect()->route('home')->with('error', 'Failed to get accounts. Please re-authenticate.');
            }

            return response($e->getMessage(), 404);
        }

        try {
            $payees = $this->getPayees($request);
        } catch (Exception $e) {
            if ($e->getCode() === 401) {
                $this->ynabAccessTokenService->delete($request);

                return redirect()->route('home')->with('error', 'Failed to get payees. Please re-authenticate.');
            }

            return response($e->getMessage(), 404);
        }

        try {
            $categories = $this->getCategories($request);
        } catch (Exception $e) {
            if ($e->getCode() === 401) {
                $this->ynabAccessTokenService->delete($request);

                return redirect()->route('home')->with('error', 'Failed to get categories. Please re-authenticate.');
            }

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

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function retrieveAccessToken(Request $request): mixed
    {
        return $this->ynabAccessTokenService->get($request);
    }
}
