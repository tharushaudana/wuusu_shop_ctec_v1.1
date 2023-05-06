<?php

namespace App\Models;

use DateTimeInterface;
use DateTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockUseRecord extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;

    /*protected $dateFormat = 'Y-m-d H:i:s';
    
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->setTimezone(new DateTimeZone('Asia/Colombo'))->format($this->dateFormat);
    }*/

    protected $hidden = [
        'ref_id',
        'product_id',
        'updated_at',
    ];

    protected $fillable = [
        'ref_id',
        'product_id',
        'type',
        'qty',
        'description',
    ];

    protected $appends = [
        'product',
    ];

    protected static function boot()
    {
        parent::boot();

        foreach (['creating', 'updating', 'deleting', 'restoring'] as $event) {
            static::registerModelEvent($event, function ($model) {
                //### disable auditing when not manual
                if ($model->type != config('stockusetypes.MANUAL')) {
                    parent::disableAuditing();
                }
            });
        }
    }

    //### for 'product'
    protected function getProductAttribute() {
        return Product::find($this->product_id)->only(['id', 'itemcode', 'description']);
    }

    //### @override toArray() for edit returning array.
    public function toArray()
    {
        $data = parent::toArray();

        switch ($this->type) {
            case config('stockusetypes.MANUAL'):
                $data['user'] = User::find($this->ref_id)->only('id', 'name');
                break;
            default:
                $data['ref_id'] = $this->ref_id;
        }

        return $data;
    }

    public function isAuditEvent()
    {
        //return $this->type === config('stockusetypes.MANUAL');
        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Public functions
    |--------------------------------------------------------------------------
    */

    public static function doUse($ref_id, $product_id, $type, $qty, $description=null) {
        $total_available_qty = Product::find($product_id)->qty;

        if ($total_available_qty < $qty) {
            return false;
        }
        
        //### Insert the StockUseRecord to 'stock_use_records' table
        $record = StockUseRecord::create([
            'ref_id' => $ref_id,
            'product_id' => $product_id,
            'type' => $type,
            'qty' => $qty,
            'description' => $description,
        ]);

        if (is_null($record)) return false;

        //### Descriase the available quantities from 'stock_records' table
        StockRecord::where('product_id', $product_id)
            ->where('availableqty', '>', 0)
            ->orderBy('availableqty', 'asc')
            ->chunk(100, function($stockRecords) use ($qty) {
                foreach ($stockRecords as $stockRecord) {
                    if ($stockRecord->availableqty <= $qty) {
                        $qty -= $stockRecord->availableqty;
                        $stockRecord->availableqty = 0;
                    } elseif ($stockRecord->availableqty > $qty) {
                        $stockRecord->availableqty -= $qty;
                        $qty = 0;
                    }

                    $stockRecord->save();

                    if ($qty == 0) break;
                }
            });

        return $record;
    }

    //### this can do with only 'SYSTEM_SALE' type records.
    public static function doRefund($ref_id) { 
        $stockUseRecords = StockUseRecord::where('ref_id', $ref_id)->where('type', config('stockusetypes.SYSTEM_SALE'))->get();

        foreach ($stockUseRecords as $stockUseRecord) {
            $stockRecord = StockRecord::create([
                'product_id' => $stockUseRecord->product_id,
                'supplier_id' => null,
                'qty' => $stockUseRecord->qty,
                'availableqty' => $stockUseRecord->qty,
            ]);

            if (is_null($stockRecord)) break;

            $stockUseRecord->delete();
        }      
        
        return true;
    } 
}
