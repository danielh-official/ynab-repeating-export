<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $accessToken = $request->cookie('ynab_access_token');

        $clientId = config('ynab.client.id');
        $redirectUri = config('ynab.redirect_uri');

        $authUrl = "https://app.ynab.com/oauth/authorize?client_id=$clientId&redirect_uri=$redirectUri&response_type=code";

        return view('welcome', [
            'access_token' => $accessToken,
            'auth_url' => $authUrl,
        ]);
    }
}
