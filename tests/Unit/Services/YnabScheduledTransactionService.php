<?php

use App\Services\YnabScheduledTransactionService;
use Illuminate\Database\Eloquent\Collection;

test('merge empty', function () {
    $ynabScheduledTransactionService = resolve(YnabScheduledTransactionService::class);

    $data = $ynabScheduledTransactionService->merge();

    expect($data)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($data->count())->toBe(0);
});

test('merge scheduled transactions', function () {
    $scheduledTransactions = new Collection([
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
    ]);

    $ynabScheduledTransactionService = resolve(YnabScheduledTransactionService::class, [
        'scheduledTransactions' => $scheduledTransactions,
    ]);

    $data = $ynabScheduledTransactionService->merge();

    expect($data)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($data->count())->toBe(1)
        ->and($data->first())->toBeArray()->toMatchArray([
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
    $scheduledTransactions = new Collection([
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
    ]);

    $categories = new Collection([
        [
            'id' => '1',
            'category_name' => 'category',
            'category_group_name' => 'category group',
        ],
    ]);

    $ynabScheduledTransactionService = resolve(YnabScheduledTransactionService::class, [
        'scheduledTransactions' => $scheduledTransactions,
        'categories' => $categories,
    ]);

    $data = $ynabScheduledTransactionService->merge();

    expect($data)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($data->count())->toBe(1)
        ->and($data->first())->toBeArray()->toMatchArray([
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
    $scheduledTransactions = new Collection([
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
    ]);

    $payees = new Collection([
        [
            'id' => '1',
            'name' => 'payee',
        ],
    ]);

    $ynabScheduledTransactionService = resolve(YnabScheduledTransactionService::class, [
        'scheduledTransactions' => $scheduledTransactions,
        'payees' => $payees,
    ]);

    $data = $ynabScheduledTransactionService->merge();

    expect($data)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($data->count())->toBe(1)
        ->and($data->first())->toBeArray()->toMatchArray([
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
    $scheduledTransactions = new Collection([
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
    ]);

    $accounts = new Collection([
        [
            'id' => '1',
            'name' => 'account',
        ],
    ]);

    $ynabScheduledTransactionService = resolve(YnabScheduledTransactionService::class, [
        'scheduledTransactions' => $scheduledTransactions,
        'accounts' => $accounts,
    ]);

    $data = $ynabScheduledTransactionService->merge();

    expect($data)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($data->count())->toBe(1)
        ->and($data->first())->toBeArray()->toMatchArray([
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
