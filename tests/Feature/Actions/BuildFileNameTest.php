<?php

it("gets the csv file name", function () {
    $request = new \Illuminate\Http\Request();
    $request->merge(['file_extension' => 'csv']);

    $buildFileName = new \App\Actions\BuildFileName();
    $fileName = $buildFileName->handle($request);

    expect($fileName)->toBe(now()->format('Y-m-d') . '-ynab-repeating-transactions.csv');
});

it("gets the excel file name", function () {
    $request = new \Illuminate\Http\Request();
    $request->merge(['file_extension' => 'excel']);

    $buildFileName = new \App\Actions\BuildFileName();
    $fileName = $buildFileName->handle($request);

    expect($fileName)->toBe(now()->format('Y-m-d') . '-ynab-repeating-transactions.xlsx');
});

it("gets the csv file name if the file extension is not recognized", function () {
    $request = new \Illuminate\Http\Request();
    $request->merge(['file_extension' => 'unknown']);

    $buildFileName = new \App\Actions\BuildFileName();
    $fileName = $buildFileName->handle($request);

    expect($fileName)->toBe(now()->format('Y-m-d') . '-ynab-repeating-transactions.csv');
});
