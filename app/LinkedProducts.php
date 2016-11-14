<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkedProducts extends Model
{
    //

    public  $timestamps = false;

    

    public function products(){
        return $this->belongsTo('App\Products');
    }
}
