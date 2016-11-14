<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Functions\Functions; 
use DB;
use Auth;
use App\PostTags;
use App\PostCategories;
use App\Media;
use App\SeoData;
class Posts extends Model
{

    public $timestamps = false;
    public $table = "posts";


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function posttags(){
        return $this->belongsToMany('App\PostTags', 'posts_posttags', 'posts_id', 'posts_posttag_id');
    }

    public function postcategories(){
        return $this->belongsToMany('App\PostCategories', 'posts_postcategories', 'posts_id' ,'postcategory_id');
    }

    public function media(){
        return $this->belongsToMany('App\Media', 'media_posts', 'post_id' ,'media_id');
    }
    public function media_featured_image(){
        return $this->hasone('App\Media', 'id' ,'featured_image_id');
    }
    
    public function user(){
        return $this->belongsTo('App\User');
    }

    public  function seo_details()
    {
        return $this->hasone('App\SeoData', 'page_id' ,'id');
    }

    public function getallposts(){
        return Posts::with('PostTags')
            ->with('PostCategories')
            ->with('User')
            ->where('type' , '=' , 'post')
            ->orderBy('id', 'desc')
            ->paginate(60);
    }

    public function getallpostsSearch($args){
        // DB::connection()->enableQueryLog();
        $query = Posts::with('PostTags')
            ->with('PostCategories')
            ->with('User')->where('type' , '=' , 'post')
            ->where(function ($query) use ($args){
                foreach($args as $arg){
                    $query->Where(
                        'posts.'.$arg['column'] ,
                        $arg['operator'] ,
                        $arg['value']);
                }
         })->orderBy('id', 'desc');
       return $query->paginate(60);
        //$query = DB::getQueryLog($query);
    }

    public function getPostIdBySlug($slug){
        $id = Posts::where('slug', $slug)->where('type', 'post')->pluck('id')->first();
        return $id;
    }

    public function getPostById($id){
        return Posts::with('posttags')->with('media')
                                      ->with('media_featured_image')
                                      ->with(['seo_details' => function($query) {
                                              $query->where('seo_data.type', 'post');}])
                                      ->where('id', $id)
                                      ->where('type', 'post')
                                      ->first();

    }
    public function getPostCategoriesById($id){
       return DB::connection($this->connection)->table('posts_postcategories')->where('posts_id', $id)->pluck('postcategory_id');
        
    }
    public function getRandomPosts(){

      return  $random= DB::connection($this->connection)
            ->select('SELECT a.*,b.path FROM posts a 
                      left join media b on a.featured_image_id = b.id WHERE   month(published_at) = month(CURDATE()- INTERVAL 3 MONTH) ORDER BY RAND() LIMIT 0,2');
     


    }

}

?>