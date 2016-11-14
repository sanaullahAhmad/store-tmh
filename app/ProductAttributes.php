<?php

namespace App;

use App\Functions\Functions;
use Illuminate\Database\Eloquent\Model;
use DB; 

class ProductAttributes extends Model
{

    public $table = "productattributes";
    public  $timestamps = false; 

    

    
    public function fGetAttributes(){ return ProductAttributes::where('type', 'default')->get(); }

    public function fGetProductsAttributes($attribute_id){

        return ProductAttributes::where('type', 'default')->get();

    }

    public function fGetProductsAttributesTerms($attribute_id){

        return Terms::where('attributes_id',$attribute_id)->orderBy('term_index', 'asc')->get();

    }

    public function getAttributeNameByID($id)
    {
        return ProductAttributes::select('name','type')->where('id',$id)->first();
    }
 

 
    public function products(){
        return $this->hasOne('App\Products','product_id','id');
    }

    public function terms()
    {
        return $this->hasMany('App\Terms', 'attributes_id', 'id');
    }
    public  function  getattributesbyId($id){
        return ProductAttributes::where('id', $id)->first();


    }
 
    public function fGetAllAttributes(){


        return ProductAttributes::where('type', 'default')->with(['Terms' => function($query)
        {
            $query->orderBy('term_index', 'asc');

        }])->get();


    }
 

 
    public function checkIfExistBySlug($slug = false)
    {

        $data   = array('count' => 0, 'att_id' => '');
        if($slug)
        {
            $data_db = ProductAttributes::where('slug',$slug)->first();

            if($data_db)
            {
                $data = array('count' => count($data_db), 'att_id' => $data_db->id);
            }

        }
        return $data;

    }

    public function getCount()
    {
        return ProductAttributes::count();
    }
    
    public function getTermsbyIdAttid($id){


        return ProductAttributes::with('Terms')
            ->where('id', $id)->orderBy('id', 'desc')->Paginate(2);
    }


}

