<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use App\Media;
class Banners extends Model
{
    public $table = "banners";

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
    public function media(){
        return $this->hasone('App\Media', 'id' ,'banner_id');
       // return $this->belongsToMany('App\Media', 'media_sliders', 'slider_id' ,'media_id');
    }
    public function products()
    {
        return $this->hasManyThrough(
           'App\Products', 'App\Media' ,
           'media_id',  'media_id', 'featured_image_id'
        );
    }

    public function getAllBanners(){
        $mobProdCatBanners =   Banners::with('media')->where('banner_type' , '=' , 'mobile-product-category')->orderBy('id', 'asc')->get();
        $mobPageBanners =   Banners::with('media')->where('banner_type' , '=' , 'mobile-page')->orderBy('id', 'asc')->get();
        $desProdCatBanners =   Banners::with('media')->where('banner_type' , '=' , 'desktop-product-category')->orderBy('id', 'asc')->get();
        $desPageBanners =   Banners::with('media')->where('banner_type' , '=' , 'desktop-page')->orderBy('id', 'asc')->get();

        $cat_array = array();
        $mobProdCatData = array();
        $mobPageData = array();
        $desProdCatData = array();
        $desPageData = array();
       // dump($mobProdCatBanners);
        foreach($mobProdCatBanners as $item){

            $mobProdCatData[$item->page_id] = array(
                'id' => $item->page_id,
                'image_id' => $item->banner_id,
                'path' => $item['media']['path'],
            );
        }
        foreach($mobPageBanners as $item){

            $mobPageData[$item->page_id] = array(
                'id' => $item->page_id,
                'image_id' => $item->banner_id,
                'path' => $item['media']['path'],
            );
        }
        foreach($desProdCatBanners as $item){

            $desProdCatData[$item->page_id] = array(
                'id' => $item->page_id,
                'image_id' => $item->banner_id,
                'path' => $item['media']['path'],
            );
        }
        foreach($desPageBanners as $item){

            $desPageData[$item->page_id] = array(
                'id' => $item->page_id,
                'image_id' => $item->banner_id,
                'path' => $item['media']['path'],
            );
        }

     //  dump($mobProdCatData);
        $data = array('mobProdCatBanner'  => $mobProdCatData, 'mobPageBanner' => $mobPageData,'desProdCatBanner'  => $desProdCatData,'desPageBanner' => $desPageData);
        return $data;
    }
   

    public function saveMobCatBanners($input){


        $count = Banners::where('banner_type' , '=' , 'mobile-product-category')->count();
        if($count > 1){
            Banners::where('banner_type' , '=' , 'mobile-product-category')->delete();
        }
         $slider_type = 'mobile-product-category';
        foreach($input['mobProdCat'] as $mob){

            $page_id =  $mob['page_id'];
            $imgid =  $mob['imageId'];
            $data = array(
                'banner_id' 	    => $imgid,
                'page_id' 			=> $page_id,
                'banner_type'        => $slider_type,
            );
            DB::connection($this->connection)->table('banners')->insert($data);

        }   
    }
    public function saveDesCatBanners($input){


        $count = Banners::where('banner_type' , '=' , 'desktop-product-category')->count();
        if($count > 1){
            Banners::where('banner_type' , '=' , 'desktop-product-category')->delete();
        }
        $slider_type = 'desktop-product-category';
        foreach($input['desProdCat'] as $des){

            $page_id =  $des['page_id'];
            $imgid =  $des['imageId'];
            $data = array(
                'banner_id' 	    => $imgid,
                'page_id' 			=> $page_id,
                'banner_type'        => $slider_type,
            );
            DB::connection($this->connection)->table('banners')->insert($data);

        }
    }
    public function saveMobPageBanners($input){

        $count = Banners::where('banner_type' , '=' , 'mobile-page')->count();
        if($count > 1){
            Banners::where('banner_type' , '=' , 'mobile-page')->delete();
        }
        $slider_type = 'mobile-page';
        foreach($input['mobPage'] as $mob){

            $page_id =  $mob['page_id'];
            $imgid =  $mob['imageId'];
            $data = array(
                'banner_id' 	    => $imgid,
                'page_id' 			=> $page_id,
                'banner_type'        => $slider_type,
            );
            DB::connection($this->connection)->table('banners')->insert($data);

        }
    }
    public function saveDesPageBanners($input){

        $count = Banners::where('banner_type' , '=' , 'desktop-page')->count();
        if($count > 1){
            Banners::where('banner_type' , '=' , 'desktop-page')->delete();
        }
        $slider_type = 'desktop-page';
        foreach($input['desPage'] as $des){

            $page_id =  $des['page_id'];
            $imgid =  $des['imageId'];
            $data = array(
                'banner_id' 	    => $imgid,
                'page_id' 			=> $page_id,
                'banner_type'        => $slider_type,
            );
            DB::connection($this->connection)->table('banners')->insert($data);

        }
    }


}
