<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class CartDetails extends Model
{
    //table name
    public $table = "cart_details";

    //adding the details
    public function addDetails($data){
        CartDetails::insert( $data );
    }

    //update the qty or price or discount etc
    public function updateDetails($id,$data){
        CartDetails::where('id', $id)->update($data);
    }

    //deleting the entry from cart
    public function deleteDetails($id , $cart_id){
        CartDetails::where('product_id', $id)->where('cart_id',$cart_id)->delete();
        return true;
    }

    public function getDetails($prod_id,$domain_id){
        return CartDetails::where('product_id', $prod_id)->where('domain_id',$domain_id)->first();
    }

    public function getItems($cart_id){
        return CartDetails::where('cart_id', $cart_id)->get();
    }

    public static function getCount($cart_id){
        return CartDetails::where('cart_id', $cart_id)->count();
    }
}
