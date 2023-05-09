<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken;
use Jenssegers\Agent\Agent;

class ApiSession extends Model
{
    use HasFactory;

    //### only fillable that attributes when insert or update
    protected $fillable = [
        'token_id',
        'ip_address',
        'user_agent',
        'hostname',
        'os',
        'os_version',
    ];

    protected $hidden = [
        'user_agent',
    ];

    protected $appends = [
        'token'
    ];

    public $timestamps = false;

    //### for 'token' attribute
    protected function getTokenAttribute() {
        return PersonalAccessToken::where('id', $this->token_id)->first();
    }

    //### Public functions

    public function token() {
        return PersonalAccessToken::where('id', $this->token_id)->first();
    }
}
