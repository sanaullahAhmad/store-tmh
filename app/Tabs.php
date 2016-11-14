<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Functions\Functions;
use DB;


class Tabs extends Model
{
    public  $timestamps = false;
 

    public function getAllTabs(){
        return Tabs::orderBy('id', 'desc')->where('type', '<>', 'details')->with('products')->paginate(20);
    }

 

    public static function getDetailTab($product_id)
    {
        return Tabs::where('parent_id', $product_id)->where('type', 'details')->with('products')->first();
    }

    public function getTabById($id = false)
    {
        if($id)
        {
            return Tabs::where('id', $id)->with('products')->first();
        }else{
            return false;
        }

    }

  
    public static function checkIfDetailsTabExist($product_id)
    {

        return Tabs::where('type', '=', 'details')->where('parent_id', $product_id)->first();
    }

  


    /**
     * relations
     * */

    public function products(){
        return $this->belongsTo('App\Products', 'parent_id', 'id');
    }


    /**
     * end of relations
     * */


    public static function getGlobalTabs()
    {
        return Tabs::where('parent_id',  0)->get();
    }

    public static function getTabsByProductId($id =  false)
    {
        if($id)
        {
            return Tabs::where('parent_id',$id)->where('type', 'custom')->get();
        }
    }
 
}
