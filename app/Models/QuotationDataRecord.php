<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationDataRecord extends Model
{
    use HasFactory;

    protected $hidden = [
        'quotation_id',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'quotation_id',
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
