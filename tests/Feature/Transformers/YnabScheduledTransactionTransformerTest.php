<?php

use App\Transformers\YnabScheduledTransactionTransformer;
use Illuminate\Database\Eloquent\Collection;

beforeEach(function () {
    Session::flush();
});

function testScheduledTransactionsAreStoredProperly(): void
{
    $ynabScheduledTransactionTransformer = resolve(YnabScheduledTransactionTransformer::class);

    $data = [
        'id' => '123',
        'account_id' => '456',
        'date_first' => '2020-01-01',
        'date_next' => '2020-01-01',
        'frequency' => 'never',
        'amount' => 1000,
        'memo' => 'Test',
        'flag_color' => 'red',
        'account_name' => 'Test Account',
        'payee_name' => 'Test Payee',
        'category_name' => 'Test Category',
    ];

    $ynabScheduledTransactionTransformer->store($data);

    $result = $ynabScheduledTransactionTransformer->get();

    expect($result->count())->toBe(1);

    $result = $result->first();

    expect($result)->toBe($data);
}

test('scheduled transactions are stored properly', function () {
    testScheduledTransactionsAreStoredProperly();
});

test('scheduled transactions are updated properly', function () {
    $ynabScheduledTransactionTransformer = resolve(YnabScheduledTransactionTransformer::class);

    testScheduledTransactionsAreStoredProperly();

    $first = [
        'id' => '455',
        'account_id' => '566',
        'date_first' => '2020-01-01',
        'date_next' => '2020-12-01',
        'frequency' => 'monthly',
        'amount' => 2000,
        'memo' => 'Test 2',
        'flag_color' => 'green',
        'account_name' => 'Test Account 2',
        'payee_name' => 'Test Payee 2',
        'category_name' => 'Test Category 2',
    ];

    $ynabScheduledTransactionTransformer->store($first);

    $second = [
        'id' => '123',
        'account_id' => '566',
        'date_first' => '2020-01-01',
        'date_next' => '2020-12-01',
        'frequency' => 'monthly',
        'amount' => 2000,
        'memo' => 'Test 2',
        'flag_color' => 'green',
        'account_name' => 'Test Account 2',
        'payee_name' => 'Test Payee 2',
        'category_name' => 'Test Category 2',
    ];

    $ynabScheduledTransactionTransformer->store($second);

    $result = $ynabScheduledTransactionTransformer->get();

    expect($result->count())->toBe(2);

    $data = new Collection([
        $first,
        $second,
    ]);

    expect($result)->toEqual($data);
});

test('storing scheduled transactions when key is not array', function () {
    \Illuminate\Support\Facades\Session::put('ynab.scheduled_transactions', 'test');

    testScheduledTransactionsAreStoredProperly();
});
