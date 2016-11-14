<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class CustomerShipping extends Model
{
    ////////////////BELONGS TO RELATION WITH CUSTOMER//////////////
    public $table = "customer_shipping";

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function customer()
    {
        return $this->belongsTo('App\Customers', 'customer_id');
    }

    //add routine for customer shipping section
    public function add($data){
        CustomerShipping::insert($data);
    }


    
    public function countryShipping()
    {
        return $this->belongsTo('App\Countries', 'country' , 'code');
    }

}
