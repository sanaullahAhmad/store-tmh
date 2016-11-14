<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Attributes;
use App\Cart;
use App\Terms;
use App\CartDetails;
use App\Functions\Functions;
use App\Products;

class CartController extends BaseController
{
    public function index(Request $request){

        $menu           = $this->menu;
        $device         = $this->device;
        $deviceName     = $this->deviceName;
        $footerMenu     = $this->footerMenu;

        $input = $request->all();


       /* $cart = new Cart();
        $cart_data = $cart->getCart($input['cid']);*/
        $message = null;
        $cart_details = new CartDetails();
        if(session('cart_id') !=='') {
            if(isset($input['remove_item'])){
                $prod_id = base_convert($input['remove_item'] , 2, 10);

                if($cart_details->deleteDetails($prod_id,session('cart_id'))){
                    $message = "product removed successfully...";
                }
            }

            $cart_items = $cart_details->getItems(session('cart_id'));
            $data_items = null;
            $prod = new Products();
            $objTerm = new Terms();

            foreach ($cart_items as $item) {

                $details = $prod->getProduct($item->product_id);

                if($details->product_type =='variation') {
                    $var_data = $prod->getVariationTitle($item->product_id);
                    foreach (unserialize($var_data['Attributes']['attributes']['attributes']) as $key => $value) {
                        $var_title = '<dl class="variation"><dt class="variation">'.$objTerm->getAttName($key).' :</dt>';
                        $var_title .= '<dd class="vairation">'.$value.'</dd></dl>';
                    }

                    $title = $var_data['parent']['name'];
                    $title .= '<br/><small>'.$var_title.'</small>';
                }
                else
                    $title = $details->name;

                $data_items[] = array(
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'id' => $item->product_id,
                    'product' => $title
                );
            }
        }
        else{
            $items = null;
            return redirect()->route('home');
        }
        $crosssells = null;

        $shipping = 2.95;

        return view('cart/cart'  ,[ 'footerMenu' => $footerMenu, 'menu'=> $menu, 'device' => $device, 'deviceName' => $deviceName,'items' => $data_items, 'crosssells'=>$crosssells , 'shipping'=>$shipping,'message'=>$message]);

    }

    public function cart_count(Request $request){
        $input = $request->all();
        if($input['cart_id'] !='') {
            session(['cart_id'=>$input['cart_id']]);
            echo json_encode(array('cartcount'=>CartDetails::getCount($input['cart_id'])));
        }
        else json_encode(array('cartcount'=>0));
    }
}
