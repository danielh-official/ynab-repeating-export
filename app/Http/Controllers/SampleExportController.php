<?php

namespace App\Http\Controllers;

use App\Actions\BuildFileName;
use App\Actions\Sample\GetSampleAccounts;
use App\Actions\Sample\GetSampleCategoryGroups;
use App\Actions\Sample\GetSamplePayees;
use App\Actions\Sample\GetSampleScheduledTransactions;
use App\Exports\RepeatingTransactionExport;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SampleExportController extends Controller
{
    protected Generator $faker;

    public function __construct(
        protected GetSampleScheduledTransactions $getSampleScheduledTransactions,
        protected GetSampleAccounts $getSampleAccounts,
        protected GetSamplePayees $getSamplePayees,
        protected GetSampleCategoryGroups $getSampleCategoryGroups,
        protected BuildFileName $buildFileName,
    ) {
        $this->faker = Factory::create();
    }

    public function __invoke(Request $request): Response|BinaryFileResponse|RedirectResponse
    {
        return (new RepeatingTransactionExport(
            scheduledTransactions: $this->getSampleScheduledTransactions->handle(),
            accounts: $this->getSampleAccounts->handle(),
            payees: $this->getSamplePayees->handle(),
            categories: $this->getSampleCategoryGroups->handle(),
        ))->download($this->buildFileName->handle($request));
    }
}
