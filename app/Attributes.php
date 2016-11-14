<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Attributes extends Model
{
	public $table = "attributes";
	public  $timestamps = false;
 

	public function products(){
		return $this->hasOne('App\Products','product_id','id');
	}

	public function terms()
	{
		return $this->hasMany('App\Terms', 'attribute_id', 'id');
	}
	public  function  getattributesbyId($id){
		return Attributes::where('id', $id)->first();
	}
	public  function attributeUpdatebyId($id, $arr){
		//dd($arr);
		$name = trim($arr['name']);
		//$slug = trim($arr['slug']);
		$slug= str_slug($arr['slug'] , "-");
		$slug =	$this->checkSlug($slug);
		$dataupdate = array(
			'name' 			=> $name,
			'slug' 			=> $slug,
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		$q=	Attributes::where('id', $id)
			->update($dataupdate);

	}
	public function fGetAllAttributes(){

		return Attributes::with('Terms')->orderBy('term_index', 'asc')->get();


	}  
	 

	public  function  getAttributesByProductId($id){
		return Attributes::where('product_id', $id)->first();
	}
	
	

	

}
