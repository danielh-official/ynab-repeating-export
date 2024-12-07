<?php

namespace App\Listeners;

use App\Contracts\YnabAccessTokenServiceInterface;
use DanielHaven\YnabSdkLaravel\Events\AccessTokenRetrieved;

class StoreAccessToken
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected YnabAccessTokenServiceInterface $ynabAccessTokenService
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
