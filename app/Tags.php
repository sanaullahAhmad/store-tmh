<?php

namespace App;

use App\Functions\Functions;
use DB; 


class Tags extends Model
{
    //
	 public $table = "tags";

	 

    public function products(){
        return $this->belongsToMany('App\Products','product_tag','tag_id','product_id');
    }

	public function AddnewTag($array)
	{
		$slugstr = trim($array['slug']);
		$slug =	 Functions::makeSlug($slugstr,$duplicates_count = 0 ,$this->table, $id ='', $type= '');
		$name = trim($array['name']);
		$desc = trim($array['desc']);

		$data = array(
			'name' 			=> $name,
			'slug' 			=> $slug,
			'description'   => $desc,
			'created_at'	=> date('Y-m-d H:i:s')
		);
	
		Tags::insert( $data );

		return redirect('tags');


	}
	public  function  getTagbyId($id){
		return Tags::where('id', $id)->first();


	}
	public  function tagUpdatebyId($id, $arr){
		
		$name = trim($arr['name']);
		$desc = trim($arr['desc']);
		$slug = trim($arr['slug']);

		$slug =	 Functions::makeSlug($slug,$duplicates_count = 0 ,$this->table, $id, $type= '');
		$dataupdate = array(
			'name' 			=> $name,
			'slug' 			=> $slug,
			'description'   => $desc,
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		$q=	Tags::where('id', $id)
			->update($dataupdate);
		
	}
	
	public function fAddTags($name){
		
		$slug =str_slug($name);
		
		$data = array(
						'name'	=> $name,
						'slug'	=> $slug
					 );
		Tags::insert( $data );
		
		$lastInsertedId = DB::connection($this->connection)->getPdo()->lastInsertId();
		
		return $lastInsertedId;
	}
	
	public function fAddProductWithTag($tag_id, $product_id){

		$data = array(
						'product_id' => $product_id,
						'tag_id' => $tag_id

					 );

		return DB::connection($this->connection)->table('product_tag')->insert( $data );
	}
	
	public function fGetAllTags(){
		return Tags::get();
	}
	
	
	public function fDeleteTagWithProduct($tag_id, $product_id){
		
		return DB::connection($this->connection)->table('product_tag')
				->where(function($query) use ($tag_id,$product_id){
					
					$query->where('tag_id',$tag_id)
						  ->where('product_id', $product_id)->delete();
					
				});
	}

	public function listAllTags()
	{
		return $tags = Tags::orderBy('id', 'desc')->paginate(60);
	}



	public function getCountProductsOfTags($tagId)
	{
		return  DB::connection($this->connection)->table('product_tag')->where('tag_id', $tagId)->count();
	}
    public function fsearchtags($keyword){

        $keyword = "%".str_replace('+','',$keyword)."%";
        return $tags = 	 Tags::with('products')
            ->where('name', 'like',  $keyword )
            ->orderBy('id', 'desc')->paginate(60);
    }


}
