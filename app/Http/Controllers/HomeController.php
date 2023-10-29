<?php

namespace App\Http\Controllers;

use App\Services\YnabAccessTokenService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * @param YnabAccessTokenService $ynabAccessTokenService
     */
    public function __construct(
        private readonly YnabAccessTokenService $ynabAccessTokenService,
    )
    {
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    private function retrieveAccessToken(Request $request): mixed
    {
        return $this->ynabAccessTokenService->get($request);
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
