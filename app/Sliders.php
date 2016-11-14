<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sliders extends Model
{
    //
    public function media(){
        return $this->hasone('App\Media', 'id' ,'media_id');
       // return $this->belongsToMany('App\Media', 'media_sliders', 'slider_id' ,'media_id');
    }
}
