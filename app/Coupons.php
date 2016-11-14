<?php

namespace App;

use App\Functions\Functions;
use App\Input;
use App\Categories;

use DB;
use Auth;
class Coupons extends Model
{
    public $timestamps = false;
    public $table = "coupons";

   
    public function getCouponsCounts(){

        $counts = array('all' => 0, 'published' => 0, 'draft' => 0);

        $all = Coupons::count();
        $counts['all'] = $all ;

        $query = Coupons::query('');
        $query->where('status', '=', 'publish');
        $counts['published'] = $query->count();

        $query = Coupons::query('');
        $query->where('status', '=', 'draft');
        $counts['draft'] = $query->count();


        return $counts;

    }
    

    public function getCouponById($id){

      return  Coupons::where('id',$id)->first();

    }
    public function updateCoupon($input){

        $id  = $input['coupon_id'];
        $code = $input['code'];
        $description = $input['description'];
        $discount_type = $input['discount_type'];
        $coupon_amount = $input['coupon_amount'];
        if(isset($input['is_shipping']) && $input['is_shipping'] =='on' ){
            $is_shipping =  $input['is_shipping'] = 1;
        }
        else{
            $is_shipping = 0;
        }
        $expiry_date_str = strtotime($input['coupon_expiry_date']);
        $expiry_date = date('Y-m-d',$expiry_date_str );

        $min_spend = $input['min_spend'];
        $max_spend = $input['max_spend'];
        if(isset($input['is_individual']) && $input['is_individual'] =='on' ){
            $is_individual =  $input['is_individual'] = 1;
        }
        else{
            $is_individual = 0;
        }
        if(isset($input['is_exclude']) && $input['is_exclude'] =='on' ){
            $is_exclude =  $input['is_exclude'] = 1;
        }
        else{
            $is_exclude = 0;
        }

        if(isset($input['action']) && $input['action']  == 'draft'){

           $status = 'draft';
        }
        elseif(isset($input['action']) && $input['action']  == 'publish'){
           $status =  trim($input['status']);
        }
        if(isset($input['show_cart']) && $input['show_cart'] =='on' ){
            $show_cart =  $input['show_cart'] = 1;
        }
        else{
            $show_cart = 0;
        }

        if(isset($input['products'])){
            $products_array = array();
            foreach($input['products'] as $pid){

                $p =    DB::connection($this->connection)->table('products')->where('id', '=',$pid)->select('name')->first();
                $name =  $p->name;
                $products_array[] =[ $pid , $name];

            }
            $products = serialize($products_array);

        }
        else{ $products = '' ;}

        if(isset($input['exc_products'])) {
            $exc_products_array = array();
            foreach($input['exc_products'] as $epid){

                $p =    DB::connection($this->connection)->table('products')->where('id', '=',$epid)->select('name')->first();
                $name =  $p->name;
                $exc_products_array[] =[ $epid , $name];

            }


            $exc_products = serialize($exc_products_array);
        }
        else{ $exc_products = '' ;}

        if(isset($input['categories'])) {

            $categories_array = array();
            foreach($input['categories'] as $cid){

                $p =    DB::connection($this->connection)->table('categories')->where('id', '=',$cid)->select('name')->first();
                $name =  $p->name;
                $categories_array[] =[ $cid , $name];

            }

         $categories = serialize($categories_array);
        }
        else{ $categories = '' ;}
       // exit;
        if(isset( $input['exc_categories'])){

            $exc_categories_array = array();
            foreach($input['exc_categories'] as $ecid){

                $p =    DB::connection($this->connection)->table('categories')->where('id', '=',$ecid)->select('name')->first();
                $name =  $p->name;
                $exc_categories_array[] =[ $ecid , $name];

            }

            $exc_categories = serialize($exc_categories_array);
        }
        else{ $exc_categories = '' ;}
        $limit_coupon = $input['limit_coupon'];
        $limit_user = $input['limit_user'];
        $published_at = $input['yy'].'-'.$input['mm'].'-'.$input['dd'].' '.$input['hr'].':'.$input['min'].':00';



        $data_update = array('code' => $code,
            'description' => $description,
            'type' => $discount_type,
            'status' => $status,
            'published_at' => $published_at,
            'amount' => $coupon_amount,
            'is_free_shipping' => $is_shipping,
            'expiry_date' => $expiry_date,
            'max_spend' => $max_spend,
            'min_spend' => $min_spend,
            'is_individual' =>$is_individual,
            'show_on_cart' =>$show_cart,
            'exclude_sale_items' => $is_exclude,
            'products' => $products,
            'exclude_products' => $exc_products,
            'categories' => $categories,
            'exclude_categories' => $exc_categories,
            'usage_limit_coupon' => $limit_coupon,
            'updated_at'		=> date('Y-m-d H:i:s'),
            'usage_limit_user' =>$limit_user);

        Coupons::where('id','=', $id)->update($data_update);
       
    }

    public function get_allCoupons($perpage ,$pageno){

        $skip = ($pageno-1)*$perpage;
        $result = Coupons::orderBy('id', 'desc')->skip($skip)->take($perpage)->get();
        // dd($result);
        
        $total_count = Coupons::count();
        $data_array= array('error' => 0 , 'total_count'=>$total_count , 'data'=>   $result );

        return json_encode($data_array);

    } 
    
}