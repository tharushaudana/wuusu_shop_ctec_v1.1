<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Models\Audit;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Append the extra attributes and set values to them
     *
     * @var array<string, string>
     */
    protected $appends = [
        'privileges'
    ];

    protected function getPrivilegesAttribute() {
        return ManagePrivileges::getPrivilegeIdsOfUser($this->id);
    }

    //### Custom functions

    public function webSessions() {
        $expiration = time() - config('session.lifetime') * 60; 
        return WebSession::where('user_id', Auth::user()->id)->where('id', '!=', session()->getId())->where('last_activity', '>=', $expiration)->get();
    }

    public function apiSessions() {
        return ApiSession::join('personal_access_tokens', 'api_sessions.token_id', '=', 'personal_access_tokens.id')
            ->select('api_sessions.id', 'api_sessions.token_id', 'api_sessions.ip_address', 'api_sessions.user_agent', 'api_sessions.hostname', 'api_sessions.os', 'api_sessions.os_version')
            ->where('personal_access_tokens.tokenable_id', Auth::user()->id)
            ->get();
    }

    public function privileges() {
        return new ManagePrivileges($this->id);
    }

    public function hasPrivilege($priviid) {
        return in_array($priviid, $this->privileges);
    }

    public function audits() {
        return Audit::where('user_id', $this->id);
    }
}

class ManagePrivileges {
    private $user_id;
    
    function __construct($user_id) {
        $this->user_id = $user_id;
    }

    public function get() {
        $privilege_ids = $this->getPrivilegeIdsOfUser($this->user_id);

        // Build detailed array.
        $data = [];

        foreach(config('userprivis') as $title => $privileges) {
            foreach($privileges as $name => $priviid) {
                $data[$title][$name] = [$priviid, in_array($priviid, $privilege_ids)];
            }
        }

        return $data;
    }    

    public function set($privilege_ids) {
        // Delete previous privilege records
        Privilege::where('user_id', $this->user_id)->delete();
        
        // Build insert array for, insert new privilege records
        $data = [];

        foreach ($privilege_ids as $id) {
            array_push($data, [
                'user_id' => $this->user_id,
                'privilege' => $id
            ]);
        }

        Privilege::insert($data);
    }

    public static function getPrivilegeIdsOfUser($user_id) {
        // Retrive all privileges of given user.
        $privileges = Privilege::where('user_id', $user_id)->get();

        // Extract only privilege ids.
        $privilege_ids = [];

        foreach($privileges as $privilege) {
            array_push($privilege_ids, $privilege['privilege']);
        }

        return $privilege_ids;
    }
}
