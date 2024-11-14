<?php

use App\Services\YnabAccessTokenService;

it('stores a token', function () {
    $token = Str::uuid()->toString();

    Session::expects('put')->with('ynab_access_token', $token);

    app(YnabAccessTokenService::class)->store($token);
});

it('deletes a token', function () {
    Session::expects('forget');

    app(YnabAccessTokenService::class)->delete();
});

it('gets a token', function () {
    $token = Str::uuid()->toString();

    session()->put('ynab_access_token', $token);

    expect(app(YnabAccessTokenService::class)->get())->toBe($token);
});

it('throws an error if missing the token', function () {
    app(YnabAccessTokenService::class)->get();
})->throws(Exception::class, 'No access token');
