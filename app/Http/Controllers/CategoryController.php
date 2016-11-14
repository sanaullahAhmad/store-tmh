<?php

namespace App\Http\Controllers;

use Mail;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Customers;
use App\Categories; 
use App\Functions\Functions;
use Cache;
use Illuminate\Support\Facades\Auth;

class CategoryController extends BaseController
{
    protected $category;
    private $customers;
    public function __construct()
    {
        parent::__construct();
        $this->customers    = new Customers();
        $this->category = new Categories();
        $this->user         = Auth::user();
    }

    //
    public function index($slug)
    {
        $menu           = $this->menu;
        $footerMenu     = $this->footerMenu; 
        $device         = $this->device;
        $deviceName     = $this->deviceName;
        
        $category = str_replace("-", " ", $slug);
        $category = preg_replace("/[^A-Za-z0-9?!]/"," ",$slug);

        if($slug=='tryouts')
        {
            $tryouts='yes';
        }
        else{
            $tryouts='no';
        }

        $products = $this->getCetegoryProducts2($slug);

        //dd($products);
        return view('category'  ,[ 'products' => $products['products'], 'tryouts' => $tryouts, 'loadfrom' => $products['loadfrom'],
            'category' => $category, 'slug' => $slug, 'menu'=> $menu, 'footerMenu' => $footerMenu, 'device' => $device,
            'deviceName' => $deviceName, 'banner' => $products['banner'] ]);
    }

    public function belowfold($slug)
    {
        $menu = $this->menu;
        $footerMenu = $this->footerMenu;
        $device = $this->device;
        $deviceName = $this->deviceName;

        $category = str_replace("-", " ", $slug);
        $category = preg_replace("/[^A-Za-z0-9?!]/", " ", $slug);

        if ($slug == 'tryouts') {
            $tryouts = 'yes';
        } else {
            $tryouts = 'no';
        }

        $products = $this->getCetegoryProducts2($slug);
        $loopcounter = 0;
        if (!empty($products)) {
            foreach ($products['products'] as $product) {
                $loopcounter += 1;
                //echo "<pre>";print_r($product->slug); exit;
                if ($loopcounter > 20) {
                    ?>
                    <div class="col-sm-3" style="text-align: center; float: left; width: 25%;">
                        <?php if ($tryouts == 'yes') { ?>
                            <span class="spanlike spanlike_{{ $product->id }}"
                                  ng-click="liketryout( <?php echo $product->id;?>,1)">&nbsp;</span>
                        <?php } ?>

                        <a href="<?php url(); ?>/product/<?php echo $product->slug; ?>"
                           title="<?php echo $product->name; ?>">
                            <?php if ($products['loadfrom'] == 'main') {
                                if ($product->media_featured_image) { ?>
                                    <img src="<?php echo categoryPageThumbnail($product->media_featured_image->path) ?>"
                                         alt="<?php echo $product->name; ?>">
                                <?php }

                            } else { ?>
                                <img src="<?php echo categoryPageThumbnail($product->media_featured_image->path); ?>"
                                     alt="<?php echo $product->name; ?>">
                                <?php
                            } ?>


                            <div class="price_detials" style="margin-bottom: 15px; height: 55px;">
                                <p class="product-name"
                                   style="padding-bottom: 5px;">  <?php echo $product->name; ?> </p>
                                <?php if ($product->sale_price != 0 && strtotime($product->sale_from) <= time() && strtotime($product->sale_to) >= time()) { ?>
                                    <span class="regular-price line-through">
                                  <?php echo money($product->regular_price) ?>
                                </span>
                                    <span class="sale-price">
                                    <?php echo money($product->sale_price); ?>
                                </span>
                                <?php } else {
                                    echo money($product->regular_price);
                                } ?>

                            </div>

                        </a>

                        <?php if ($tryouts == 'yes') { ?>
                            <span class="spandeslike spandeslike_<?php $product->id; ?>"
                                  ng-click="liketryout( {{ $product->id }},0)">X</span>
                        <?php } ?>

                    </div>
                    <?php
                }
            }

        }

    }

    public function getcat_sidebar_prod_slider($slug)
    {
        $mainSliderImages  = array();
        $allSlided         = array();

        /*this was previous code show all musthaves in slider
         * $products = $this->getCetegoryProducts2($slug);
        $products = $products['products'];
        foreach ($products as $slide) {
            $mainSliderImages['id'] = $slide->id     ;
            $mainSliderImages['img'] = categoryPageThumbnail($slide->media_featured_image->path);
            $mainSliderImages['link'] = url('').'/product/'.$slide->slug;
            $allSlided[] =  $mainSliderImages;
        }*/

        $musthave_deal_widgetSliders =  \DB::select('select m.path, a.media_id ,p.name ,p.slug ,p.sale_price ,p.sale_from ,p.sale_to
                                               ,p.regular_price from sliders a
                                               left join products p on a.media_id = p.id
                                               left join media m on p.featured_image_id = m.id
                                               where a.slider_type = "musthave_deal_widget" order by a.id asc ');


        foreach ($musthave_deal_widgetSliders as $slide) {
            if($slide->sale_price != 0 && strtotime($slide->sale_from) <= time() && strtotime($slide->sale_to) >= time())
            {
                $mainSliderImages['pricemarkup'] ='<span class="regular-price line-through" >'. money($slide->regular_price) .'</span >
                                                   <span class="sale-price" >'.money($slide->sale_price) .'</span >';
            }
            else
            {
                $mainSliderImages['pricemarkup'] =money($slide->regular_price);
            }
            $mainSliderImages['id'] = $slide->media_id     ;
            $mainSliderImages['img'] = categoryPageThumbnail($slide->path);
            $mainSliderImages['link'] = url('').'/product/'.$slide->slug;
            $allSlided[] =  $mainSliderImages;
        }

        return  json_encode($allSlided);
    }

    public function getCetegoryProducts2($slug = false)
    {
        $products = array(); 
        if($slug)
        {
            $products = Cache::get('product-'.$slug.Request::capture()['page'], function() use ( $slug ){
                return $this->category->getCategoryItemsByslug2($slug);
            });

        }  

        return $products; 
    }

    public function getCategoryProducts($slug, $itemsPerPage, $pageNo)
    {
        if($slug)
        {
            $productsDB = $this->category->getCategoryItemsByslug($slug, $itemsPerPage, $pageNo);

            $products = array(); 
            if(isset($productsDB['data']))
            {

                foreach ($productsDB['data'] as $product) {
                     
                    if(isset( $product->media_featured_image)) 
                        $product->setAttribute('thumbnail', Functions::categoryPageThumbnail($product->media_featured_image->path)); 
                    else
                        $product->setAttribute('thumbnail', ''); 

                    $products[] = $product;  
                }

                $productsDB['data'] = $products; 
            }
            
            $products = $productsDB; 
        }else{
            $products = json_encode(array());
        }

        return $products; 

        
    }

    public function show($slug = 'home')
    {
        $page = page::whereSlug($slug)->first();
        return View::make('pages.index')->with('page', $page);
    }

    public function tryouts_like($product_id,$like)
    {
        $pro_id  =  $product_id;
        $user_id = 0;
        if($this->user) {
            $user = $this->customers->getCustomer($this->user->id);
            $user_id = $user->id;
        }


        if($like=='0'){ $unlike = 1;} else { $unlike = 0; }
        \DB::table('tryouts_feedback')->insertGetId(
            [
                'user_id'     => $user_id,
                'product_id'  => $pro_id,
                'like'        => $like,
                'unlike'      => $unlike,
                'created_at'  => date('Y-m-d H:i:s'),
            ]
        );
        $message = "Tryout liked successfully.";
        //
        $result['likes'] = array('product_id' => $pro_id, 'is_logged_in'=> $user_id, 'message'=>$message);
        echo json_encode($result);
    }


}
