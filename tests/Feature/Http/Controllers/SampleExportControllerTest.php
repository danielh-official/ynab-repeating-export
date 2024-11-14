<?php

use App\Exports\RepeatingTransactionExport;
use App\Services\YnabAccessTokenService;

it("exports a sample csv", function () {
    $this->mock(YnabAccessTokenService::class);

    Excel::fake();

    $this->post(route('sample.export'))->assertOk();

    $today = now()->format('Y-m-d');

    Excel::assertDownloaded("$today-ynab-repeating-transactions.csv", function (RepeatingTransactionExport $export) {
        return $export->collection()->count() === 10;
    });
});

it("exports a sample xlsx", function () {
    $this->mock(YnabAccessTokenService::class);

    Excel::fake();

    $this->post(route('sample.export'), [
        'file_extension' => 'excel',
    ])->assertOk();

    $today = now()->format('Y-m-d');

    Excel::assertDownloaded("$today-ynab-repeating-transactions.xlsx");
});

it("exports a sample csv if not excel", function () {
    $this->mock(YnabAccessTokenService::class);

    Excel::fake();

    $this->post(route('sample.export'), [
        'file_extension' => 'test',
    ])->assertOk();

    $today = now()->format('Y-m-d');

    Excel::assertDownloaded("$today-ynab-repeating-transactions.csv");
});
