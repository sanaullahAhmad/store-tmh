<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    //file to load product from slug
    //@slug = string
    public function getProductFromSlug($slug=""){
        if(!empty($slug)){
            return Products::where('slug','=',$slug)
                ->with('tabs')
                ->with('media')
                ->with('media_featured_image')
                ->with('linkedProducts')
                ->with('meta')
                ->with('children')
                ->with('components')
                ->first();
        }
        return false;
    }

    //to get products for upsells
    public function getProducts($id){
        if(!empty($id)){
        return Products::wherein('id',$id)
            ->with('media_featured_image')
            ->with('media')
            ->get();
        }
        return false;
    }

    //find single product for add to cart function
    public function getProduct($id){
        if(!empty($id)){
            return Products::where('id','=',$id)
                ->with('media_featured_image')
                ->with('inventories')
                ->first();
        }
        return false;
    }
    /* 
		Relations  
    */

	public function tags(){
		return $this->belongsToMany('App\Tags','product_tag','product_id','tag_id');
	}

	public function categories(){
		return $this->belongsToMany('App\Categories','category_product','product_id','category_id');
	}

	public function getVariationTitle($var_id){
	    if($var_id != 0)
	        return Products::where('id','=',$var_id)
                ->with('attributes')
            ->with('parent')->first();
        else
            return false;
    }
	/*public function attributes(){
		return $this->belongsToMany('App\Attributes','products_attributes','products_id','attributes_id')->select('*');
	}*/

    // get product variations

    public function getProductVariations($id = false){
        $variations = array();

        if($id)
        {
            return Products::with('meta')->with('attributes')->with('inventories')->Where('parent_id', '=', $id)->orderBy('id', 'asc')->get();
        }

        return $variations;
    }

	public function attributes()
	{
		return $this->hasOne('App\Attributes', 'product_id', 'id');
	}

	public function inventories(){
		return $this->hasOne('App\Inventories','product_sku','sku')->orderBy('stock_status');
	}

	public function linkedProducts()
	{
		return $this->hasOne('App\LinkedProducts', 'product_id', 'id');
	}



	public function meta()
	{
		return $this->hasMany('App\Metas', 'product_id', 'id');
	}

	public function components()
	{
		return $this->hasMany('App\Components', 'product_id', 'id');
	}

	public function tabs()
	{
		return $this->hasMany('App\Tabs', 'type', 'id');
	}

	public function parent()
	{
		return $this->belongsTo('App\Products', 'parent_id');
	}

	public function children()
	{
		return $this->hasMany('App\Products', 'parent_id');
	}

	

	public function media(){
		return $this->belongsToMany('App\Media', 'media_products', 'product_id' ,'media_id');
	}
	public function media_featured_image(){
		return $this->hasone('App\Media', 'id' ,'featured_image_id');
	}
	/*
		Relations end 
	*/
}
