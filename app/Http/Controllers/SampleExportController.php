<?php

namespace App\Http\Controllers;

use App\Actions\BuildFileName;
use App\Exports\RepeatingTransactionExport;
use App\Models\Account;
use App\Models\Category;
use App\Models\Payee;
use App\Models\ScheduledTransaction;
use App\Models\Subtransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SampleExportController extends Controller
{
    public function __construct(
        protected BuildFileName $buildFileName,
    ) {}

    public function __invoke(Request $request): Response|BinaryFileResponse|RedirectResponse
    {
        $category = Category::factory()->raw();
        $payee = Payee::factory()->raw();
        $account = Account::factory()->raw();

        $subtransactionCategory = Category::factory()->raw();
        $subtransactionPayee = Payee::factory()->raw();

        $scheduledTransactions = collect();

        $scheduledTransactionsWithSubtransactions = collect(ScheduledTransaction::factory()->count(5)->raw([
            'category_id' => $category['id'],
            'payee_id' => $payee['id'],
            'account_id' => $account['id'],
            'subtransactions' => Subtransaction::factory()->count(1)->raw([
                'category_id' => $subtransactionCategory['id'],
                'payee_id' => $subtransactionPayee['id'],
            ]),
        ]));

        $scheduledTransactionsWithoutSubtransactions = collect(ScheduledTransaction::factory()->count(5)->raw([
            'category_id' => $category['id'],
            'payee_id' => $payee['id'],
            'account_id' => $account['id'],
        ]));

        $scheduledTransactions = $scheduledTransactions->merge($scheduledTransactionsWithSubtransactions);
        $scheduledTransactions = $scheduledTransactions->merge($scheduledTransactionsWithoutSubtransactions);

        return (new RepeatingTransactionExport(
            scheduledTransactions: $scheduledTransactions,
            accounts: collect([$account]),
            payees: collect([$payee, $subtransactionPayee]),
            categories: collect([$category, $subtransactionCategory]),
        ))->download($this->buildFileName->handle($request));
    }
}
