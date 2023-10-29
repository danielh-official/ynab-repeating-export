<?php

use App\Exports\RepeatingTransactionExport;
use App\Transformers\YnabAccountTransformer;
use App\Transformers\YnabCategoryTransformer;
use App\Transformers\YnabPayeeTransformer;
use App\Transformers\YnabScheduledTransactionTransformer;
use Illuminate\Support\Collection;

test('get collection', function () {
    $ynabScheduledTransactionTransformer = resolve(YnabScheduledTransactionTransformer::class);
    $ynabCategoryTransformer = resolve(YnabCategoryTransformer::class);
    $ynabAccountTransformer = resolve(YnabAccountTransformer::class);
    $ynabPayeeTransformer = resolve(YnabPayeeTransformer::class);

    $data = [
        'id' => '1',
        'account_id' => '1',
        'payee_id' => '1',
        'category_id' => '1',
        'transfer_account_id' => null,
        'deleted' => false,
        'frequency' => 'monthly',
        'date_first' => '2021-01-01',
        'date_next' => '2021-01-01',
        'amount' => -1000,
        'memo' => 'Test',
        'flag_color' => 'red',
    ];

    $ynabScheduledTransactionTransformer->store($data);

    $ynabCategoryTransformer->store([
        'id' => '1',
        'name' => 'Test',
        'category_group_name' => 'Test Group',
        'deleted' => false,
    ]);

    $ynabAccountTransformer->store([
        'id' => '1',
        'name' => 'Test',
        'deleted' => false,
    ]);

    $ynabPayeeTransformer->store([
        'id' => '1',
        'name' => 'Test',
        'deleted' => false,
    ]);

    $repeatingTransactionExport = resolve(RepeatingTransactionExport::class);

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
