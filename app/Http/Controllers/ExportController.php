<?php

namespace App\Http\Controllers;

use App\Actions\BuildFileName;
use App\Actions\FlattenCategories;
use App\Actions\GetAccounts;
use App\Actions\GetCategoryGroups;
use App\Actions\GetPayees;
use App\Actions\GetScheduledTransactions;
use App\Exports\RepeatingTransactionExport;
use App\Services\YnabAccessTokenService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct(
        protected YnabAccessTokenService $ynabAccessTokenService,
        protected GetScheduledTransactions $getScheduledTransactions,
        protected GetAccounts $getAccounts,
        protected GetPayees $getPayees,
        protected GetCategoryGroups $getCategoryGroups,
        protected FlattenCategories $flattenCategories,
        protected BuildFileName $buildFileName,
    ) {

    }

    public function __invoke(Request $request): Response|BinaryFileResponse|RedirectResponse
    {
        try {
            $response = $this->getScheduledTransactions->handle();

            $scheduledTransactions = data_get($response->json(), 'data.scheduled_transactions', collect());

            if (!$scheduledTransactions instanceof Collection) {
                $scheduledTransactions = collect($scheduledTransactions);
            }
        } catch (Exception $e) {
            return $this->handleError($e, $request, 'Failed to get scheduled transactions.');
        }

        try {
            $response = $this->getAccounts->handle();

            $accounts = data_get($response->json(), 'data.accounts', collect());

            if (!$accounts instanceof Collection) {
                $accounts = collect($accounts);
            }
        } catch (Exception $e) {
            return $this->handleError($e, $request, 'Failed to get accounts.');
        }

        try {
            $response = $this->getPayees->handle();

            $payees = data_get($response->json(), 'data.payees', collect());

            if (!$payees instanceof Collection) {
                $payees = collect($payees);
            }
        } catch (Exception $e) {
            return $this->handleError($e, $request, 'Failed to get payees.');
        }

        try {
            $response = $this->getCategoryGroups->handle();

            $categoryGroups = data_get($response->json(), 'data.category_groups', collect());

            if (!$categoryGroups instanceof Collection) {
                $categoryGroups = collect($categoryGroups);
            }

            $categoryGroups = $this->flattenCategories->handle($categoryGroups);
        } catch (Exception $e) {
            return $this->handleError($e, $request, 'Failed to get categories.');
        }

        $fileName = $this->buildFileName->handle($request);

        return (new RepeatingTransactionExport(
            scheduledTransactions: $scheduledTransactions,
            accounts: $accounts,
            payees: $payees,
            categories: $categoryGroups,
        ))->download($fileName);
    }

    private function handleError(Exception $e, Request $request, string $customMessage)
    {
        if ($e->getCode() === 401) {
            $this->ynabAccessTokenService->delete();

            return redirect()->route('home')->with('error', "$customMessage Please re-authenticate.");
        }

        $errorMessage = trim(str_replace("\n", ' ', $e->getMessage()));

        return response("$customMessage {$errorMessage}", 503);
    }
}
