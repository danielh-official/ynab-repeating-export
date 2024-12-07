<?php

use App\Exports\RepeatingTransactionExport;

it('exports a sample csv', function () {
    Excel::fake();

    $this->post(route('sample.export'))->assertOk();

    $today = now()->format('Y-m-d');

    Excel::assertDownloaded("$today-ynab-repeating-transactions.csv", function (RepeatingTransactionExport $export) {
        $firstRecordInCollection = $export->collection()->first();

        return $export->collection()->count() === 10 && $firstRecordInCollection['category_name'] && $firstRecordInCollection['category_group_name'] && $firstRecordInCollection['payee_name'] && $firstRecordInCollection['account_name'];
    });
});

it('exports a sample xlsx', function () {
    Excel::fake();

    $this->post(route('sample.export'), [
        'file_extension' => 'excel',
    ])->assertOk();

    $today = now()->format('Y-m-d');

    Excel::assertDownloaded("$today-ynab-repeating-transactions.xlsx");
});

it('exports a sample csv if not excel', function () {
    Excel::fake();

    $this->post(route('sample.export'), [
        'file_extension' => 'test',
    ])->assertOk();

    $today = now()->format('Y-m-d');

    Excel::assertDownloaded("$today-ynab-repeating-transactions.csv");
});
