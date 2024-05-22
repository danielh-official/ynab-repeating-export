<?php

use App\Services\YnabScheduledTransactionService;
use Illuminate\Database\Eloquent\Collection;

test('merge empty', function () {
    $merge = resolve(YnabScheduledTransactionService::class)->merge();

    expect($merge)
        ->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($merge->count())->toBe(0);
});

test('merge scheduled transactions', function () {
    $merge = resolve(YnabScheduledTransactionService::class, [
        'scheduledTransactions' => new Collection([
            [
                'id' => '1',
                'account_id' => '1',
                'payee_id' => '1',
                'category_id' => '1',
                'transfer_account_id' => null,
                'deleted' => false,
                'frequency' => 'monthly',
                'date_first' => '2021-01-01',
                'date_next' => '2021-02-01',
                'amount' => 1000,
                'memo' => 'memo',
                'flag_color' => 'red',
            ],
        ]),
    ])->merge();

    expect($merge)
        ->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($merge->count())->toBe(1)
        ->and($merge->first())->toBeArray()->toMatchArray([
            'deleted' => false,
            'frequency' => 'monthly',
            'date_first' => '2021-01-01',
            'date_next' => '2021-02-01',
            'amount' => 1000,
            'memo' => 'memo',
            'flag_color' => 'red',
            'account' => null,
            'payee' => null,
            'category' => null,
            'transfer_account' => null,
        ]);
});

test('merge scheduled transactions and categories', function () {
    $merge = resolve(YnabScheduledTransactionService::class, [
        'scheduledTransactions' => new Collection([
            [
                'id' => '1',
                'account_id' => '1',
                'payee_id' => '1',
                'category_id' => '1',
                'transfer_account_id' => null,
                'deleted' => false,
                'frequency' => 'monthly',
                'date_first' => '2021-01-01',
                'date_next' => '2021-02-01',
                'amount' => 1000,
                'memo' => 'memo',
                'flag_color' => 'red',
            ],
        ]),
        'categories' => new Collection([
            [
                'id' => '1',
                'category_name' => 'category',
                'category_group_name' => 'category group',
            ],
        ]),
    ])->merge();

    expect($merge)
        ->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($merge->count())->toBe(1)
        ->and($merge->first())->toBeArray()->toMatchArray([
            'deleted' => false,
            'frequency' => 'monthly',
            'date_first' => '2021-01-01',
            'date_next' => '2021-02-01',
            'amount' => 1000,
            'memo' => 'memo',
            'flag_color' => 'red',
            'account' => null,
            'payee' => null,
            'category' => [
                'id' => '1',
                'category_name' => 'category',
                'category_group_name' => 'category group',
            ],
            'transfer_account' => null,
        ]);
});

test('merge scheduled transactions and payees', function () {
    $merge = resolve(YnabScheduledTransactionService::class, [
        'scheduledTransactions' => new Collection([
            [
                'id' => '1',
                'account_id' => '1',
                'payee_id' => '1',
                'category_id' => '1',
                'transfer_account_id' => null,
                'deleted' => false,
                'frequency' => 'monthly',
                'date_first' => '2021-01-01',
                'date_next' => '2021-02-01',
                'amount' => 1000,
                'memo' => 'memo',
                'flag_color' => 'red',
            ],
        ]),
        'payees' => new Collection([
            [
                'id' => '1',
                'name' => 'payee',
            ],
        ]),
    ])->merge();

    expect($merge)
        ->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($merge->count())->toBe(1)
        ->and($merge->first())->toBeArray()->toMatchArray([
            'deleted' => false,
            'frequency' => 'monthly',
            'date_first' => '2021-01-01',
            'date_next' => '2021-02-01',
            'amount' => 1000,
            'memo' => 'memo',
            'flag_color' => 'red',
            'account' => null,
            'payee' => [
                'id' => '1',
                'name' => 'payee',
            ],
            'category' => null,
            'transfer_account' => null,
        ]);
});

test('merge scheduled transactions and accounts', function () {
    $merge = resolve(YnabScheduledTransactionService::class, [
        'scheduledTransactions' => new Collection([
            [
                'id' => '1',
                'account_id' => '1',
                'payee_id' => '1',
                'category_id' => '1',
                'transfer_account_id' => null,
                'deleted' => false,
                'frequency' => 'monthly',
                'date_first' => '2021-01-01',
                'date_next' => '2021-02-01',
                'amount' => 1000,
                'memo' => 'memo',
                'flag_color' => 'red',
            ],
        ]),
        'accounts' => new Collection([
            [
                'id' => '1',
                'name' => 'account',
            ],
        ]),
    ])->merge();

    expect($merge)
        ->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($merge->count())->toBe(1)
        ->and($merge->first())->toBeArray()->toMatchArray([
            'deleted' => false,
            'frequency' => 'monthly',
            'date_first' => '2021-01-01',
            'date_next' => '2021-02-01',
            'amount' => 1000,
            'memo' => 'memo',
            'flag_color' => 'red',
            'account' => [
                'id' => '1',
                'name' => 'account',
            ],
            'payee' => null,
            'category' => null,
            'transfer_account' => null,
        ]);
});
