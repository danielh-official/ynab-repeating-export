<?php

namespace App\Http\Controllers;

use App\Services\YnabAccessTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class YnabController extends Controller
{
    public function __construct(
        private readonly YnabAccessTokenService $ynabAccessTokenService,
    ) {
    }

    public function callback(Request $request)
    {
        $query = http_build_query([
            'client_id' => config('ynab.client.id'),
            'client_secret' => config('ynab.client.secret'),
            'redirect_uri' => config('ynab.redirect_uri'),
            'grant_type' => 'authorization_code',
            'code' => $request->query('code'),
            'scope' => 'read-only',
        ]);

        $accessToken = data_get(
            Http::post("https://app.ynab.com/oauth/token?$query")->json(),
            'access_token'
        );

        if ($accessToken) {
            $this->ynabAccessTokenService->store($request, $accessToken);

            return redirect()->route('home')->with('success', 'Access token retrieved');
        } else {
            return redirect()->route('home')->with('error', 'Failed to get access token');
        }
    }
}
