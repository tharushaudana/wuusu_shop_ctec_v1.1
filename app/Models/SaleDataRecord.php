<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDataRecord extends Model
{
    use HasFactory;

    protected $hidden = [
        'sale_id',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'sale_id',
        'product_id',
        'itemcode',
        'description',
        'unitprice',
        'qty',
        'discount',
    ];

    protected function getUnitpriceAttribute($value) {
        return $this->resolveDouble($value);
    }

    private function resolveDouble($val) {
        return (number_format((float)$val, 2, '.', ''));
    }
}
