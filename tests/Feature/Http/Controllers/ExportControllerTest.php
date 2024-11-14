<?php

use App\Exports\RepeatingTransactionExport;
use App\Models\Account;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\Payee;
use App\Models\ScheduledTransaction;
use App\Models\Subtransaction;
use App\Services\YnabAccessTokenService;

it("exports a csv", function () {
    $this->mock(YnabAccessTokenService::class)->shouldReceive('get')->andReturn('fake-token');

    Excel::fake();

    $account_id = Str::uuid()->toString();
    $payee_id = Str::uuid()->toString();
    $category_id = Str::uuid()->toString();
    $category_group_id = Str::uuid()->toString();

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/scheduled_transactions" => Http::response([
            'data' => [
                'scheduled_transactions' => ScheduledTransaction::factory(1)->raw([
                    'deleted' => false,
                    ...compact('account_id', 'payee_id', 'category_id', 'category_group_id'),
                    'subtransactions' => Subtransaction::factory(1)->raw(),
                ]),
                'server_knowledge' => 0,
            ]
        ], 200),
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/accounts" => Http::response([
            'data' => [
                'accounts' => Account::factory(1)->raw([
                    'deleted' => false,
                    'id' => $account_id,
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/payees" => Http::response([
            'data' => [
                'payees' => Payee::factory(1)->raw([
                    'deleted' => false,
                    'id' => $payee_id,
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/categories" => Http::response([
            'data' => [
                'category_groups' => CategoryGroup::factory(1)->raw([
                    'deleted' => false,
                    'id' => $category_group_id,
                    'categories' => Category::factory(1)->raw(['id' => $category_id]),
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    $this->post(route('export'))->assertOk();

    $today = now()->format('Y-m-d');

    Excel::assertDownloaded("$today-ynab-repeating-transactions.csv", function (RepeatingTransactionExport $export) {
        return $export->collection()->count() === 1;
    });
});

it("fails to export a csv due to failing to get scheduled transactions", function () {
    $this->mock(YnabAccessTokenService::class)->shouldReceive('get')->andReturn('fake-token');

    Excel::fake();

    $account_id = Str::uuid()->toString();
    $payee_id = Str::uuid()->toString();
    $category_id = Str::uuid()->toString();
    $category_group_id = Str::uuid()->toString();

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/scheduled_transactions" => Http::response("Server error", 500),
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/accounts" => Http::response([
            'data' => [
                'accounts' => Account::factory(1)->raw([
                    'deleted' => false,
                    'id' => $account_id,
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/payees" => Http::response([
            'data' => [
                'payees' => Payee::factory(1)->raw([
                    'deleted' => false,
                    'id' => $payee_id,
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/categories" => Http::response([
            'data' => [
                'category_groups' => CategoryGroup::factory(1)->raw([
                    'deleted' => false,
                    'id' => $category_group_id,
                    'categories' => Category::factory(1)->raw(['id' => $category_id]),
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    $this->post(route('export'))->assertStatus(503)->assertContent('Failed to get scheduled transactions. HTTP request returned status code 500: Server error');
});

it("fails to export a csv due to failing to get accounts", function () {
    $this->mock(YnabAccessTokenService::class)->shouldReceive('get')->andReturn('fake-token');

    Excel::fake();

    $account_id = Str::uuid()->toString();
    $payee_id = Str::uuid()->toString();
    $category_id = Str::uuid()->toString();
    $category_group_id = Str::uuid()->toString();

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/scheduled_transactions" => Http::response([
            'data' => [
                'scheduled_transactions' => ScheduledTransaction::factory(1)->raw([
                    'deleted' => false,
                    ...compact('account_id', 'payee_id', 'category_id', 'category_group_id'),
                    'subtransactions' => Subtransaction::factory(1)->raw(),
                ]),
                'server_knowledge' => 0,
            ]
        ], 200),
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/accounts" => Http::response('Server error', 500)
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/payees" => Http::response([
            'data' => [
                'payees' => Payee::factory(1)->raw([
                    'deleted' => false,
                    'id' => $payee_id,
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/categories" => Http::response([
            'data' => [
                'category_groups' => CategoryGroup::factory(1)->raw([
                    'deleted' => false,
                    'id' => $category_group_id,
                    'categories' => Category::factory(1)->raw(['id' => $category_id]),
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    $this->post(route('export'))->assertStatus(503)->assertContent('Failed to get accounts. HTTP request returned status code 500: Server error');
});

it("fails to export a csv due to failing to get payees", function () {
    $this->mock(YnabAccessTokenService::class)->shouldReceive('get')->andReturn('fake-token');

    Excel::fake();

    $account_id = Str::uuid()->toString();
    $payee_id = Str::uuid()->toString();
    $category_id = Str::uuid()->toString();
    $category_group_id = Str::uuid()->toString();

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/scheduled_transactions" => Http::response([
            'data' => [
                'scheduled_transactions' => ScheduledTransaction::factory(1)->raw([
                    'deleted' => false,
                    ...compact('account_id', 'payee_id', 'category_id', 'category_group_id'),
                    'subtransactions' => Subtransaction::factory(1)->raw(),
                ]),
                'server_knowledge' => 0,
            ]
        ], 200),
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/accounts" => Http::response([
            'data' => [
                'accounts' => Account::factory(1)->raw([
                    'deleted' => false,
                    'id' => $account_id,
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/payees" => Http::response('Server error', 500)
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/categories" => Http::response([
            'data' => [
                'category_groups' => CategoryGroup::factory(1)->raw([
                    'deleted' => false,
                    'id' => $category_group_id,
                    'categories' => Category::factory(1)->raw(['id' => $category_id]),
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    $this->post(route('export'))->assertStatus(503)->assertContent('Failed to get payees. HTTP request returned status code 500: Server error');
});

it("fails to export a csv due to failing to get categories", function () {
    $this->mock(YnabAccessTokenService::class)->shouldReceive('get')->andReturn('fake-token');

    Excel::fake();

    $account_id = Str::uuid()->toString();
    $payee_id = Str::uuid()->toString();
    $category_id = Str::uuid()->toString();
    $category_group_id = Str::uuid()->toString();

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/scheduled_transactions" => Http::response([
            'data' => [
                'scheduled_transactions' => ScheduledTransaction::factory(1)->raw([
                    'deleted' => false,
                    ...compact('account_id', 'payee_id', 'category_id', 'category_group_id'),
                    'subtransactions' => Subtransaction::factory(1)->raw(),
                ]),
                'server_knowledge' => 0,
            ]
        ], 200),
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/accounts" => Http::response([
            'data' => [
                'accounts' => Account::factory(1)->raw([
                    'deleted' => false,
                    'id' => $account_id,
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/payees" => Http::response([
            'data' => [
                'payees' => Payee::factory(1)->raw([
                    'deleted' => false,
                    'id' => $payee_id,
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/categories" => Http::response('Server error', status: 500)
    ]);

    $this->post(route('export'))->assertStatus(503)->assertContent('Failed to get categories. HTTP request returned status code 500: Server error');
});

it("fails to export a csv due to being unauthenticated", function () {
    $mock = $this->mock(YnabAccessTokenService::class);

    $mock->shouldReceive('get')->andReturn('fake-token');

    $mock->shouldReceive('delete')->andReturn();

    Excel::fake();

    $account_id = Str::uuid()->toString();
    $payee_id = Str::uuid()->toString();
    $category_id = Str::uuid()->toString();
    $category_group_id = Str::uuid()->toString();

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/scheduled_transactions" => Http::response('Unauthenticated', 401),
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/accounts" => Http::response([
            'data' => [
                'accounts' => Account::factory(1)->raw([
                    'deleted' => false,
                    'id' => $account_id,
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/payees" => Http::response([
            'data' => [
                'payees' => Payee::factory(1)->raw([
                    'deleted' => false,
                    'id' => $payee_id,
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    Http::fake([
        "https://api.ynab.com/v1/budgets/default/categories" => Http::response([
            'data' => [
                'category_groups' => CategoryGroup::factory(1)->raw([
                    'deleted' => false,
                    'id' => $category_group_id,
                    'categories' => Category::factory(1)->raw(['id' => $category_id]),
                ]),
                'server_knowledge' => 0,
            ]
        ], 200)
    ]);

    $this->post(route('export'))->assertRedirect(route('home'));
});
