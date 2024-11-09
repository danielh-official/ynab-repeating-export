<?php

use App\Services\YnabAccessTokenService;

it("gets welcome view", function () {
    Config::set("ynab.client.id", "test123");
    Config::set("ynab.redirect_uri", "https://test.com");

    $this->mock(YnabAccessTokenService::class)->shouldReceive("get")->andReturn("test456");

    $this->get(route("home"))
        ->assertOk()
        ->assertViewIs('welcome')
        ->assertViewHas('auth_url', fn($authUrl) => $authUrl === "https://app.ynab.com/oauth/authorize?client_id=test123&redirect_uri=https%3A%2F%2Ftest.com&response_type=code")
        ->assertViewHas('access_token', 'test456');
});

it("handles exception and gets welcome view with null access token", function () {
    $this->mock(YnabAccessTokenService::class)->shouldReceive("get")->andThrow(new Exception());

    $this->get(route("home"))
        ->assertOk()
        ->assertViewIs('welcome')
        ->assertViewHas('access_token', null)
        ->assertViewHas('auth_url', fn($authUrl) => str_contains($authUrl, 'https://app.ynab.com/oauth/authorize'));
});
