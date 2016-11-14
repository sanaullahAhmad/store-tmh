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



class CustomersController extends BaseController
{

    private $customers; 
    private $user; 
    public function __construct()
    {
        parent::__construct();
        $this->customers    = new Customers();
        $this->user         = Auth::user();  
        $this->countries      = new Countries();
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
            return view('account.mijnaccount'  ,[ 'countries' => $countries, 'wishlist' => $wishlist, 'user' => $user, 'menu'=> $menu, 'footerMenu' => $footerMenu, 'device' => $device, 'deviceName' => $deviceName ]);
        }else
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


    public function updateBillingAddress(Request $request)
    {

        $input = $request->all(); 

        $errors = array();  // array to hold validation errors
        $data = array();        // array to pass back data

     
       if (empty($input['b_fname']))
            $errors['b_fname'] = 'First Name is required' ;

        if (empty($input['b_lname']))
            $errors['b_lname'] = 'Last Name is required' ;

        if (empty($input['b_address_1']))
            $errors['b_address_1'] = 'Address is requireds' ;

        if (empty($input['b_country']))
            $errors['b_country'] = 'Country is required' ;

        if (empty($input['b_city']))
            $errors['b_city'] = 'City is required' ;

        if (empty($input['b_email']))
            $errors['b_email'] = 'Email is required' ;

        if (!filter_var($input['b_email'], FILTER_VALIDATE_EMAIL)) {
             $errors['b_email'] = "Invalid email format"; 
            }

        if (empty($input['b_phone']))
            $errors['b_phone'] = 'Phone number is required' ;

        if ( ! empty($errors)) {

          // if there are items in our errors array, return those errors
          $data['success'] = false;
          $data['errors']  = $errors;
        } else {
            $this->customers->UpdateCustomerBilling($input, $input['customer_id']);
            $data['success'] = true;
            $data['message'] = 'Billing address updated!';
        }

        echo json_encode($data);

    }


    public function updateShippingAddress(Request $request)
    {

        $input = $request->all(); 

        $errors = array();  // array to hold validation errors
        $data = array();        // array to pass back data

     
       if (empty($input['s_fname']))
            $errors['s_fname'] = 'First Name is required' ;

        if (empty($input['s_lname']))
            $errors['s_lname'] = 'Last Name is required' ;

        if (empty($input['s_address_1']))
            $errors['s_address_1'] = 'Address is requireds' ;

        if (empty($input['s_country']))
            $errors['s_country'] = 'Country is required' ;

        if (empty($input['s_city']))
            $errors['s_city'] = 'City is required' ;

        if (empty($input['s_email']))
            $errors['s_email'] = 'Email is required' ;

        if (!filter_var($input['s_email'], FILTER_VALIDATE_EMAIL)) {
             $errors['s_email'] = "Invalid email format"; 
            }

        if (empty($input['s_phone']))
            $errors['s_phone'] = 'Phone number is required' ;

        if ( ! empty($errors)) {

          // if there are items in our errors array, return those errors
          $data['success'] = false;
          $data['errors']  = $errors;
        } else {
            $this->customers->UpdateCustomerShpping($input, $input['customer_id']);
            $data['success'] = true;
            $data['message_shipping'] = 'Shipping address updated!';
        }

        echo json_encode($data);

    }

    public function resetMyPassword(Request $request)
    {
         $input = $request->all(); 

        $errors = array();  // array to hold validation errors
        $data = array();        // array to pass back data

     
       if (empty($input['current_pass']))
            $errors['er_current_pass'] = 'Current password is required' ;

        if (empty($input['new_pass']))
            $errors['er_new_pass'] = 'New password is required' ;

        if (empty($input['new_pass_confirmation']))
            $errors['er_new_pass_confirmation'] = 'Password confirmation is required' ;

        if($input['new_pass_confirmation'] !== $input['new_pass'])
            $errors['er_new_pass_confirmation'] = 'Password confirmation is incorrect' ;            

        if ( !empty($errors)) {

          // if there are items in our errors array, return those errors
          $data['success'] = false;
          $data['errors']  = $errors;
        } else {

            
            $user = Customers::where('id' ,  $input['customer_id'])->first();  

            if (Hash::check($input['current_pass'], $user->password))
            {
                
                $user->password = bcrypt($input['new_pass']);
                $user->save();     

                //$this->customers->UpdateCustomerPassword($input, $input['customer_id']);
                $data['success'] = true;
                $data['message_pass'] = 'Password changed!';      
            }else{ 
                    $errors['er_current_pass'] = 'Current password is incorrect' ;  
                    $data['success'] = false;
                    $data['errors']  = $errors;
            }

            
        }

        echo json_encode($data);


    }


}
