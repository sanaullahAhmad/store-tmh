<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Functions\Functions; 
use DB;

class PostCategories extends Model
{
    //

    public $timestamps = false;
    public $table = "postcategories";


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function posts(){
        return $this->belongsToMany('App\Posts', 'posts_postcategories','postcategory_id', 'posts_id' );
    }

    public  function seo_details()
    {
        return $this->hasone('App\SeoData', 'page_id' ,'id');
    }
    ///////////////////////////////////////////////////////////////////////////////////

   public function getPostsByCategory($slug){

     /*  $get_id = PostCategories::where('slug',$slug)->pluck('id');
       echo $id =  $get_id[0];
      return PostCategories::find($id)->with('posts')->paginate(10);*/
       return  $categoryPosts =  PostCategories::with('posts.media_featured_image')->where('slug',$slug )->first();
   }

    ///////////////////////////////////////SEARCH FUNCTION/////////////////////////
  public function  searchcat($keyword){

      $search_array =[];
      $keyword = "%".str_replace('+','',$keyword)."%";
      //  DB::connection()->enableQueryLog();
      $s =   PostCategories::where('name', 'like',  $keyword )->where('status','=', 'publish')->orderBy('id', 'desc')->get();
     //  dd(DB::getQueryLog($c));exit;

      foreach ($s as $searchCat){
               $search_array[] = [
              'id' 		            => $searchCat->id,
              'name' 	            => $searchCat->name,
              'description' 	    => $searchCat->description,
              'slug'                => $searchCat->slug,
              'category_count'      => $this->getCountPostsOfCategories($searchCat->id)
              ];

      }
        return $search_array;
  }
////////////////////////////////GET POST CATEGORIES PARENT & CHILD/////////////////////////
    public function getPostCategories($parentId = 0, $level=0)
    {
        $categories = [];
        $c = PostCategories::where('parent_category_id', $parentId)->where('status','=', 'publish')
            ->orderBy('id', 'desc')->get();

       foreach( $c  as $category)
        {
            $categories[] = [
                'category_id' 		=> $category->id,
                'category_name' 	=> $category->name,
                'description' 		=> $category->description,
                'status' 			=> $category->status,
                'category_slug' 	=> $category->slug,
                'parent_id' 	    => $category->parent_category_id,
                'category_level'	=> $level,
                'children' 			=> $this->getPostCategories($category->id, $level+=1),
                'product_count' 	=> $this->getCountPostsOfCategories($category->id)
            ];
            $level--;
        }
        return $categories;
    }
    /////////////////////////////////////////////////////////////////////////////////////////////

    public function getSelectedPostCategories($parentId = 0, $level=0 ,$post_id)
    {
        $categories = [];
        $c = PostCategories::where('parent_category_id', $parentId)->where('status','=', 'publish')
            ->orderBy('id', 'desc')->get();

        foreach( $c  as $category)
        {
            $categories[] = [
                'category_id' 		=> $category->id,
                'category_name' 	=> $category->name,
                'description' 		=> $category->description,
                'status' 			=> $category->status,
                'category_slug' 	=> $category->slug,
                'parent_id' 	    => $category->parent_category_id,
                'category_level'	=> $level,
                'children' 			=> $this->getSelectedPostCategories($category->id, $level+=1 ,$post_id),
                'product_count' 	=> $this->getCountPostsOfCategories($category->id),
                'selected'          => $this->getSelectedPostId($category->id ,$post_id)
            ];
            $level--;
        }
        return $categories;
    }
    /////////////////////////////////////////////////////////////////////////////////////

    public function getSelectedPostId($cat_id, $post_id){

        $countPostCategories =  count(DB::connection($this->connection)->table('posts_postcategories')->where('postcategory_id', $cat_id)->where('posts_id', $post_id)->get());
        if($countPostCategories > 0){
            return 'Selected';
        }
        else{
            return 'NotSelected';
        }

    }

    ////////////////////////////////////////SAVE CATEGORY////////////////////////////////////////
    public function fSavecategory($array)
    {
        $slugstr = trim($array['slug']);
        $slug =	 Functions::makeSlug($slugstr, $duplicates_count = 0 ,$this->table, $id = '',$type= '');

        $name = trim($array['name']);
        $desc = trim($array['desc']);
        $parent =trim($array['parent']);
        $status =trim($array['status']);
        $data = array(
            'name' 			=> $name,
            'slug' 			=> $slug,
            'description'   => $desc,
            'status'   => $status,
            'parent_category_id'  => $parent
        );

        PostCategories::insert( $data );
        $inserted_category_id = DB::connection($this->connection)->getPdo()->lastInsertId();
        return $inserted_category_id;

    }
    public function CreateCategory($category_name,$parent_category_id=0){

        $slug = str_slug($category_name);
        $data = array(
            'name'					=> $category_name,
            'slug'					=> $slug,
            'parent_category_id'	=> $parent_category_id
        );
        PostCategories::insert($data);
        $inserted_category_id = DB::connection($this->connection)->getPdo()->lastInsertId();
        return $inserted_category_id;
    }

    ///////////////////////////CHECK ID IN DB///////////////////////////////////////
    public function checkIdinDB($id){
        return  count(PostCategories::where('id', $id)->get());
    
    }
    /////////////////////////GET CATEGORY BY ID//////////////////////////////////////////////////////
    public function getCategoryById($id){

        return PostCategories::with(['seo_details' => function($query) {
            $query->where('seo_data.type', 'post-category');}])->where('id', $id)->where('status','=', 'publish')->first();

    }

    ////////////////GET CATEGORY WITHOUT GIVEN ID////////////////////////////////////////////////

    public function getCategoryNotId($parentId = 0, $level=0, $id){

        $categoriesNotId = [];
        $c =  PostCategories::where('parent_category_id', $parentId)->where('id','!=', $id)->where('status','=', 'publish')->get();
        foreach( $c  as $category)
        {
            $categoriesNotId[] = [
                'category_id' 		=> $category->id,
                'category_name' 	=> $category->name,
                'description' 		=> $category->description,
                'status' 			=> $category->status,
                'category_slug' 	=> $category->slug,
                'parent_id' 	    => $category->parent_category_id,
                'category_level'	=> $level,
                'children' 			=> $this->getCategoryNotId($category->id, $level+=1,$id),
                'product_count' 	=> $this->getCountPostsOfCategories($category->id)
            ];
            $level--;
        }
        return $categoriesNotId;
    }
    ////////////////////////////////////////////////UPDATE FUNCTION//////////////////////////////////////////////////
    public function fUpdateCategory($id, $arr){

        $name = trim($arr['name']);
        $desc = trim($arr['desc']);
        $parent =trim($arr['parent']);
        $status =trim($arr['status']);
        $slug = trim($arr['slug']);
        $slug =	Functions::makeSlug( $slug , $duplicates_count = 0 ,$this->table, $id ,$type= '');
        $dataupdate = array(
            'name' 			=> $name,
            'slug' 			=> $slug,
            'description'   => $desc,
            'status'   => $status,
            'parent_category_id'  => $parent
        );
        //DB::connection()->enableQueryLog();
        $q=	PostCategories::where('id', $id)->update($dataupdate);
        //$q = DB::getQueryLog();
        
    }
   /////////////////////////////////////////////////COUNT POSTS OF CATEGORY//////////////////////////////////
    public function getCountPostsOfCategories($catId)
    {
    return  count(DB::connection($this->connection)->table('posts_postcategories')->where('postcategory_id', $catId)->get());
    }

    ////////////////////////////////REMOVE CATEGORY////////////////////////////////////////////////////

    public function DeleteCategories($input){

        echo $input['remove'];

        if($input['remove'] == 'rm'){

            foreach($input['del'] as $key => $del){

                $this->delcatbyid($del);
            }

         }
    }
    public function delcatbyid($id){

       $count =   count(PostCategories::where('parent_category_id', $id)->get());

        if($count > 0){
            $del_ids = PostCategories::select('id')->where('parent_category_id', $id)->get();

            foreach($del_ids as $del_id){


                PostCategories::where('id', '=', $del_id->id )->update(['status' => 'deleted']);
                 
                DB::connection($this->connection)->table('posts_postcategories')->where('postcategory_id', '=', $del_id->id )->delete();
                $this->delcatbyid($del_id->id);
            }
            PostCategories::where('id', '=', $id)->update(['status' => 'deleted']);
            DB::connection($this->connection)->table('posts_postcategories')->where('postcategory_id', '=', $id )->delete();
         }
        else{
            
            PostCategories::where('id', '=', $id)->update(['status' => 'deleted']);
            DB::connection($this->connection)->table('posts_postcategories')->where('postcategory_id', '=', $id )->delete();

        }

     }

}
?>