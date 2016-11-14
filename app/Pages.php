<?php

namespace App;

use DB;
use Auth;
use App\functions\Functions;
use Illuminate\Database\Eloquent\Model;
use App\Posts;
class Pages extends Model
{
    //

    public $timestamps = false;
    public $table = "posts";

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public  function seo_details()
    {
        return $this->hasone('App\SeoData', 'page_id' ,'id');
    }


    public function getauthorName($id){

        $name = DB::table('users')->where('id', $id)->pluck('username');
        return $name[0];
    }


    public function getPageIdBySlug($slug){

        $id = Pages::where('slug', $slug)->where('type', 'page')->pluck('id')->first();
        return $id;
    }




    public function getPageById($id,$device){
       
        if($device == 'desktop' || $device == 'tablet' ){
            return   $page = DB::connection($this->connection)
                ->table('posts')
                ->select(
                    'posts.slug',
                    'posts.title',
                    'posts.description',
                    'media.path'
                )
                ->where('posts.id', $id)
                ->where('posts.type', 'page')
                ->leftJoin('banners', function($join){
                    $join->on('posts.id', '=', 'banners.page_id')->where('banner_type', '=' , 'desktop-page');
                })
                ->leftJoin('media', function($joinm){
                    $joinm->on('banners.banner_id', '=', 'media.id');
                })->first();
        }
        else{
        return   $page = DB::connection($this->connection)
            ->table('posts')
            ->select(
                'posts.slug',
                'posts.title',
                'posts.description',
                'media.path'
            )
            ->where('posts.id', $id)
            ->where('posts.type', 'page')
            ->leftJoin('banners', function($join){
                $join->on('posts.id', '=', 'banners.page_id')->where('banner_type', '=' , 'mobile-page');
            })
            ->leftJoin('media', function($joinm){
                $joinm->on('banners.banner_id', '=', 'media.id');
            })->first();
    }
    }
  
}
?>