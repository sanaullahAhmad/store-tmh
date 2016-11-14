<?php

namespace App\Http\Controllers;

use App\Menu;
use App\MenuDetails;
use App\Products;
use App\Sliders;
use Illuminate\Http\Request;

use App\Http\Requests;
use Jenssegers\Agent\Agent ;
use Config; 
use DB; 
use App\Functions\Functions; 


class HomeController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    //
    public function index()
    {
        $menu           = $this->menu;
        $device         = $this->device;
        $deviceName     = $this->deviceName;
        $footerMenu     = $this->footerMenu;
        $desHomeSliders =  Sliders::with('media')->where('slider_type' , '=' , 'desktop_homepage')->orderBy('id', 'asc')->get();
        if($device == 'mobile' || $device == 'tablet')
        {
          $homePageImages = $this->homePageImagesMobile(); 
        }else{
          $homePageImages = array();
        }
        
         

        return view('home'  ,[  'menu'            => $menu, 
                                'footerMenu'      => $footerMenu, 
                                'homePageImages'  => $homePageImages,
                                'device'          => $device, 
                                'deviceName'      => $deviceName,
                                'des_homeSliders' => $desHomeSliders
                                ]
                    );
    }

    public function getMainSliderImages()
    {
        $device         = $this->device;
        if($device == 'mobile' || $device == 'tablet')
        {
            $slides =  Sliders::with('media')->where('slider_type' , '=' , 'mobile')->orderBy('id', 'asc')->get();
        }else{
            $slides =  Sliders::with('media')->where('slider_type' , '=' , 'desktop')->orderBy('id', 'asc')->get();
        }//previously desktop_homepage


         $mainSliderImages  = array();
         $allSlided         = array();

         foreach ($slides as $slide) {
             $mainSliderImages['id'] = $slide->id     ;
             $mainSliderImages['img'] = Config::get('constants.IMG_PATH').$slide->media->path;
             $mainSliderImages['link'] = $slide->link_url;
             $allSlided[] =  $mainSliderImages;
         }
      
       return  json_encode($allSlided); 
    }

    public function getProductsSliderImages()
    { 
        
      $productSliders =  DB::select('select 
                                      m.path as img, 
                                      a.media_id ,
                                      p.id,
                                      p.name,
                                      p.regular_price as regular_price, 
                                      p.sale_price, 
                                      p.sale_from,
                                      p.sale_to,  
                                      p.slug, 
                                      p.product_type
                                      from sliders a
                                      left join products p on a.media_id = p.id
                                      left join media m on p.featured_image_id = m.id
                                      where a.slider_type = "product" order by a.id asc '); 

      $getProductsSliderImages  = array();

      foreach ($productSliders as $productSlide) {
          $pimages = [];
          $pimges['img']      =  Functions::getThumbnail($productSlide->img, '120x184') ;
          $pimges['name']      =  $productSlide->name;

          //$pimges['price'] = Functions::getPrice($productSlide);
          if($productSlide->product_type == 'variable')
          {

              $variations  = Products::select('id', 'regular_price', 'sale_price', 'sale_from', 'sale_to')->where('parent_id', $productSlide->id)->get();
              //dd($variations);
              if($variations) {
                  $variation_regular_prices  = array();
                  $variation_sale_prices  = array();
                  foreach ($variations as $variation)
                  {
                      $variation_regular_prices[] = $variation->regular_price;
                      $variation_sale_prices[] = $variation->sale_price;
                  }
                  //dd($variation_sale_prices);

                  $regular_price  = max($variation_regular_prices);
                  $sale_price  = min($variation_sale_prices);
                  if ($sale_price != 0 && $variation->sale_from <= time() && $variation->sale_to >= time()) {
                      $pimges['price'] =    Functions::money($regular_price);
                      $pimges['sale_price'] =    Functions::money($sale_price);
                  }else if ($sale_price != 0 && $variation->sale_from <= '0000-00-00 00:00:00' && $variation->sale_to >= '0000-00-00 00:00:00') {
                      $pimges['price'] =    Functions::money($regular_price);
                      $pimges['sale_price'] =    Functions::money($sale_price);
                  }else{
                      $pimges['price'] =  Functions::money($regular_price);
                      $pimges['sale_price'] =    '';
                  }
              }else{
                  $pimges['price'] = 0.00;
              }


          }else{
              if($productSlide->sale_price !=0 && $productSlide->sale_from <= time() && $productSlide->sale_to >= time())
              {
                  $pimges['price'] =    Functions::money($productSlide->regular_price);
                  $pimges['sale_price'] =    Functions::money($productSlide->sale_price);
              }else{
                  $pimges['price']    =  Functions::money($productSlide->regular_price)    ;
                  $pimges['sale_price'] =    '';
              }
          }


          $pimges['link']       =  Config::get('app.url').'/product/'.$productSlide->slug; 
          $getProductsSliderImages[] = $pimges; 
      }

        

      return json_encode($getProductsSliderImages); 

       
    }

    public function homePageImagesMobile()
    {
      return  Sliders::with('media')->where('slider_type' , '=' , 'mobile_homepage')->orderBy('id', 'asc')->get();
    }



}
