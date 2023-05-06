<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;

    protected $hidden = [
        'customer_id',
        'user_id',
    ];

    protected $fillable = [
        'invoice_id',
        'customer_id',
        'user_id',
        'title',
    ];

    protected $appends = [
        'customer',
        'user',
    ];

    protected function getCustomerAttribute() {
        return Customer::find($this->customer_id)->only('id', 'name');
    }

    protected function getUserAttribute() {
        return User::find($this->user_id)->only('id', 'name');
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Public functions
    |--------------------------------------------------------------------------
    */

    public function customer() {
        return Customer::find($this->customer_id);
    }

    public function sale_data() {
        return new ManageSaleData($this->id);
    }

    public static function generateInvoiceId() {
        $todaySaleCount = self::whereDate('created_at', today())->orderBy('id', 'desc')->count();

        $prefix = 'INV';

        return $prefix.'-'.date('Ymd').'-'.str_pad(($todaySaleCount + 1), 4, '0', STR_PAD_LEFT);;
    }
}

class ManageSaleData {
    private $sale_id;
    
    function __construct($sale_id) {
        $this->sale_id = $sale_id;
    }

    public function grand_total() {
        return $this->sub_total() - $this->total_discount();
    }

    public function total_discount() {
        $total = 0;

        foreach ($this->get() as $i => $record) {
            $total += $record->discount;
        }

        return $total;
    }

    public function sub_total() {
        $total = 0;

        foreach ($this->get() as $i => $record) {
            $total += ($record->unitprice * $record->qty);
        }

        return $total;
    }

    public function get() {
        return SaleDataRecord::where('sale_id', $this->sale_id)->get();
    }  
    
    public function update($sale_data) {
        //### Delete current 'sale_data_records'
        $this->delete();
        //### Refund all 'stock_use_records' of given 'sale'
        StockUseRecord::doRefund($this->sale_id);
        //### Re setting...
        $this->set($sale_data);
    }

    public function set($sale_data) {
        foreach ($sale_data as $i => $data) {
            $product = Product::find($data['product_id']);

            if (is_null($product)) break;

            StockUseRecord::doUse($this->sale_id, $product->id, config('stockusetypes.SYSTEM_SALE'), $data['qty']);

            SaleDataRecord::create([
                'sale_id' => $this->sale_id,
                'product_id' => $product->id,
                'itemcode' => $product->itemcode,
                'description' => $product->description,
                'unitprice' => $product->price_sale,
                'qty' => $data['qty'],
                'discount' => $data['discount']
            ]);
        }
    }

    public function delete() {
        SaleDataRecord::where('sale_id', $this->sale_id)->delete();
    }
}
