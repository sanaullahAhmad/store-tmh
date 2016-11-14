<?php

namespace App;
 
use DB;
use Illuminate\Database\Eloquent\Model;

class Inventories extends Model
{
    //
    public  $timestamps = false;

	protected $connection = 'eagleeye';

	protected $table = 'inventories'; 
	
	public function products(){
		return $this->belongsTo('App\Products', 'product_sku', 'sku');
	}

	public static function updateInventory($input, $sku)
	{
		return Inventories::where('product_sku', $sku)->update($input);
	}

	public function getInventoryByProductSKU($product_sku = false)
	{
		if($product_sku)
		{
			return Inventories::where('product_sku', $product_sku)->first();
		}
	}

	public static function findBySKU($sku = false)
	{
		if($sku)
		{
			$count = Inventories::where('product_sku', $sku)->count();
		 	if($count > 0)
			{
				return true;
			}else{
				return false;
			}
		}

		return false; 

	}
}
