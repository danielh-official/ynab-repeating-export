<?php

use App\Exports\RepeatingTransactionExport;
use App\Models\Account;
use App\Models\Category;
use App\Models\Payee;
use App\Models\ScheduledTransaction;
use App\Models\Subtransaction;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->categories = collect(Category::factory(1)->raw());
    $this->payees = collect(Payee::factory(1)->raw());
    $this->accounts = collect(Account::factory(1)->raw());

    $this->scheduledTransactions = collect(ScheduledTransaction::factory(1)->raw([
        'subtransactions' => Subtransaction::factory(2)->raw(),
        'category_id' => $this->categories->first()['id'],
        'payee_id' => $this->payees->first()['id'],
        'account_id' => $this->accounts->first()['id'],
    ]));
});

it('gets a collection', function () {
    $result = app(RepeatingTransactionExport::class, [
        'scheduledTransactions' => $this->scheduledTransactions,
        'categories' => $this->categories,
        'payees' => $this->payees,
        'accounts' => $this->accounts,
    ])->collection();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result->first())->toHaveKeys(
            [
                'frequency',
                'date_first',
                'date_next',
                'amount',
                'memo',
                'flag_color',
                'account_name',
                'payee_name',
                'category_name',
                'category_group_name',
                'transfer_account_name',
                'raw_amount',
                'inflow_outflow',
                'raw_amount_per_week',
                'raw_amount_per_month',
                'raw_amount_per_year',
                'amount_per_week',
                'amount_per_month',
                'amount_per_year',
                'parent_memo',
                'parent_payee_name',
            ])
        ->and($result->count())->toBe(2);
});

describe('ignores', function () {
    describe('deleted', function () {
        test('parent ', function () {
            $this->categories = collect(Category::factory(1)->raw());
            $this->payees = collect(Payee::factory(1)->raw());
            $this->accounts = collect(Account::factory(1)->raw());

            $this->scheduledTransactions = collect(ScheduledTransaction::factory(1)->raw([
                'subtransactions' => Subtransaction::factory(2)->raw(),
                'category_id' => $this->categories->first()['id'],
                'payee_id' => $this->payees->first()['id'],
                'account_id' => $this->accounts->first()['id'],
                'deleted' => true,
            ]));

            $result = app(RepeatingTransactionExport::class, [
                'scheduledTransactions' => $this->scheduledTransactions,
                'categories' => $this->categories,
                'payees' => $this->payees,
                'accounts' => $this->accounts,
            ])->collection();

            expect($result)->toBeInstanceOf(Collection::class)
                ->and($result->toArray())->toEqual([]);
        });

        test('subtransaction', function () {
            $this->categories = collect(Category::factory(1)->raw());
            $this->payees = collect(Payee::factory(1)->raw());
            $this->accounts = collect(Account::factory(1)->raw());

            $this->scheduledTransactions = collect(ScheduledTransaction::factory(1)->raw([
                'subtransactions' => Subtransaction::factory(2)->raw([
                    'deleted' => true,
                ]),
                'category_id' => $this->categories->first()['id'],
                'payee_id' => $this->payees->first()['id'],
                'account_id' => $this->accounts->first()['id'],
                'deleted' => false,
            ]));

            $result = app(RepeatingTransactionExport::class, [
                'scheduledTransactions' => $this->scheduledTransactions,
                'categories' => $this->categories,
                'payees' => $this->payees,
                'accounts' => $this->accounts,
            ])->collection();

            expect($result)->toBeInstanceOf(Collection::class)
                ->and($result->toArray())->toEqual([]);
        });

        test('transaction', function () {
            $this->categories = collect(Category::factory(1)->raw());
            $this->payees = collect(Payee::factory(1)->raw());
            $this->accounts = collect(Account::factory(1)->raw());

            $this->scheduledTransactions = collect(ScheduledTransaction::factory(1)->raw([
                'category_id' => $this->categories->first()['id'],
                'payee_id' => $this->payees->first()['id'],
                'account_id' => $this->accounts->first()['id'],
                'deleted' => true,
            ]));

            $result = app(RepeatingTransactionExport::class, [
                'scheduledTransactions' => $this->scheduledTransactions,
                'categories' => $this->categories,
                'payees' => $this->payees,
                'accounts' => $this->accounts,
            ])->collection();

            expect($result)->toBeInstanceOf(Collection::class)
                ->and($result->toArray())->toEqual([]);
        });
    });

    describe('without amount', function () {
        test('transaction', function () {
            $this->categories = collect(Category::factory(1)->raw());
            $this->payees = collect(Payee::factory(1)->raw());
            $this->accounts = collect(Account::factory(1)->raw());

            $this->scheduledTransactions = collect(ScheduledTransaction::factory(1)->raw([
                'category_id' => $this->categories->first()['id'],
                'payee_id' => $this->payees->first()['id'],
                'account_id' => $this->accounts->first()['id'],
                'amount' => null,
            ]));

            $result = app(RepeatingTransactionExport::class, [
                'scheduledTransactions' => $this->scheduledTransactions,
                'categories' => $this->categories,
                'payees' => $this->payees,
                'accounts' => $this->accounts,
            ])->collection();

            expect($result)->toBeInstanceOf(Collection::class)
                ->and($result->toArray())->toEqual([]);
        });

        test('subtransactions', function () {
            $this->categories = collect(Category::factory(1)->raw());
            $this->payees = collect(Payee::factory(1)->raw());
            $this->accounts = collect(Account::factory(1)->raw());

            $this->scheduledTransactions = collect(ScheduledTransaction::factory(1)->raw([
                'category_id' => $this->categories->first()['id'],
                'payee_id' => $this->payees->first()['id'],
                'account_id' => $this->accounts->first()['id'],
                'subtransactions' => Subtransaction::factory(1)->raw([
                    'amount' => null,
                ]),
            ]));

            $result = app(RepeatingTransactionExport::class, [
                'scheduledTransactions' => $this->scheduledTransactions,
                'categories' => $this->categories,
                'payees' => $this->payees,
                'accounts' => $this->accounts,
            ])->collection();

            expect($result)->toBeInstanceOf(Collection::class)
                ->and($result->toArray())->toEqual([]);
        });
    });

    describe('without accepted frequency', function () {
        test('transaction', function () {
            $this->categories = collect(Category::factory(1)->raw());
            $this->payees = collect(Payee::factory(1)->raw());
            $this->accounts = collect(Account::factory(1)->raw());

            $this->scheduledTransactions = collect(ScheduledTransaction::factory(1)->raw([
                'category_id' => $this->categories->first()['id'],
                'payee_id' => $this->payees->first()['id'],
                'account_id' => $this->accounts->first()['id'],
                'frequency' => 'something',
            ]));

            $result = app(RepeatingTransactionExport::class, [
                'scheduledTransactions' => $this->scheduledTransactions,
                'categories' => $this->categories,
                'payees' => $this->payees,
                'accounts' => $this->accounts,
            ])->collection();

            expect($result)->toBeInstanceOf(Collection::class)
                ->and($result->toArray())->toEqual([]);
        });

        test('subtransactions', function () {
            $this->categories = collect(Category::factory(1)->raw());
            $this->payees = collect(Payee::factory(1)->raw());
            $this->accounts = collect(Account::factory(1)->raw());

            $this->scheduledTransactions = collect(ScheduledTransaction::factory(1)->raw([
                'category_id' => $this->categories->first()['id'],
                'payee_id' => $this->payees->first()['id'],
                'account_id' => $this->accounts->first()['id'],
                'frequency' => 'something',
                'subtransactions' => Subtransaction::factory(1)->raw(),
            ]));

            $result = app(RepeatingTransactionExport::class, [
                'scheduledTransactions' => $this->scheduledTransactions,
                'categories' => $this->categories,
                'payees' => $this->payees,
                'accounts' => $this->accounts,
            ])->collection();

            expect($result)->toBeInstanceOf(Collection::class)
                ->and($result->toArray())->toEqual([]);
        });
    });
});
