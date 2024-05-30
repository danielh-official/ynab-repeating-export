<?php

use App\Exports\RepeatingTransactionExport;
use Illuminate\Support\Collection;

test('get collection', function () {
    $result = resolve(RepeatingTransactionExport::class, [
        'scheduledTransactions' => collect([[
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
        ]]),
        'categories' => collect([
            [
                'id' => '1',
                'name' => 'Test',
                'category_group_name' => 'Test Group',
                'deleted' => false,
            ],
        ]),
        'payees' => collect([
            [
                'id' => '1',
                'name' => 'Test',
                'deleted' => false,
            ],
        ]),
        'accounts' => collect([
            [
                'id' => '1',
                'name' => 'Test',
                'deleted' => false,
            ],
        ]),
    ])->collection();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result->toArray())->toEqual([
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
                'parent_memo' => null,
                'parent_payee_name' => null,
            ],
        ]);

});

test('get collection for transaction with every 3 months', function () {
    $result = resolve(RepeatingTransactionExport::class, [
        'scheduledTransactions' => collect([[
            'id' => 1,
            'account_id' => 1,
            'payee_id' => 1,
            'category_id' => 1,
            'transfer_account_id' => null,
            'deleted' => false,
            'frequency' => 'every3Months',
            'date_first' => '2021-01-01',
            'date_next' => '2021-01-01',
            'amount' => -6000,
            'memo' => 'Test',
            'flag_color' => 'red',
        ]]),
        'categories' => collect([
            [
                'id' => '1',
                'name' => 'Test',
                'category_group_name' => 'Test Group',
                'deleted' => false,
            ],
        ]),
        'payees' => collect([
            [
                'id' => '1',
                'name' => 'Test',
                'deleted' => false,
            ],
        ]),
        'accounts' => collect([
            [
                'id' => '1',
                'name' => 'Test',
                'deleted' => false,
            ],
        ]),
    ])->collection();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result->toArray())->toEqual([
            [
                'frequency' => 'every3Months',
                'date_first' => '2021-01-01',
                'date_next' => '2021-01-01',
                'amount' => 6,
                'memo' => 'Test',
                'flag_color' => 'red',
                'account_name' => 'Test',
                'payee_name' => 'Test',
                'category_name' => 'Test',
                'category_group_name' => 'Test Group',
                'transfer_account_name' => null,
                'raw_amount' => -6,
                'inflow_outflow' => 'outflow',
                'raw_amount_per_week' => -0.5,
                'raw_amount_per_month' => -2.0,
                'raw_amount_per_year' => -24.0,
                'amount_per_week' => 0.5,
                'amount_per_month' => 2.0,
                'amount_per_year' => 24.0,
                'parent_memo' => null,
                'parent_payee_name' => null,
            ],
        ]);

});

test('convert subtransactions into collection', function () {
    $result = resolve(RepeatingTransactionExport::class, [
        'scheduledTransactions' => collect([[
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
            'memo' => 'Parent Memo Test',
            'flag_color' => 'red',
            'subtransactions' => [
                [
                    'id' => 1,
                    'scheduled_transaction_id' => 1,
                    'amount' => -500,
                    'memo' => 'Test',
                    'payee_id' => 1,
                    'category_id' => 1,
                    'transfer_account_id' => null,
                    'deleted' => false,
                ],
                [
                    'id' => 2,
                    'scheduled_transaction_id' => 1,
                    'amount' => -500,
                    'memo' => 'Test',
                    'payee_id' => 2,
                    'category_id' => 2,
                    'transfer_account_id' => null,
                    'deleted' => false,
                ],
            ],
        ]]),
        'categories' => collect([
            [
                'id' => '1',
                'name' => 'Test',
                'category_group_name' => 'Test Group',
                'deleted' => false,
            ],
            [
                'id' => '2',
                'name' => 'Test 2',
                'category_group_name' => 'Test Group 2',
                'deleted' => false,
            ],
        ]),
        'payees' => collect([
            [
                'id' => '1',
                'name' => 'Test',
                'deleted' => false,
            ],
            [
                'id' => '2',
                'name' => 'Test 2',
                'deleted' => false,
            ],
        ]),
        'accounts' => collect([
            [
                'id' => '1',
                'name' => 'Test',
                'deleted' => false,
            ],
        ]),
    ])->collection();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result->toArray())->toEqual([
            [
                'frequency' => 'monthly',
                'date_first' => '2021-01-01',
                'date_next' => '2021-01-01',
                'amount' => .5,
                'memo' => 'Test',
                'flag_color' => 'red',
                'account_name' => 'Test',
                'payee_name' => 'Test',
                'category_name' => 'Test',
                'category_group_name' => 'Test Group',
                'transfer_account_name' => null,
                'raw_amount' => -.5,
                'inflow_outflow' => 'outflow',
                'raw_amount_per_week' => -0.13,
                'raw_amount_per_month' => -0.5,
                'raw_amount_per_year' => -6.0,
                'amount_per_week' => 0.13,
                'amount_per_month' => 0.5,
                'amount_per_year' => 6.0,
                'parent_memo' => 'Parent Memo Test',
                'parent_payee_name' => 'Test',
            ],
            [
                'frequency' => 'monthly',
                'date_first' => '2021-01-01',
                'date_next' => '2021-01-01',
                'amount' => .5,
                'memo' => 'Test',
                'flag_color' => 'red',
                'account_name' => 'Test',
                'payee_name' => 'Test 2',
                'category_name' => 'Test 2',
                'category_group_name' => 'Test Group 2',
                'transfer_account_name' => null,
                'raw_amount' => -.5,
                'inflow_outflow' => 'outflow',
                'raw_amount_per_week' => -0.13,
                'raw_amount_per_month' => -0.5,
                'raw_amount_per_year' => -6.0,
                'amount_per_week' => 0.13,
                'amount_per_month' => 0.5,
                'amount_per_year' => 6.0,
                'parent_memo' => 'Parent Memo Test',
                'parent_payee_name' => 'Test',
            ],
        ]);
});
