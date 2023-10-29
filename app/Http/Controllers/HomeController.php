<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    private function retrieveAccessToken(Request $request): mixed
    {
        $accessToken = $request->session()->get('ynab_access_token');

        if (!$accessToken) {
            throw new Exception('No access token');
        }

        return $accessToken;
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function __invoke(Request $request)
    {
        try {
            $accessToken = $this->retrieveAccessToken($request);
        } catch (Exception) {
            $accessToken = null;
        }

        $clientId = config('ynab.client.id');
        $redirectUri = config('ynab.redirect_uri');

        $authUrl = "https://app.ynab.com/oauth/authorize?client_id=$clientId&redirect_uri=$redirectUri&response_type=code";

        return view('welcome', [
            'access_token' => $accessToken,
            'auth_url' => $authUrl,
        ]);
    }
}
