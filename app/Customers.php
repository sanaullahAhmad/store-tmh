<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Customers extends Model
{

    ////////////Has one relation with billing///////////

    public function customerbilling()
    {
        return $this->hasOne('App\CustomerBilling' , 'customer_id' ,'id');
    }
    ////////////Has one relation with Shipping///////////
    public function customershipping()
    {
        return $this->hasOne('App\CustomerShipping' , 'customer_id' , 'id');
    }

    ////////////Has many relation with Orders///////////
    public function Orders()
    {
        return $this->hasMany('App\Orders' , 'customer_id' , 'id');
    }
  
      
    
    //////////////////get customer by id///////////////////////
    public function getCustomer($id){ 

		return Customers::with(
							    array(
							        'orders' => function($q){
							            $q->limit(5);
									        },
									'CustomerShipping.countryShipping',
									'CustomerBilling.countryBilling'        
									    )
									) 
								->where('id', '=' , $id)->first(); 
    }

    /////////////////update customer by id///////////////////////
     public function UpdateCustomerById($input ,$id){

        $username = trim($input['username']);
        $status = trim($input['status']);

        /////////////Billing info///////////////////
        $billing_fname = trim($input['billing_first_name']);
        $billing_lname = trim($input['billing_last_name']);
        $bill_address1 = trim($input['billing_address1']);
        $bill_city = trim($input['billing_city']);
        $bill_pcode = trim($input['billing_post_code']);
        $bill_country = trim($input['billing_country']);
        $bill_phone = trim($input['billing_phone']);
        $bill_email = trim($input['billing_email']);

        ////////////shipping info/////////////////////////
        $shipping_fname = trim($input['shipping_first_name']);
        $shipping_lname = trim($input['shipping_last_name']);
        $ship_address1 = trim($input['shipping_address1']);
        $ship_city = trim($input['shipping_city']);
        $ship_pcode = trim($input['shipping_post_code']);
         $ship_country = trim($input['shipping_country']);


        $updateArrayForCustomer = array(
            'username' 	    => $username,
            'status'     =>$status,
            'updated_at'		=> date('Y-m-d H:i:s')

        );
        Customer::where('id', $id)
            ->update($updateArrayForCustomer);

        DB::connection($this->connection)->table('customer_billing')->where('customer_id', '=',$id)->delete();

        $dataForCustomerBilling = array(
            'customer_id'  => $id,
            'first_name' => $billing_fname,
            'last_name' => $billing_lname,
            'address_1'   =>$bill_address1,
            'city'        =>$bill_city,
            'postcode'     =>$bill_pcode,
            'country'     =>$bill_country,
            'phone'     =>$bill_phone,
            'email'     =>$bill_email,
            'created_at' => date('Y-m-d H:i:s')

        );

        DB::connection($this->connection)->table('customer_billing')->insert($dataForCustomerBilling);
        DB::connection($this->connection)->table('customer_shipping')->where('customer_id', '=',$id)->delete();

        $dataForCustomerShipping = array(
            'customer_id'  => $id,
            'first_name' => $shipping_fname,
            'last_name' => $shipping_lname,
            'address_1'   =>$ship_address1,
            'city'        =>$ship_city,
            'postcode'     =>$ship_pcode,
            'country'     =>$ship_country,
            'created_at' => date('Y-m-d H:i:s')

        );
        DB::connection($this->connection)->table('customer_shipping')->insert($dataForCustomerShipping);

    }



public function UpdateCustomerBilling($input ,$id){

       

        /////////////Billing info///////////////////
        $billing_fname = trim($input['b_fname']);
        $billing_lname = trim($input['b_lname']);
        $bill_address1 = trim($input['b_address_1']);
        $bill_address2 = trim($input['b_address_2']);
        $bill_city = trim($input['b_city']);
        $bill_pcode = trim($input['b_postcode']);
        $bill_country = trim($input['b_country']);
        $bill_phone = trim($input['b_phone']);
        $bill_email = trim($input['b_email']); 

        $dataForCustomerBilling = array(
            'customer_id'  => $id,
            'first_name' => $billing_fname,
            'last_name' => $billing_lname,
            'address_1'   =>$bill_address1,
            'address_2'   =>$bill_address2,
            'city'        =>$bill_city,
            'postcode'     =>$bill_pcode,
            'country'     =>$bill_country,
            'phone'     =>$bill_phone,
            'email'     =>$bill_email  
        );
        DB::table('customer_billing')->where('customer_id', $id)
            ->update($dataForCustomerBilling); 
        
    }



public function UpdateCustomerShpping($input ,$id){

       

        /////////////Billing info///////////////////
        $s_fname = trim($input['s_fname']);
        $s_lname = trim($input['s_lname']);
        $s_address1 = trim($input['s_address_1']);
        $s_address2 = trim($input['s_address_2']);
        $s_city = trim($input['s_city']);
        $s_pcode = trim($input['s_postcode']);
        $s_country = trim($input['s_country']);
        $s_phone = trim($input['s_phone']);
        $s_email = trim($input['s_email']); 

        $dataForCustomerShipping = array(
            'customer_id'  => $id,
            'first_name' => $s_fname,
            'last_name' => $s_lname,
            'address_1'   =>$s_address1,
            'address_2'   =>$s_address2,
            'city'        =>$s_city,
            'postcode'     =>$s_pcode,
            'country'     =>$s_country,
            'phone'     =>$s_phone,
            'email'     =>$s_email  
        );
        DB::table('customer_shipping')->where('customer_id', $id)
            ->update($dataForCustomerShipping); 
        
    }

 
    public function UpdateCustomerPassword($input)
    {
    	return Customers::where('id', '=', $input['customer_id'])->update(['password' => bcrypt($input['current_pass'])]);
    }



    public function checkIfExist($field = 'id', $field_value = false)
    {
        $count  = false ;
        if($field_value)
        {
            if(Customers::Where($field, '=', $field_value)->get()->count() > 0)
                $count  = true ;
        }
        return $count;
    }

    /////////////////////get customer details by id/////////////////////
    public function getCustomerId($field = 'email', $field_value = false)
    {
        $id = false;
        if ($field_value) {
            $user = Customers::select('id')->where($field, $field_value)->first();

            if ($user) {
                $id = $user->id;
                return $id;
            }
        }
    }


    /////////////customer add function//////////////////
    public function add($data)
    {
        Customers::insert($data);
        return DB::connection($this->connection)->getPdo()->lastInsertId();
    }

}
