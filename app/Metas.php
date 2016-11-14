<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Metas extends Model
{ 

    public  $timestamps = false;

    public function products(){
        return $this->belongsTo('App\Products', 'product_id');
    }


    public function saveProductMeta($meta_name = false, $meta_value = false, $product_id = false )
    {
        if($meta_name && $meta_value  && $product_id)
        {
            $data = array( 'meta_name' => $meta_name, 'meta_value' => $meta_value, 'product_id' => $product_id);
            if(Metas::insert( $data ))
            {
                return true;
            }
        }

        return false;
    }

    public function getProductMeta($meta = false, $product_id = false){
        $meta_result = array();
        if($meta && $product_id)
        {
            $meta_result =  Metas::Where('meta_name', '=', $meta)->Where('product_id', '=', $product_id)->first() ;
        }

        return $meta_result;
    }

    public function updateProductMeta($meta_name = false, $meta_value = false, $product_id = false )
    {

        if($meta_name && $meta_value  && $product_id)
        {

            $data = array( 'meta_value' => $meta_value);
            return Metas::where('meta_name', $meta_name)
                ->where('product_id', $product_id)
                ->update( $data );


        }

        return false;
    }

}
