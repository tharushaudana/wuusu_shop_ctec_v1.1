<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;

class WebSession extends Model
{
    use HasFactory;

    protected $hidden = [
        'payload',
        'user_agent',
        'last_activity',
    ];

    protected $appends = [
        'last_used_at',
        'browser',
        'platform'
    ];

    protected $casts = ['id' => 'string'];

    //### for 'last_used_at'
    protected function getLastUsedAtAttribute() {
        return date('Y-m-d H:i:s', $this->last_activity);
    }

    //### for 'browser'
    protected function getBrowserAttribute() {
        $agent = new Agent();
        $agent->setUserAgent($this->user_agent);
        return $agent->browser();
    }

    //### for 'platform'
    protected function getPlatformAttribute() {
        $agent = new Agent();
        $agent->setUserAgent($this->user_agent);
        return $agent->platform();
    }
}
