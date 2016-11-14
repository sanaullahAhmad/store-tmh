<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class CustomerBilling extends Model
{
    //add routine for customer billing section
    public $table = "customer_billing";

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    ////////////////BELONGS TO RELATION WITH CUSTOMER//////////////

    public function customer()
    {
        return $this->belongsTo('App\Customers', 'customer_id' , 'id');
    }

    public function countryBilling()
    {
        return $this->belongsTo('App\Countries', 'country' , 'code');
    }


    public function add($data){
        CustomerBilling::insert($data);
    }
}
