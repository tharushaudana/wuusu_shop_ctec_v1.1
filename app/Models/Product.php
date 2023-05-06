<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;

    //### hide these attributes.
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    //### only fillable that attributes when insert or update
    protected $fillable = [
        'itemcode',
        'description',
        'unit',
        'minqty',
        'max_retail_price',
        'saler_discount_rate',
        'profit_percent',
        'price_matara',
        'price_akuressa',
    ];

    //### append extra attributes.
    protected $appends = [
        'qty',
        'received_rate',
        'price_sale',
        'recomanded_price_sale',
        'recomanded_profit_percent',
    ];

    /*
    |--------------------------------------------------------------------------
    | Modify values of attributes below.
    |--------------------------------------------------------------------------
    */

    //### for 'max_retail_price'
    protected function getMaxRetailPriceAttribute($value) {
        return $this->resolveDouble($value);
    }

    //### for 'saler_discount_rate'
    protected function getSalerDiscountRateAttribute($value) {
        return $this->resolveDouble($value);
    }

    //### for 'profit_percent'
    protected function getProfitPercentAttribute($value) {
        return $this->resolveDouble($value);
    }

    //### for 'price_matara'
    protected function getPriceMataraAttribute($value) {
        return $this->resolveDouble($value);
    }

    //### for 'price_akuressa'
    protected function getPriceAkuressaAttribute($value) {
        return $this->resolveDouble($value);
    }

    /*
    |--------------------------------------------------------------------------
    | Set values for extra attributes.
    |--------------------------------------------------------------------------
    */

    //### for 'qty'
    protected function getQtyAttribute() {
        return self::getAvailableQty($this->id);
    }

    //### for 'received_rate'
    protected function getReceivedRateAttribute() {
        return $this->calc_received_rate();
    }

    //### for 'price_sale'
    protected function getPriceSaleAttribute() {
        return $this->calc_price_sale();
    }

    //### for 'recomanded_price_sale'
    protected function getRecomandedPriceSaleAttribute() {
        return $this->calc_recomanded_price_sale();
    }

    //### for 'recomanded_profit_percent'
    protected function getRecomandedProfitPercentAttribute() {
        return $this->calc_recomanded_profit_percent();
    }


    /*
    |--------------------------------------------------------------------------
    | Custom Public functions
    |--------------------------------------------------------------------------
    */

    public static function getAvailableQty($product_id) {
        if (!Product::where('id', $product_id)->exists()) return null;
        return intval(StockRecord::where('product_id', $product_id)->sum('availableqty'));
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Private functions
    |--------------------------------------------------------------------------
    */

    private function resolveDouble($val) {
        return (number_format((float)$val, 2, '.', ''));
    }

    private function calc_received_rate() {
		$discount = (($this->saler_discount_rate / 100) * $this->max_retail_price);
		$received_rate = ($this->max_retail_price - $discount);
		$received_rate = number_format((float)$received_rate, 2, '.', '');
        return $received_rate;
	}

	private function calc_price_sale() {
		$price_sale = $this->received_rate + (($this->received_rate * $this->profit_percent) / 100);
		$price_sale = number_format((float)$price_sale, 2, '.', '');

        return $price_sale;
	}

    private function calc_recomanded_price_sale() {
		$min_profit = $this->calc_min_profit();

		if ($min_profit <= 0) {
			return $this->received_rate;
		} 

		$recomanded_price_sale = $this->received_rate + $min_profit;
		$recomanded_price_sale = number_format((float)$recomanded_price_sale, 2, '.', '');

        return $recomanded_price_sale;
    }

    private function calc_recomanded_profit_percent() {
		$min_profit = $this->calc_min_profit();

		if ($min_profit <= 0) {
			return null;
		} 

		$recomanded_profit_percent = ($min_profit / $this->received_rate) * 100;
		$recomanded_profit_percent = number_format((float)$recomanded_profit_percent, 2, '.', '');;

        return $recomanded_profit_percent;
    }

    private function calc_min_profit() {
		$min_price = min($this->price_matara, $this->price_akuressa);

		if ($this->price_sale <= $min_price) return 0;

		return $min_price - $this->received_rate;
    }
}
