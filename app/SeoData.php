<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Functions\Functions;
use DB;



class SeoData extends Model
{

    public $timestamps = false;
    public $table = "seo_data";

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

 //////////////////////////Media relation with posts/////////////////

    public function saveSeo($input, $id, $type){
        
        

        $seo_title = trim($input['seo_title']);
        $seo_desc = trim($input['seo_desc']);
        $can_url = trim($input['can_url']);
        $red_url = trim($input['red_url']);

        if(isset($input['is_index'])){
            $index = trim($input['is_index']);
            if($index == 'index' ){ $is_index = 1;}else if($index == 'non-index')  { $is_index = 0;}}
        else{
            $is_index = 1;
        }
        if(isset($input['is_follow'])){
            $follow = trim($input['is_follow']);
            if($follow == 'follow'){ $is_follow = 1;}else if($follow == 'no-follow')  { $is_follow = 0;}
        }
        else{
            $is_follow = 1;
        }
        $dataSeo = array(
            'page_id'        => $id,
            'title'			 => $seo_title,
            'description' 	 => $seo_desc,
            'is_index'       => $is_index,
            'is_follow'		 => $is_follow,
            'canonical_url'	 => $can_url,
            'redirect'   	 => $red_url,
            'type'           => $type
        );
        SeoData::saveSeoDetails($dataSeo);
    }
    
    
    public static function saveSeoDetails($input)
    {
       
        $page_id = $input['page_id'];
        $type = $input['type'];
        $count = SeoData::where('page_id' , '=' , $page_id)->where('type' , '=' , $type)->count();

        if($count >= 1){
            SeoData::where('page_id' , '=' , $page_id)->where('type' , '=' , $type)->delete();
        }
        return SeoData::insert($input);
    }

    public function products(){
        return $this->hasone('App\Products');
    }
    public function posts(){
        return $this->hasone('App\Posts');
    }
    public function pages(){
        return $this->hasone('App\Pages');
    }

}


?>