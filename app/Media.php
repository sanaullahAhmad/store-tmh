<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Functions\Functions;
use App\Input;
use App\Products;
use Session; 
use DB;
use Config;  

class Media extends Model
{

    public $timestamps = false;
    public $table = "media";

  
 //////////////////////////Media relation with posts/////////////////

    public function posts()
    {
      return $this->belongsToMany('App\Posts' ,'media_posts' , 'post_id' , 'media_id');
    } 

    //////////////////////////Media relation with posts/////////////////

    public function products()
    {
        return $this->belongsToMany('App\Products' ,'media_products' , 'product_id' , 'media_id');
    }
///////////////////////Product Feature Image relation///////////////
    public function product_featured_image()
    {
        return $this->belongsTo('App\Products');
    }
    ///////////////////////Post Feature Image relation///////////////
    public function post_featured_image()
    {
        return $this->belongsTo('App\Posts');
    }

    public function user(){
        return $this->belongsTo('App\User','uploaded_by','id' );
    }

    public function sliders()
    {
        //return $this->belongsToMany('App\Sliders' ,'media_sliders' , 'slider_id' , 'media_id');
        return $this->belongsTo('App\Sliders');
    }
 

     
 


    ///////////////////////Show selected media detail/////////////////

    public function showMediaDetail($input)
    {
        $id = $input['img_id'];
        return Media::with('user')->where('id', '=', $id)->first();
    }

   

 
    function galleryThumb($url = false)
    {
        //no image

        if (!$url) {
            return false;
        }

        $admin_thumbnails       =   Config::get('adminpanel-thumbnails');

        $listing_thumb_size     =    $admin_thumbnails[Session::get('connection')]['add-edit-gallery'];
        $img_path = $url;
        $filename = basename($img_path);
        $img_ext = pathinfo($img_path, PATHINFO_EXTENSION);
        $img_path = str_replace($img_ext, '', $img_path);
        $img_path = rtrim($img_path, '.');
        $img_path = $img_path.'-'.$listing_thumb_size.'.'.$img_ext;



        if (file_exists($img_path)) {
            return $img_path;
        }else{
            return false;
        }
    }

}


?>