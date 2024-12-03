<?php

namespace App\Listeners;

use App\Services\YnabAccessTokenService;
use DanielHaven\YnabSdkLaravel\Events\AccessTokenRetrieved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreAccessToken
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected YnabAccessTokenService $ynabAccessTokenService
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AccessTokenRetrieved $event): void
    {
        if (empty($event->data['access_token'])) {
            return;
        }

        $this->ynabAccessTokenService->store($event->data['access_token']);
    }
}
