<?php

namespace App\Models;

use DateTimeInterface;
use DateTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRecord extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;

    //protected $dateFormat = 'Y-m-d H:i:s';
    
    /*protected function serializeDate(DateTimeInterface $date)
    {
        return $date->setTimezone(new DateTimeZone('Asia/Colombo'))->format($this->dateFormat);
    }*/

    protected $hidden = [
        'product_id',
        'supplier_id',  
        'updated_at',      
    ];

    protected $fillable = [
        'product_id',
        'supplier_id',
        'qty',
        'availableqty',
    ];

    protected $appends = [
        'product',
        'supplier',        
    ];

    protected static function boot()
    {
        parent::boot();

        foreach (['creating', 'updating', 'deleting', 'restoring'] as $event) {
            static::registerModelEvent($event, function ($model) {
                //### disable auditing when 'supplier_id' is null (disable for not manual events)
                if (is_null($model->supplier_id)) {
                    parent::disableAuditing();
                }
            });
        }
    }

    //### for 'product'
    protected function getProductAttribute() {
        return Product::find($this->product_id)->only(['id', 'itemcode', 'description']);
    }

    //### for 'supplier'
    protected function getSupplierAttribute() {
        return Supplier::find($this->supplier_id)->only('id', 'name');
    }
}
