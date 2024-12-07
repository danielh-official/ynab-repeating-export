<?php

use App\Contracts\YnabAccessTokenServiceInterface;

it("gets welcome view", function () {
    Config::set("ynab-sdk-laravel.client.id", "test123");

    $this->mock(YnabAccessTokenServiceInterface::class)->shouldReceive("get")->andReturn("test456");

    $this->get(route("home"))
        ->assertOk()
        ->assertViewIs('welcome')
        ->assertViewHas('auth_url', "https://app.ynab.com/oauth/authorize?client_id=test123&redirect_uri=https%3A%2F%2Fynab-repeating-export.test%2Fynab-oauth%2Fcallback&response_type=code")
        ->assertViewHas('access_token', 'test456');
});

it("handles exception and gets welcome view with null access token", function () {
    $this->mock(YnabAccessTokenServiceInterface::class)->shouldReceive("get")->andThrow(new Exception());

    $this->get(route("home"))
        ->assertOk()
        ->assertViewIs('welcome')
        ->assertViewHas('access_token', null)
        ->assertViewHas('auth_url', fn($authUrl) => str_contains($authUrl, 'https://app.ynab.com/oauth/authorize'));
});
