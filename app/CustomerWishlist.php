<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class CustomerWishlist extends Model
{
    ////////////////BELONGS TO RELATION WITH CUSTOMER//////////////
    public $table = "customer_wishlist";

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
    public function getWishes()
    {
        return CustomerWishlist::all();
    }
    public function customer()
    {
        return $this->belongsTo('App\Customers', 'customer_id');
    }

    //add routine for customer shipping section
    public function add($data){
        CustomerWishlist::insert($data);
    }

}
