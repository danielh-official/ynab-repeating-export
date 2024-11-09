<?php

use App\Services\YnabAccessTokenService;

it("gets the callback", function () {
    Config::set("ynab.client.id", "test123");
    Config::set("ynab.client.secret", "ofhnweuironwe");
    Config::set("ynab.redirect_uri", "https://test.com");

    $this->mock(YnabAccessTokenService::class)->shouldReceive("store")->andReturn("test123")->once();

    Http::fake([
        "https://app.ynab.com/oauth/token?*" => Http::response([
            "access_token" => "test",
        ]),
    ]);

    $this->get(route("ynab.callback", [
        "code" => "test456",
    ]))
        ->assertRedirect()
        ->assertSessionHas("success", "Access token retrieved");
});

it("fails to get the access token", function () {
    Config::set("ynab.client.id", "test123");
    Config::set("ynab.client.secret", "ofhnweuironwe");
    Config::set("ynab.redirect_uri", "https://test.com");

    $this->mock(YnabAccessTokenService::class)->shouldReceive("store")->never();

    Http::fake([
        "https://app.ynab.com/oauth/token?*" => Http::response("Server error", 500),
    ]);

    $this->get(route("ynab.callback", [
        "code" => "test456",
    ]))
        ->assertRedirect()
        ->assertSessionHas("error", "Failed to get access token");
});
