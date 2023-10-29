<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class YnabController extends Controller
{
    public function callback(Request $request)
    {
        $code = $request->query('code');

        $clientId = config('ynab.client.id');
        $clientSecret = config('ynab.client.secret');
        $redirectUri = config('ynab.redirect_uri');

        $accessTokenUrl = "https://app.ynab.com/oauth/token?client_id=$clientId&client_secret=$clientSecret&redirect_uri=$redirectUri&grant_type=authorization_code&code=$code&scope=read-only";

        $response = Http::post($accessTokenUrl);

        $accessToken = data_get($response->json(), 'access_token');

        if ($accessToken) {
            $request->session()->put('ynab_access_token', $accessToken);

            return redirect()->route('home')->with('success', 'Access token retrieved');
        } else {
            return redirect()->route('home')->with('error', 'Failed to get access token');
        }
    }
}
