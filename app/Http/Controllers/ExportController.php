<?php

namespace App\Http\Controllers;

use App\Exports\RepeatingTransactionExport;
use App\Services\YnabAccessTokenService;
use App\Services\YnabLastKnowledgeOfServerService;
use App\Transformers\YnabAccountTransformer;
use App\Transformers\YnabCategoryTransformer;
use App\Transformers\YnabPayeeTransformer;
use App\Transformers\YnabScheduledTransactionTransformer;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends BaseExportController
{
    /**
     * @param YnabAccessTokenService $ynabAccessTokenService
     */
    public function __construct(
        private readonly YnabAccessTokenService              $ynabAccessTokenService,
        private readonly YnabScheduledTransactionTransformer $ynabScheduledTransactionTransformer,
        private readonly YnabAccountTransformer              $ynabAccountTransformer,
        private readonly YnabCategoryTransformer             $ynabCategoryTransformer,
        private readonly YnabPayeeTransformer                $ynabPayeeTransformer,
        private readonly YnabLastKnowledgeOfServerService    $ynabLastKnowledgeOfServerService,
    )
    {
    }

    /**
     * @param Request $request
     * @param string $budgetId
     * @return void
     * @throws Exception
     */
    private function getScheduledTransactions(Request $request, string $budgetId = 'default')
    {
        $accessToken = $this->retrieveAccessToken($request);

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/scheduled_transactions", [
            'last_knowledge_of_server' => $this->getLastKnowledgeOfServer(),
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to get scheduled transactions', $response->status());
        }

        $this->retrieveAndStoreLastKnowledgeOfServerFromResponse($response, $request);

        $scheduledTransactions = data_get($response->json(), 'data.scheduled_transactions', collect());

        if (!$scheduledTransactions instanceof Collection) {
            $scheduledTransactions = collect($scheduledTransactions);
        }

        foreach ($scheduledTransactions as $scheduledTransaction) {
            $this->ynabScheduledTransactionTransformer->store($scheduledTransaction);
        }
    }

    /**
     * @param Request $request
     * @param string $budgetId
     * @return void
     * @throws Exception
     */
    private function getAccounts(Request $request, string $budgetId = 'default')
    {
        $accessToken = $this->retrieveAccessToken($request);

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/accounts", [
            'last_knowledge_of_server' => $this->getLastKnowledgeOfServer(),
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to get accounts', $response->status());
        }

        $this->retrieveAndStoreLastKnowledgeOfServerFromResponse($response, $request);

        $accounts = data_get($response->json(), 'data.accounts', collect());

        if (!$accounts instanceof Collection) {
            $accounts = collect($accounts);
        }

        foreach ($accounts as $account) {
            $this->ynabAccountTransformer->store($account);
        }
    }

    /**
     * @param Request $request
     * @param string $budgetId
     * @return void
     * @throws Exception
     */
    private function getPayees(Request $request, string $budgetId = 'default')
    {
        $accessToken = $this->retrieveAccessToken($request);

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/payees", [
            'last_knowledge_of_server' => $this->getLastKnowledgeOfServer(),
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to get payees', $response->status());
        }

        $this->retrieveAndStoreLastKnowledgeOfServerFromResponse($response, $request);

        $payees = data_get($response->json(), 'data.payees', collect());

        if (!$payees instanceof Collection) {
            $payees = collect($payees);
        }

        foreach ($payees as $payee) {
            $this->ynabPayeeTransformer->store($payee);
        }
    }

    /**
     * @param Request $request
     * @param string $budgetId
     * @return void
     * @throws Exception
     */
    private function getCategories(Request $request, string $budgetId = 'default')
    {
        $accessToken = $this->retrieveAccessToken($request);

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/categories", [
            'last_knowledge_of_server' => $this->getLastKnowledgeOfServer(),
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to get categories', $response->status());
        }

        $this->retrieveAndStoreLastKnowledgeOfServerFromResponse($response, $request);

        $categories = data_get($response->json(), 'data.category_groups', collect());

        if (!$categories instanceof Collection) {
            $categories = collect($categories);
        }

        $categories = $this->flattenCategories($categories);

        foreach ($categories as $category) {
            $this->ynabCategoryTransformer->store($category);
        }
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
            $this->getScheduledTransactions($request);
        } catch (Exception $e) {
            if ($e->getCode() === 401) {
                $this->ynabAccessTokenService->delete($request);

                return redirect()->route('home')->with('error', 'Failed to get scheduled transactions. Please re-authenticate.');
            }

            return response($e->getMessage(), 404);
        }

        try {
            $this->getAccounts($request);
        } catch (Exception $e) {
            if ($e->getCode() === 401) {
                $this->ynabAccessTokenService->delete($request);

                return redirect()->route('home')->with('error', 'Failed to get accounts. Please re-authenticate.');
            }

            return response($e->getMessage(), 404);
        }

        try {
            $this->getPayees($request);
        } catch (Exception $e) {
            if ($e->getCode() === 401) {
                $this->ynabAccessTokenService->delete($request);

                return redirect()->route('home')->with('error', 'Failed to get payees. Please re-authenticate.');
            }

            return response($e->getMessage(), 404);
        }

        try {
            $this->getCategories($request);
        } catch (Exception $e) {
            if ($e->getCode() === 401) {
                $this->ynabAccessTokenService->delete($request);

                return redirect()->route('home')->with('error', 'Failed to get categories. Please re-authenticate.');
            }

            return response($e->getMessage(), 404);
        }

        $fileName = $this->buildFileName($request);

        return (new RepeatingTransactionExport(
            ynabScheduledTransactionTransformer: $this->ynabScheduledTransactionTransformer,
            ynabCategoryTransformer: $this->ynabCategoryTransformer,
            ynabAccountTransformer: $this->ynabAccountTransformer,
            ynabPayeeTransformer: $this->ynabPayeeTransformer,
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

    /**
     * @param PromiseInterface|\Illuminate\Http\Client\Response $response
     * @param Request $request
     * @return void
     */
    private function retrieveAndStoreLastKnowledgeOfServerFromResponse(PromiseInterface|\Illuminate\Http\Client\Response $response, Request $request): void
    {
        $lastKnowledgeOfServer = data_get($response->json(), 'data.server_knowledge');

        $this->ynabLastKnowledgeOfServerService->store($lastKnowledgeOfServer, $request);
    }

    /**
     * @return int
     */
    private function getLastKnowledgeOfServer()
    {
        return $this->ynabLastKnowledgeOfServerService->get();
    }
}
