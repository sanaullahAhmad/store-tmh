<?php

namespace App\Http\Controllers;

use App\Attributes;
use App\Cart;
use App\CartDetails;
use App\Functions\Functions;
use App\Products;
use App\Tabs;
use App\Terms;
use Illuminate\Http\Request;

use App\Customers;
use App\Metas;

use Illuminate\Support\Facades\Auth;


use App\Http\Requests;

class ProductDetailsController extends BaseController
{
    protected $customers;
    protected $meta;

    public function __construct()
    {
        parent::__construct();

        $this->customers    = new Customers();
        $this->meta         = new Metas();
    }
    //
    public function index($slug){
        $menu           = $this->menu;
        $device         = $this->device;
        $deviceName     = $this->deviceName;
        $footerMenu     = $this->footerMenu;

        $objProduct = new Products();
        $product = $objProduct->getProductFromSlug($slug);
        $baseimages = $product['media'];
        $tabs = Tabs::getGlobalTabs();

       /* $upsells = $product['linkedProducts']['up_sells'];
        $upsells = explode('|',$upsells);*/



        $variations = null;
        $selector = null;
        if($product->product_type=='variable') {
            $variations = $objProduct->getProductVariations($product->id);
           if(isset($variations[0]->attributes->attributes)) {
               $term = unserialize($variations[0]->attributes->attributes);
               foreach ($term as $key => $value) {
                   $objTerm = new Terms();
                   $selector = $objTerm->getAttName($key);
               }
           }

        }

        $components = null;
        $prod_components = null;
        $type = null;
        if($product->product_type=='composite') {
            $components = $product->components;
            foreach($components as $component){

                if($component->type == 1) {
                    $comp_ids = explode('|', $component->default_id);
                    $type = 'multiple';
                }
                else {
                    $comp_ids[] = $component->default_id;
                    $type = 'single';
                }
                $all_comp_products = $objProduct->getProducts($comp_ids);
                $cp_var_data = null;
                foreach($all_comp_products as $cp_products){
                    if($cp_products->product_type=='variable') {
                        $variations = $objProduct->getProductVariations($cp_products->id);
                        if(isset($variations[0]->attributes->attributes)) {
                            $term = unserialize($variations[0]->attributes->attributes);
                            foreach ($term as $key => $value) {
                                $objTerm = new Terms();
                                $selector = $objTerm->getAttName($key);
                            }
                        }

                        $cp_var_data[$cp_products->id] = array('variations'=>$variations, 'selector'=>$selector);
                    }
                }
                $prod_components[] = array('product'=>$all_comp_products , 'title'=>$component->title,'description'=>$component->description,'type'=>$type, 'var_data'=>$cp_var_data);
            }

        }
        //dd($prod_components);

       /* $upsell_prods = $objProduct->getProducts($upsells);

        $upsells = null;
        $index = 0;
        foreach($upsell_prods as $item){
            $upsells[]=array(
                'price' => Functions::getPrice($item),
                'title' => $item['name'],
                'slug' => $item['slug'],
                'image' => Functions::categoryPageThumbnail($item['media_featured_image']['path'])
            );

            $index++;

            if($index >=4)
                break;
        }
*/
        $color_swatches=null;
        foreach($product['meta'] as $meta){
            if($meta['meta_name']=='ce_colors_swatches_prod'){
                $color_swatches = unserialize($meta['meta_value']);
            }
        }

        if( $color_swatches != null)
        $color_swatches = $objProduct->getProducts( $color_swatches);



        $price = Functions::getPrice($product);
        $featured = Functions::productImage($product['media_featured_image']['path']);
        $images = null;
        $images[] = array('thumb'=>Functions::productThumbnail($product['media_featured_image']['path']) , 'original' =>$featured);
        foreach ($baseimages as $path){
            $images[] = array('thumb'=>Functions::productThumbnail($path['path']) , 'original' =>Functions::productImage($path['path']));
        }

        $categories = \DB::table('category_product')->where('product_id', $product->id)->pluck('category_id');
        //var_dump($categories);

        if(in_array(12376, $categories))//10796
        {
            $tryouts='yes';
        }
        else{
            $tryouts='no';
        }
        //echo $tryouts;exit;

       // return view('prod_details'  ,[ 'product' => $product,'select_title'=>$selector,'price'=>$price,'upsells' => $upsells,'color_swatches'=>$color_swatches,'featured'=>$featured,'variations'=>$variations, 'tabs'=>$tabs ,'images'=>$images,'slug' => $slug, 'menu'=> $menu, 'device' => $device, 'deviceName' => $deviceName ]);
        return view('prod_details'  ,[ 'product' => $product, 'tryouts' => $tryouts,'footerMenu' => $footerMenu,'select_title'=>$selector,'price'=>$price,'color_swatches'=>$color_swatches,'featured'=>$featured,'variations'=>$variations,'components'=>$prod_components, 'tabs'=>$tabs ,'images'=>$images,'slug' => $slug, 'menu'=> $menu, 'device' => $device, 'deviceName' => $deviceName ]);

    }
    public function crosssells($slug){

        $objProduct = new Products();
        $product = $objProduct->getProductFromSlug($slug);

        $upsells = $product['linkedProducts']['up_sells'];
        $upsells = explode('|',$upsells);

        $upsell_prods = $objProduct->getProducts($upsells);

        $upsells = null;
        $index = 0;
        foreach($upsell_prods as $item){
            $upsells[]=array(
                'price' => Functions::getPrice($item),
                'title' => $item['name'],
                'slug' => $item['slug'],
                'image' => Functions::categoryPageThumbnail($item['media_featured_image']['path'])
            );

            $index++;

            if($index >=4)
                break;
        }

        return view('cross_sells'  ,[ 'upsells' => $upsells ]);
    }



    //add to cart
    public function addtocart(Request $request){
        $user = Auth::user();

        $input = $request->all();

        if(isset($input['type'])){
            $cartsession = 0;
            $cart = new Cart();
            if (!empty($input['cart_id'])) {
                $cart->updateCart($input['cart_id']);
                $cartsession = $input['cart_id'];
            }
            else{
                if ($user != null)
                    $data = array('user_id' => $user->id, 'created_at' => date('Y-m-d H:i:s'));
                else
                    $data = array('user_id' => 0, 'created_at' => date('Y-m-d H:i:s'));

                $cartsession = $cart->setCart($data);
            }

            $components = explode(',',$input['prodid']);
            foreach($components as $component) {
                $objProduct = new Products();
                $product = $objProduct->getProduct($component);


                if ($product->inventories->stock_qty > 0) {

                    if (!empty($input['cart_id'])) {
                        $cartdet = new CartDetails();
                        $existing = $cartdet->getDetails($component, 0);
                        if ($existing == null) {
                            $data = array(
                                'product_id' => $component,
                                'cart_id' => $input['cart_id'],
                                'domain_id' => 0,
                                'qty' => 1,
                                'price' => $product->regular_price,
                                'discount' => 0,
                                'created_at' => date('Y-m-d H:i:s')
                            );

                            $cartdet->addDetails($data);
                        } else {
                            $data = array(
                                'product_id' => $component,
                                'cart_id' => $input['cart_id'],
                                'domain_id' => 0,
                                'qty' => $existing->qty + 1,
                                //'price' => $product->regular_price,
                                //'discount' => 0,
                                'updated_at' => date('Y-m-d H:i:s')
                            );

                            $cartdet->updateDetails($existing->id, $data);

                        }

                    } else {
                        $cartdet = new CartDetails();
                        //$existing = $cartdet->getDetails($input['prodid'],0);
                        $data = array(
                            'product_id' => $component,
                            'cart_id' => $cartsession,
                            'domain_id' => 0,
                            'qty' => 1,
                            'price' => $product->regular_price,
                            'discount' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        );

                        $cartdet->addDetails($data);
                    }

                }
            }
            session(['cart_id'=>$cartsession]);
            echo json_encode(array('cart_id' => $cartsession));
        }
        else {

            $objProduct = new Products();
            $product = $objProduct->getProduct($input['prodid']);

            if ($product->inventories->stock_qty > 0) {
                $cart = new Cart();
                if (!empty($input['cart_id'])) {
                    $cart->updateCart($input['cart_id']);

                    $cartdet = new CartDetails();
                    $existing = $cartdet->getDetails($input['prodid'], 0);
                    if ($existing == null) {
                        $data = array(
                            'product_id' => $input['prodid'],
                            'cart_id' => $input['cart_id'],
                            'domain_id' => 0,
                            'qty' => 1,
                            'price' => $product->regular_price,
                            'discount' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        );

                        $cartdet->addDetails($data);
                    } else {
                        $data = array(
                            'product_id' => $input['prodid'],
                            'cart_id' => $input['cart_id'],
                            'domain_id' => 0,
                            'qty' => $existing->qty + 1,
                            //'price' => $product->regular_price,
                            //'discount' => 0,
                            'updated_at' => date('Y-m-d H:i:s')
                        );

                        $cartdet->updateDetails($existing->id, $data);

                    }
                    session(['cart_id'=>$input['cart_id']]);
                    echo json_encode(array('cart_id' => $input['cart_id']));
                } else {
                    if ($user != null)
                        $data = array('user_id' => $user->id, 'created_at' => date('Y-m-d H:i:s'));
                    else
                        $data = array('user_id' => 0, 'created_at' => date('Y-m-d H:i:s'));

                    $cartsession = $cart->setCart($data);


                    $cartdet = new CartDetails();
                    //$existing = $cartdet->getDetails($input['prodid'],0);
                    $data = array(
                        'product_id' => $input['prodid'],
                        'cart_id' => $cartsession,
                        'domain_id' => 0,
                        'qty' => 1,
                        'price' => $product->regular_price,
                        'discount' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    );

                    $cartdet->addDetails($data);
                    session(['cart_id'=>$cartsession]);
                    echo json_encode(array('cart_id' => $cartsession));
                }

            }
        }
    }
}
