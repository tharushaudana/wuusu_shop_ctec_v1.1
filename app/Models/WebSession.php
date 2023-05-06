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
        'last_activity',
    ];

    protected $appends = [
        'last_used_at',
    ];

    protected $casts = ['id' => 'string'];

    //### for 'user_agent'
    protected function getUserAgentAttribute($user_agent) {
        $agent = new Agent();
        $agent->setUserAgent($user_agent);
        return $agent;
    }

    //### for 'last_used_at'
    protected function getLastUsedAtAttribute() {
        return date('Y-m-d H:i:s', $this->last_activity);
    }
}
