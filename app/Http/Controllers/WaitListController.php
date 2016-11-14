<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Customers;
use App\Metas;
use App\Functions\Functions;
use Mail;
use Config;


use App\Http\Requests;

class WaitListController extends BaseController
{
    protected $customers;
    protected $meta;

    public function __construct()
    {
        parent::__construct();

        $this->customers    = new Customers();
        $this->meta         = new Metas();
    }


    //public function addToWaitList(Request $request)
    /**
     * @param $product_id
     * @param $email
     * @param $type
     * @return string
     */
    public function addToWaitList($product_id, $email)
    {
        //$input = $request->all();
        //if(isset($input['product_id']) && $input['email'] && $input['type'])
        if($product_id && $email)
        {
            $new_wait_list = array();
            //$product_id = $request->request('product_id') ;

            //$email = $request->request('email');

            if($this->customers->checkIfExist('email', $email) !== false) // check if user already exist with this email
            {
                $user_id = $this->customers->getCustomerId('email', $email);

            }else{  // if user does not exist with this email, create user and add to wait list.

                $user_em    = explode('@', $email);
                $username   = $user_em[0];
                $password   = Functions::getRandomString(8);
                $user_data = array(
                    'email'     => $email,
                    'username'  => $username,
                    'password'  => bcrypt($password)
                );
                $user_id = $this->customers->add($user_data);

                if($user_id)
                {

                    $data = [];
                    $data['site_url'] = Config::get('app.url');
                    $data['user_name'] = $username;
                    $data['user_email'] = $email;
                    $data['user_password'] = $password;


                    $mailSent = Mail::send('waitlist', array('data' => $data), function ($mail) use ($email) {
                        $mail->to('lucky.uae1989@gmail.com')
                            ->from('klantenservice@themusthaves.nl', 'themusthaves.nl')
                            ->subject('Jouw favoriete Musthave is weer op voorraad bij ');
                    });


                }

            }


            if($user_id !==false)
            {
                // fetch current wait list
                $db_wait_list = $this->getWaitList($product_id);

                if($db_wait_list)
                {

                    $db_wait_list = unserialize($db_wait_list->meta_value);
                    $new_wait_list = $db_wait_list;
                    if(!in_array($user_id, $db_wait_list))
                    {

                        $new_wait_list[] = $user_id;
                        $new_wait_list = serialize($new_wait_list);
                        // save wait list meta
                        $update_meta = $this->meta->updateProductMeta('woocommerce_waitlist', $new_wait_list, $product_id);

                        if($update_meta)
                        {
                            return json_encode(array('action' => 'true', 'user_id' => $user_id));
                            exit;
                        }
                    }else{
                        return json_encode(array('action' => 'false', 'msg' => 'Email already exist in wait list.'));
                        exit;
                    }

                }else{

                    $new_wait_list = serialize(array('0' => $user_id));
                    // save wait list meta
                    $update_meta = $this->meta->saveProductMeta('woocommerce_waitlist', $new_wait_list, $product_id);

                    if($update_meta)
                    {
                        return json_encode(array('action' => 'true', 'user_id' => $user_id));
                        exit;
                    }

                }


            }

            return json_encode(array('action' => 'false'));
            exit;
        }
    }


    // fetch wait list

    public function getWaitList($product_id)
    {
        return  $this->meta->getProductMeta('woocommerce_waitlist', $product_id);
    }

}
