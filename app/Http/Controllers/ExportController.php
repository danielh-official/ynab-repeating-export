<?php

namespace App\Http\Controllers;

use App\Actions\BuildFileName;
use App\Actions\FlattenCategories;
use App\Contracts\YnabAccessTokenServiceInterface;
use App\Exports\RepeatingTransactionExport;
use YnabSdkLaravel\YnabSdkLaravel\Ynab;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    protected Ynab $ynab;

    public function __construct(
        protected YnabAccessTokenServiceInterface $ynabAccessTokenService,
        protected FlattenCategories $flattenCategories,
        protected BuildFileName $buildFileName,
    ) {
    }

    public function __invoke(Request $request): Response|BinaryFileResponse|RedirectResponse
    {
        if ($this->ynabAccessTokenService->doesNotExist()) {
            return redirect()->route('home')->with('error', 'Please authenticate with YNAB first.');
        }

        $this->ynab = new Ynab($this->ynabAccessTokenService->get());

        try {
            $response = $this->ynab->scheduledTransactions()->list('default')->throw();

            $scheduledTransactions = data_get($response->json(), 'data.scheduled_transactions', collect());

            if (!$scheduledTransactions instanceof Collection) {
                $scheduledTransactions = collect($scheduledTransactions);
            }
        } catch (Exception $e) {
            return $this->handleError($e, $request, 'Failed to get scheduled transactions.');
        }

        try {
            $response = $this->ynab->accounts()->list('default')->throw();

            $accounts = data_get($response->json(), 'data.accounts', collect());

            if (!$accounts instanceof Collection) {
                $accounts = collect($accounts);
            }
        } catch (Exception $e) {
            return $this->handleError($e, $request, 'Failed to get accounts.');
        }

        try {
            $response = $this->ynab->payees()->list('default')->throw();

            $payees = data_get($response->json(), 'data.payees', collect());

            if (!$payees instanceof Collection) {
                $payees = collect($payees);
            }
        } catch (Exception $e) {
            return $this->handleError($e, $request, 'Failed to get payees.');
        }

        try {
            $response = $this->ynab->categories()->list('default')->throw();

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
