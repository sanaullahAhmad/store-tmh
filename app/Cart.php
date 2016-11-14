<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Cart extends Model
{
    public $table = "cart";

    public function getCart($id){
        if(!empty($id)){
            return Cart::where('id','=',$id)
                ->first();
        }
        return false;
    }

    public function setCart($data){
        if(!empty($data)){
            Cart::insert( $data );

            $lastInsertId = DB::connection($this->connection)->getPdo()->lastInsertId();
            return $lastInsertId;
        }
        return false;
    }

    public function updateCart($id){
        if(!empty($id)){
            $data = array(
                'updated_at'	=> date('Y-m-d H:i:s')
            );
            Cart::where('id', $id)->update($data);
        }
    }
}
