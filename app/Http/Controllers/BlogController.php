<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Mail;
use Config;
use App\PostCategories;
use App\Posts;
use App\Pages;
use App\Functions\Functions;
use App\Functions\PHPMailer;
use Cache;
use Session;
use ReCaptcha\ReCaptcha;

class BlogController extends BaseController
{
    protected $postcategory;
    protected $post;
    protected $page;

    public function __construct()
    {
        parent::__construct();
        $this->postcategory = new PostCategories();
        $this->post = new Posts();
        $this->page = new Pages();
    }

    //
    public function index(Request $request, $slug )
    {

        $menu = $this->menu;
        $footerMenu = $this->footerMenu;
        $device = $this->device;
        $deviceName = $this->deviceName;

        $category = str_replace("-", " ", $slug);
        $category = preg_replace("/[^A-Za-z0-9?!]/", " ", $slug);
        $categoryPosts = $this->postcategory->getPostsByCategory($slug);
        $postPaginated = Functions::arrayToPaginate($request ,$categoryPosts['posts']);
       // dump($postPaginated);

        return view('post_category'  ,[  'category' => $categoryPosts, 'posts' => $postPaginated, 'slug' => $slug, 'menu'=> $menu, 'footerMenu' => $footerMenu, 'device' => $device, 'deviceName' => $deviceName ]);
    }
    public function showPost($slug)
    {

        $menu = $this->menu;
        $footerMenu = $this->footerMenu;
        $device = $this->device;
        $deviceName = $this->deviceName;
        $post_id  = $this->post->getPostIdBySlug($slug);
        $Post = $this->post->getPostById($post_id);
        $random_posts = $this->post->getRandomPosts();


        return view('posts'  ,[  'post' => $Post, 'slug' => $slug, 'randomPosts'=>$random_posts, 'menu'=> $menu, 'footerMenu' => $footerMenu, 'device' => $device, 'deviceName' => $deviceName ]);
    }
    public function showPage($slug)
    {

        $menu = $this->menu;
        $footerMenu = $this->footerMenu;
        $device = $this->device;
        $deviceName = $this->deviceName;
        $page_id  = $this->page->getPageIdBySlug($slug);

        $page = $this->page->getPageById($page_id, $device);
        return view('pages'  ,[ 'page' => $page, 'menu'=> $menu, 'footerMenu' => $footerMenu, 'device' => $device, 'deviceName' => $deviceName ]);
    }

    public function SubmitContactForm(Request $request){

        $this->validate($request, [
            'name'       => 'required',
            'order_num'       => 'required',
            'email'       => 'required|email',
            'g-recaptcha-response'       => 'required',

        ]);
        $input = $request->all();

     //  echo "<pre>";print_r($input);echo "</pre>";
        $recaptcha = $input['g-recaptcha-response'];
        if(!empty($recaptcha)) {

            $google_url = "https://www.google.com/recaptcha/api/siteverify";
            $secret = '6LeHtykTAAAAAF4Tuko8AozdldxFeXydTJ6EZI8J';
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$recaptcha);
            $responseData = json_decode($verifyResponse);

            if($responseData->success){
                //contact form submission code
                $name = $input['name'];
                $email = $input['email'];
                $order_num = $input['order_num'];
                $msg = $input['desc'];
                $subject = 'Nieuw Contactformulier van '.$name.' ';
                $sendTo_details = Config::get('mail');
                $to = $sendTo_details['contact_form'];

                Mail::send('auth.emails.contact', ['name' => $name, 'email' => $email, 'order_num' => $order_num, 'msg' => $msg],
                    function($message) use ($subject, $input,$to) {

                    // note: if you don't set this, it will use the defaults from config/mail.php
                    $message->from($input['email'] , $input['name']);
                    $message->to('maryamfjwu@gmail.com')->subject($subject);
                    $message->to('s.wessels@themusthaves.nl')->subject($subject);
                    //$message->to($to)->subject($subject);

                    foreach($input['files'] as $file) {
                        $file_path = $file->getPathName();
                        $file_originalname = $file->getClientOriginalName();
                        $message->attach($file_path, ['as' => $file_originalname]);
                    }
                });




                /* $mail = new PHPMailer();
                 //From email address and name
                 $mail->From = $email;
                 $mail->FromName =$name;

                 //To address and name
                 $mail->addAddress("maryamfjwu@gmail.com", "Maryam");

                 //Address to which recipient will reply
                 $mail->addReplyTo("maryamfjwu@gmail.com", "Maryam");

                 //CC and BCC
               //  $mail->addCC("cc@example.com");
               //  $mail->addBCC("bcc@example.com");

                 //Send HTML or Plain Text email
                 $mail->isHTML(true);

                 $mail->Subject = "Test Contact Form";
                 $mail->Body = "<i>Mail body in HTML</i>";
                 $mail->AltBody = "This is the plain text version of the email content";

                 if(!$mail->send())
                 {
                     echo "Mailer Error: " . $mail->ErrorInfo;
                 }
                 else
                 {
                     echo "Message has been sent successfully";
                     echo "email sent !";
                 }


                 $to = 'maryamfjwu@gmail.com, maryam.malik@devprovider.com';
                 $subject = 'New contact form have been submitted';
                 $htmlContent = "
                 <h1>Contact request details</h1>
                 <p><b>Name: </b>".$name."</p>
                 <p><b>Email: </b>".$email."</p>
                 <p><b>Message: </b>".$message."</p>
             ";
                 // Always set content-type when sending HTML email
                 $headers = "MIME-Version: 1.0" . "\r\n";
                 $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                 // More headers
                 $headers .= 'From:'.$name.' <'.$email.'>' . "\r\n";
                 //send email
                 @mail($to,$subject,$htmlContent,$headers);*/


            }

        }
       /*echo "<pre>"; print_r($input); echo "</pre>";exit;*/
        Session::flash('flash_message', 'Email Sent!');
        return redirect('page/contact');
        
    }

    public function getCurlData($url)
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
        $curlData = curl_exec($curl);
        curl_close($curl);
        return $curlData;
    }
}