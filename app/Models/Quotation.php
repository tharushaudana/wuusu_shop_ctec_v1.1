<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model implements \OwenIt\Auditing\Contracts\Auditable
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
        'validuntil',
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

    public function quotation_data() {
        return new ManageQuotationData($this->id);
    }

    public static function generateInvoiceId() {
        $todaySaleCount = self::whereDate('created_at', today())->orderBy('id', 'desc')->count();

        $prefix = 'QUO';

        return $prefix.'-'.date('Ymd').'-'.str_pad(($todaySaleCount + 1), 4, '0', STR_PAD_LEFT);;
    }
}

class ManageQuotationData {
    private $quotation_id;
    
    function __construct($quotation_id) {
        $this->quotation_id = $quotation_id;
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
        return QuotationDataRecord::where('quotation_id', $this->quotation_id)->get();
    }  
    
    public function update($quotation_data) {
        //### Delete current 'quotation_data_records'
        $this->delete();
        //### Re setting...
        $this->set($quotation_data);
    }

    public function set($quotation_data) {
        foreach ($quotation_data as $i => $data) {
            $product = Product::find($data['product_id']);

            if (is_null($product)) break;

            QuotationDataRecord::create([
                'quotation_id' => $this->quotation_id,
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
        QuotationDataRecord::where('quotation_id', $this->quotation_id)->delete();
    }
}
