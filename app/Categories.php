<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Products;

class Categories extends Model
{
    //
 

    public function getCategoryItemsByslug($slug = false, $itemsPerPage, $pageNo)
    {
    	ini_set('max_execution_time', 0);
    	if($slug)
    	{
    		$category =  Categories::select('id')->where('slug', $slug)->first();	

    		DB::enableQueryLog();

			$query  = Products::query();
			if($category!='') 
			{
				$query->whereHas('Categories', function ($query) use ($category) {
					$query->where('categories.id', '=', $category->id);
				});
			 

				$args = [] ;

				$query->select([ 'id', 'sku', 'name', 'slug', 'regular_price', 'sale_price', 'sale_from', 'sale_to', 'featured_image_id'])
						//->with('Categories')
						->with('Inventories')   
						->with('media_featured_image')
							->where(function($query) use ($args){
								$query->where('products.product_type', '<>', 'variation');
								$query->where('products.status', 'publish');
								$query->where('products.visibility',  'visible'); 
								$query->where('products.published_at', '<', 'now()');
								 
							});


			 	$total_count = $query->get()->count();				

				if($pageNo > 1 )			
				{ 
					$offset = (($pageNo-1) * $itemsPerPage); 				 	
				}else{
					$offset = 1; 
				}
				
				$query->limit($itemsPerPage)->offset($offset); 			

				

				$products = $query->get();
				//$products = $query->toSql();
				//dd($products);
				//$query = DB::getQueryLog();
				//dd($query);
				//dd($products);  
				 if($total_count == 0)
				 {
					return array( 'error' => 1 , 'error_msg' => "No products found",  'total_count' => 0); 
				 }else{
				 	return array( 'data' => $products, 'error' => 0 , 'total_count' => $total_count );
				 }
				
			}else{
				return array( 'error' => 1 , 'error_msg' => "No such category found",  'total_count' => 0);
			}
		}
    } 


	public function getCategoryItemsByslug2($slug = false)
    {
    	ini_set('max_execution_time', 0);
    	if($slug)
    	{
    		$category =  Categories::select('id')->where('slug', $slug)->first();	

    		DB::enableQueryLog();

			 
			if($category) 
			{
				$catID = $category->id;
                $banner = '';
                //echo $catID;exit;
                $banner_info = \DB::table('banners')
                    ->leftJoin('media', 'media.id', '=', 'banners.banner_id')
                    ->where('banners.page_id', '=', $catID)
                    ->where('banners.banner_type', '=','desktop-product-category')
                    ->select('media.path')
                    ->first();
                if($banner_info)
                {
                    $banner = $banner_info->path;
                }
				if($this->sortedExist($catID))
					{
							$products = DB::connection($this->connection)
									->table('category_products_sorted')
									->select(	'products.id', 
												'products.name', 
												'products.slug',
											 	'products.regular_price', 
											 	'products.sale_price', 
											 	'products.sale_from', 
											 	'products.sale_to',  
											 	'products.featured_image_id',
											 	'products.slug',
												'category_products_sorted.category_id',
												'category_products_sorted.product_id',
												'category_products_sorted.sort_index',
												'media.path' 
											)
									->where('category_id', $catID)
                                    ->where('products.status',  'publish')
									->leftJoin('products', function($join){
										 $join->on('products.id', '=', 'category_products_sorted.product_id');				
									})
									->leftJoin('media', function($joinm){
										 $joinm->on('products.featured_image_id', '=', 'media.id');				
									})
									->orderBy('category_products_sorted.sort_index', 'ASC')
									->paginate(120); 

									 
								$loadfrom = 'sroted'; 			
					}else{
							$query  = Products::query();
							 $query->whereHas('Categories', function ($query) use ($catID) {
									$query->where('categories.id', '=', $catID);
								}); 

								$args = []; 
								$query->select([ 'products.slug', 'products.id', 'sku', 'name', 'regular_price', 'featured_image_id' ])  
										->with('media_featured_image') 
										->where('products.product_type', '<>', 'variation')
                                        ->where('products.status',  'publish');
							    $query->orderBy('products.id', 'DESC'); 
							    //$query->limit(20); 

								$products = $query->paginate(120); 
								$loadfrom = 'main';
					}

			 		 
					 
					return $arrayName = array('products' => $products, 'loadfrom' => $loadfrom, 'banner' => $banner);
				}else{
					return array('products' => [], 'loadfrom' => '', 'banner' => '');
				}
		}
    } 

    public function products(){
		return $this->belongsToMany('App\Products','category_product','category_id','product_id');
	}
 

	public function sortedExist($catID)
	{
		return DB::table('category_products_sorted')->where('category_id', $catID)->first();
	}
}
