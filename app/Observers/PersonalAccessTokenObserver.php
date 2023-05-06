<?php

namespace App\Observers;

use App\Models\ApiSession;
use Laravel\Sanctum\PersonalAccessToken;

class PersonalAccessTokenObserver
{
    /**
     * Handle the PersonalAccessToken "created" event.
     */
    public function created(PersonalAccessToken $token): void
    {
        $session = ['token_id' => $token->id];

        $this->fillDeviceDetails($session);

        ApiSession::create($session);
    }

    /**
     * Handle the PersonalAccessToken "updated" event.
     */
    public function updated(PersonalAccessToken $token): void
    {
        $api_session = ApiSession::where('token_id', $token->id)->first();

        $session = [];

        $this->fillDeviceDetails($session);

        $api_session->update($session);
    }

    /**
     * Handle the PersonalAccessToken "deleted" event.
     */
    public function deleted(PersonalAccessToken $token): void
    {
        //
    }

    /**
     * Handle the PersonalAccessToken "restored" event.
     */
    public function restored(PersonalAccessToken $token): void
    {
        //
    }

    /**
     * Handle the PersonalAccessToken "force deleted" event.
     */
    public function forceDeleted(PersonalAccessToken $token): void
    {
        //
    }

    //### private functions

    private function fillDeviceDetails(&$session) {
        $session['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $session['user_agent'] = request()->userAgent();
        $session['hostname'] = request()->header('User-Hostname');
        $session['os'] = request()->header('User-OperatingSystem');
        $session['os_version'] = request()->header('User-OperatingSystemVersion');
    }
}
