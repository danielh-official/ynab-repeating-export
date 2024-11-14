<?php

namespace App\Services;

use Exception;

class YnabAccessTokenService
{
    public function store(mixed $accessToken): void
    {
        session()->put('ynab_access_token', $accessToken);
    }

    /**
     * @throws Exception
     */
    public function get(): mixed
    {
        $accessToken = session()->get('ynab_access_token');

        if (empty($accessToken)) {
            throw new Exception('No access token');
        }

        return $accessToken;
    }

    public function delete(): void
    {
        session()->forget('ynab_access_token');
    }
}
