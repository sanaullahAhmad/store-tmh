<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{

    public $table = "countries";

    
    public function getCountries()
    {
        return Countries::all();
    }

 
    public function CustomerBilling()
    {
        return $this->hasOne('App\Countries', 'country' , 'code');
    }

    public function CustomerShipping()
    {
        return $this->hasOne('App\Countries', 'country' , 'code');
    }
}
