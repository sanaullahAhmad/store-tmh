<?php
namespace App\Functions;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App; 
use DB;
use Session;
use NumberFormatter;
use Config; 

class Functions{


    public static function checkIdInDB( $id , $table)
    {
 
        $query = DB::connection(Session::get('connection'))->table($table);
        $query->where('id', '=', $id);
        return $query->count();
    }


   
    
    public static function validPrice($price = false)
    {
        if($price)
        {
            if($price!=''  && $price != '0' && $price !='0.00' )
            {
                return true;
            }
        }

        return false;
    }

    public static function priceForDB($price = false)
    {
        if($price)
        {
            // 123.45,67
            $cleanString = preg_replace('/([^0-9\.,])/i', '', $price);
            $onlyNumbersString = preg_replace('/([^0-9])/i', '', $price);

            $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

            $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
            $removedThousendSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);

            return (float) str_replace(',', '.', $removedThousendSeparator);
        }
    }



    public static function moneyForField($money)
    {
        if(Functions::validPrice($money)) {

            $fmt = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
            $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
            $format_money = $fmt->format($money);

            if (intl_is_failure($fmt->getErrorCode())) {
                return report_error("Formatter error");
            }

            return $format_money;
        }
    }




    public static function money($money)
    {
        if(Functions::validPrice($money)) {


            $fmt = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
             $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
            $money = $fmt->format($money);
            $symbol = $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
            $money = $symbol . $money;

            if (intl_is_failure($fmt->getErrorCode())) {
                return report_error("Formatter error");
            }


            return $money;
        }
    }

    public static function getCurrencySymbol()
    {
        $fmt = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
        $symbol = $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
        return $symbol;
    }




    public static function waitList($meta)
    {

        $wait_list = array();
        if(!is_array($meta))
        {
            return $wait_list; 
        }
        foreach($meta as $item)
        {
            if(!empty($item) && $item->meta_value!='')
            {
                $wait_list = unserialize($item->meta_value);
            }
        }

        return $wait_list;
    }

    public static function getThumbnail($url = false, $size = false)
    {
        //no image
        if (!$url || !$size) {
            return false;
        }  

        $img_url = Config::get('constants.IMG_PATH').$url;
        $img_path = $url;
        $img_ext = pathinfo($img_url, PATHINFO_EXTENSION);
        $small_thumb = str_replace($img_ext, '', $img_url);
        $img_path = str_replace($img_ext, '', $img_path);
        $small_thumb = rtrim($small_thumb, '.');
        $img_path = rtrim($img_path, '.');
        $small_thumb = $small_thumb.'-'.$size.'.'.$img_ext;
        $img_path = $img_path.'-'.$size.'.'.$img_ext; 
        return $small_thumb; 
    }


    public static function categoryPageThumbnail($url = false)
    {
        //no image
        if (!$url) {
            return false;
        }  

        $img_url = Config::get('constants.IMG_PATH').$url;
        $img_path = $url;
        $img_ext = pathinfo($img_url, PATHINFO_EXTENSION);
        $small_thumb = str_replace($img_ext, '', $img_url);
        $img_path = str_replace($img_ext, '', $img_path);
        $small_thumb = rtrim($small_thumb, '.');
        $img_path = rtrim($img_path, '.');
        $small_thumb = $small_thumb.'-'.'186x295'.'.'.$img_ext;
        $img_path = $img_path.'-'.'186x295'.'.'.$img_ext; 
        return $small_thumb; 
    }

    public static function productThumbnail($url = false)
    {
        //no image
        if (!$url) {
            return false;
        }

        $img_url = Config::get('constants.IMG_PATH').$url;
        $img_path = $url;
        $img_ext = pathinfo($img_url, PATHINFO_EXTENSION);
        $small_thumb = str_replace($img_ext, '', $img_url);
        $img_path = str_replace($img_ext, '', $img_path);
        $small_thumb = rtrim($small_thumb, '.');
        $img_path = rtrim($img_path, '.');
        $small_thumb = $small_thumb.'-'.'55x85'.'.'.$img_ext;
       // $img_path = $img_path.'-'.'186x295'.'.'.$img_ext;
        return $small_thumb;
    }

    public static function productImage($url = false)
    {
        //no image
        if (!$url) {
            return false;
        }

        $img_url = Config::get('constants.IMG_PATH').$url;
        return $img_url;
    }

    /*price calculation function
   *
    * @param product object
    * return product html
    */

    public static function getPrice($product){
        if($product->sale_price != 0){
            if($product->sale_from == "0000-00-00 00:00:00"){
                return @"<span class=\"price-tag\"><del>".Functions::money($product->regular_price)."</del>".Functions::money($product->sale_price)."</span>";
            }else if (strtotime($product->sale_from) <= time() && strtotime($product->sale_to) >= time()){
                return @"<span class=\"price-tag\"><del>".Functions::money($product->regular_price)."</del>".Functions::money($product->sale_price)."</span>";
            }
           else{
                return @"<span class=\"price-tag\">".Functions::money($product->regular_price)."</span>";
            }
        }
        else{
            return @"<span class=\"price-tag\">".Functions::money($product->regular_price)."</span>";
        }
    }

    public static function arrayToPaginate($request,$array){
        //Get current page form url e.g. &page=6
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        //Create a new Laravel collection from the array data
        $collection = new Collection($array);
        //Define how many items we want to be visible in each page
        $perPage = 10;
        //Slice the collection to get the items to display in current page
        $currentPageSearchResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        //Create our paginator and pass it to the view
       $paginatedSearchResults= new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);
        $paginatedSearchResults->setPath($request->url());
        $paginatedSearchResults->appends($request->except(['page']));
        return $paginatedSearchResults;

    }


    public static function getRandomString($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789*%#_-';

        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result ;
    }

}


?>