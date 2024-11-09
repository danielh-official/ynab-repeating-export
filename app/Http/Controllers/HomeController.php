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
    public function __construct(
        private readonly YnabAccessTokenService $ynabAccessTokenService,
    ) {
    }

    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function __invoke(Request $request)
    {
        try {
            $accessToken = $this->ynabAccessTokenService->get($request);
        } catch (Exception) {
            $accessToken = null;
        }

        $query = http_build_query([
            'client_id' => config('ynab.client.id'),
            'redirect_uri' => config('ynab.redirect_uri'),
            'response_type' => 'code',
        ]);

        $authUrl = "https://app.ynab.com/oauth/authorize?$query";

        return view('welcome', [
            'access_token' => $accessToken,
            'auth_url' => $authUrl,
        ]);
    }
}
