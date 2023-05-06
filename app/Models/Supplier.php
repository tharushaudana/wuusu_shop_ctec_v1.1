<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name'
    ];
}
