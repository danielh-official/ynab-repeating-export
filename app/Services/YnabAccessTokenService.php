<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;

class YnabAccessTokenService
{
    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function store(Request $request): void
    {
        $accessToken = $request->input('access_token');

        if (!$accessToken) {
            throw new Exception('No access token');
        }

        $request->session()->put('ynab_access_token', $accessToken);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function get(Request $request): mixed
    {
        $accessToken = $request->session()->get('ynab_access_token');

        if (!$accessToken) {
            throw new Exception('No access token');
        }

        return $accessToken;
    }

    public function delete(Request $request): void
    {
        $request->session()->forget('ynab_access_token');
    }
}
