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

class ExportController extends BaseExportController
{
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
}
