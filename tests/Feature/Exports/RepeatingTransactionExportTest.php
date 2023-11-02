<?php

use App\Exports\RepeatingTransactionExport;
use Illuminate\Support\Collection;

test('get collection', function () {
    $data = [
        'id' => 1,
        'account_id' => 1,
        'payee_id' => 1,
        'category_id' => 1,
        'transfer_account_id' => null,
        'deleted' => false,
        'frequency' => 'monthly',
        'date_first' => '2021-01-01',
        'date_next' => '2021-01-01',
        'amount' => -1000,
        'memo' => 'Test',
        'flag_color' => 'red',
    ];

    $categories = collect([
        [
            'id' => '1',
            'name' => 'Test',
            'category_group_name' => 'Test Group',
            'deleted' => false,
        ],
    ]);

    $payees = collect([
        [
            'id' => '1',
            'name' => 'Test',
            'deleted' => false,
        ],
    ]);

    $accounts = collect([
        [
            'id' => '1',
            'name' => 'Test',
            'deleted' => false,
        ],
    ]);

    $repeatingTransactionExport = resolve(RepeatingTransactionExport::class, [
        'scheduledTransactions' => collect([$data]),
        'categories' => $categories,
        'payees' => $payees,
        'accounts' => $accounts,
    ]);

    $result = $repeatingTransactionExport->collection();

    $expected = [
        [
            'frequency' => 'monthly',
            'date_first' => '2021-01-01',
            'date_next' => '2021-01-01',
            'amount' => 1,
            'memo' => 'Test',
            'flag_color' => 'red',
            'account_name' => 'Test',
            'payee_name' => 'Test',
            'category_name' => 'Test',
            'category_group_name' => 'Test Group',
            'transfer_account_name' => null,
            'raw_amount' => -1,
            'inflow_outflow' => 'outflow',
            'raw_amount_per_week' => -0.25,
            'raw_amount_per_month' => -1.0,
            'raw_amount_per_year' => -12.0,
            'amount_per_week' => 0.25,
            'amount_per_month' => 1.0,
            'amount_per_year' => 12.0,
        ],
    ];

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result->toArray())->toEqual($expected);

});
