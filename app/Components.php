<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Components extends Model
{
    //

    public  $timestamps = false;
 

    public function products(){
        return $this->belongsTo('App\Products', 'product_id');
    }
}
