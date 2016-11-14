<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Functions\Functions; 
use DB;

class PostTags extends Model
{
    //

    public $timestamps = false;
    public $table = "posttags";

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function posts(){
        return $this->belongsToMany('App\Posts', 'posts_posttags', 'posts_id', 'posts_posttag_id');
    }

    public function fGetAllPostTags(){
        return PostTags::paginate(60);
    }
    public function AllPostTags(){
        return PostTags::get();
    }
    public function listAllTags()
    {
        return $tags = PostTags::with('posts')->orderBy('id', 'desc')->paginate(60);
    }
    
    public function getCountPostsOfTags($tagId)
    {
        return  count(DB::connection($this->connection)->table('posts_posttags')->where('posts_posttag_id', $tagId)->get());
    }
    public function fsearchposttags($keyword){

        $keyword = "%".str_replace('+','',$keyword)."%";
        return $tags =  PostTags::with('posts')
            ->where('name', 'like',  $keyword )
            ->orderBy('id', 'desc')->paginate(60);
    }

    public function AddnewTag($array)
    {
        $slugstr = trim($array['slug']);
        $slug =  Functions::makeSlug($slugstr , $duplicates_count = 0 ,$this->table, $id = '',$type= '');
        $name = trim($array['name']);
        $desc = trim($array['desc']);

        $data = array(
            'name' 			=> $name,
            'slug' 			=> $slug,
            'description'   => $desc

        );
       
        return  PostTags::insert( $data );
     }
    public  function  getTagbyId($id){
        return PostTags::where('id', $id)->first();


    }
    public  function tagUpdatebyId($id, $arr){
        
        $name = trim($arr['name']);
        $desc = trim($arr['desc']);
        $slug = trim($arr['slug']);

        $slug =  Functions::makeSlug($slug , $duplicates_count = 0 ,$this->table, $id ,$type= '');
        $dataupdate = array(
            'name' 			=> $name,
            'slug' 			=> $slug,
            'description'   => $desc

        );
        $q=	PostTags::where('id', $id)->update($dataupdate);

    }
    public function DeletePostTags($input){
        if($input['remove'] == 'rm'){

            foreach($input['del'] as $key => $del){

               
                PostTags::where('id', '=',$del)->delete();
                DB::connection($this->connection)->table('posts_posttags')->where('posts_posttag_id', '=',$del)->delete();
            }


        }
     }


}
?>