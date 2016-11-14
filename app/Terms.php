<?php

namespace App;

use App\Functions\Functions;
use Illuminate\Database\Eloquent\Model;
use DB;


class Terms extends Model
{

    public  $timestamps = false;
    public $table = 'terms';
 
    public function productattributes(){
        return $this->belongsTo('App\ProductAttributes', 'attributes_id');
    }
     
    public  function  getTermsbyIdAttid($id){

        return $terms = Terms::where('attributes_id', '=',  $id )
            ->orderBy('term_index', 'asc')->paginate(60);
 
    }

    public function getAttName($id){
        return ProductAttributes::where('id', '=',  $id )->value('name');

    }

    public function getTermById($id){
        return Terms::where('id', $id)->orderBy('term_index', 'asc')->first();  
    }

    public function getTermBySlug($slug){
        return Terms::where('slug', $slug)->orderBy('term_index', 'asc')->first();
    }
     
 
    public  function termUpdatebyId($id, $arr){
        
        $name = trim($arr['name']);
        $desc = trim($arr['desc']);
        $slugstr = trim($arr['slug']);
        $slug =	Functions::makeSlug($slugstr ,$duplicates_count = 0 ,$this->table, $id, $type= '');
        $dataupdate = array(
            'name' 			=> $name,
            'slug' 			=> $slug,
            'description'   => $desc,
            'updated_at'	=> date('Y-m-d H:i:s')
        );
        $q=	Terms::where('id', $id)->update($dataupdate);


    }
    public function save_term_indexes(){
        $id = trim($_POST['id']);
        $indexid = trim($_POST['indexid']);
        $dataupdate_index = array( 'term_index' => $indexid);
         $q = Terms::where('id',$id)->update($dataupdate_index);

    }

    public function getAttributeIDByTerm($term)
    {
         
        return Terms::select('attributes_id')->where('terms.slug',$term)->first();
    }

    public function checkIfExistByAttributeId($att_id = false, $slug = false)
    {
        $count = 0 ;
        if($att_id && $slug)
        {
            $count = Terms::where('slug',$slug)->where('attributes_id',$att_id)->get()->count();
        }

        return $count;
    }

    public function attribute()
    {
        return $this->hasMany('App\ProductAttribute', 'product_id', 'id');
    }
    
}
