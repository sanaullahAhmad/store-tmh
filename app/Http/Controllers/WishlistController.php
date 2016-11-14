<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Customers; 
use App\Countries;
use App\CustomerWishlist;
use App\Functions\Functions;
use Auth; 
use Hash; 



class WishlistController extends BaseController
{
    private $customers; 
    private $user; 
    public function __construct()
    {
        parent::__construct();
        $this->customers    = new Customers();
        $this->user         = Auth::user();
        $this->countries      = new Countries();
        $this->wishlist      = new CustomerWishlist();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $menu           = $this->menu;
        $footerMenu     = $this->footerMenu;
        $device         = $this->device;
        $deviceName     = $this->deviceName;
        if($this->user)
        {
            $user           = $this->customers->getCustomer($this->user->id);
            $countries      = $this->countries->getCountries();
            //$wishlist       = \DB::table('customer_wishlist')->where('customer_id', $user->id)->get();//$this->wishlist->getWishes();
            $wishlist = \DB::table('customer_wishlist')
                ->leftJoin('products', 'customer_wishlist.product_id', '=', 'products.id')
                ->leftJoin('media', 'media.id', '=', 'products.featured_image_id')
                ->where('customer_wishlist.customer_id', $user->id)
                ->select('products.*', 'media.path')
                ->get();


            foreach($wishlist as $key=>$wish)
            {
                $wishlist[$key]->path = Functions::productThumbnail($wish->path,'55x85');//Functions::productImage($wish->path);
                $price = Functions::getPrice($wish);
                $wishlist[$key]->price=$price;
            }
            //echo "<pre>";print_r($wishlist);exit;

            return view('account.miwishlist'  ,[ 'countries' => $countries, 'wishlist' => $wishlist, 'user' => $user, 'menu'=> $menu, 'footerMenu' => $footerMenu, 'device' => $device, 'deviceName' => $deviceName ]);
        }
        else
        {
            return view('auth.login'  ,[ 'menu'=> $menu, 'footerMenu' => $footerMenu, 'device' => $device, 'deviceName' => $deviceName ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function addtowishlist($product_id)
    {
        $pro_id  =  $product_id;
        //Check if logged in
        if($this->user) {
            $user = $this->customers->getCustomer($this->user->id);
            $user_id = $user->id;
            //Check if already added
            $countthis = \DB::table('customer_wishlist')
                ->where([
                    ['customer_id', $user_id],
                    ['product_id', $pro_id],
                ])
                ->count();
            //if Not added, than insert
            if($countthis==0)
            {
                \DB::table('customer_wishlist')->insertGetId(
                    [
                        'customer_id' => $user_id,
                        'product_id'  => $pro_id
                    ]
                );
                $message = "Add to wish list successfully.";
            }
            else{
                $message = "You have already added this procut to wishlist.";
            }

        }
        else
        {
            //if not logged in, Alert
            $user_id = 0;
            $message = "You are not Logged in.";
        }

        //
        $result['wishes'] = array('product_id' => $pro_id, 'is_logged_in'=> $user_id, 'message'=>$message);
        echo json_encode($result);
    }
    public function removefromwishlist($pro_id)
    {
        //Check if logged in
        if($this->user) {
            $user = $this->customers->getCustomer($this->user->id);
            $user_id = $user->id;
            \DB::table('customer_wishlist')
                ->where([
                    ['customer_id', $user_id],
                    ['product_id', $pro_id],
                ])
                ->delete();
            $message = "Product removed from wishlist.";
        }
        else
        {
            //if not logged in, Alert
            $user_id = 0;
            $message = "You are not logged in.";
        }

        //
        $result['wishes'] = array('product_id' => $pro_id, 'is_logged_in'=> $user_id, 'message'=> $message);
        echo json_encode($result);
    }

}
