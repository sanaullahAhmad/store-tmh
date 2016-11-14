<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Redirect; 

use Illuminate\Http\Request; 
use Session;
use App\User;
use Validator; 
use Auth; 

class AuthController extends BaseController
{  

    protected $username = "username";



    public function __construct()
    {
        parent::__construct(); 
        $this->middleware($this->guestMiddleware(), ['except' => 'getLogout']);
    }
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = 'mijn-account';

     
 
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        isset($data['username'])?$name = $data['username']:$name = $data['email'];  
        return User::create([
            'username' => $name,
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }


    public function loginPath()
    {
        return '/mijn-account';
    }
  

    public function getLogout()
    { 
        Session::flush();
        Auth::guard($this->getGuard())->logout(); 

        return redirect('/');
    }

    public function getLogin()
    {
        $menu           = $this->menu;
        $footerMenu     = $this->footerMenu; 
        $device         = $this->device;
        $deviceName     = $this->deviceName;


         return view('auth.login'  ,[ 'menu'=> $menu, 'footerMenu' => $footerMenu, 'device' => $device, 'deviceName' => $deviceName ]);

    }




    public function postLogin(Request $request)
    {
        
         
        $login = $request->input('username');
         
        $login_type = filter_var( $login, FILTER_VALIDATE_EMAIL ) ? 'email' : 'username';
         
        $request->merge([ $login_type => $login ]);
         
        if ( $login_type == 'email' ) {
            $this->validate($request, [
                'email'    => 'required|email',
                'password' => 'required',
            ]);
            $credentials = $request->only( 'email', 'password' );
        } else {
            $this->validate($request, [
                'username' => 'required',
                'password' => 'required',
            ]);
            $credentials = $request->only( 'username', 'password' );
        }

        if (Auth::attempt($credentials, $request->has('remember')))
        {

             $user = Auth::getLastAttempted();
               
                if ($user->status == 'active') {
                    Auth::login($user, $request->has('remember'));
                    return redirect()->intended($this->redirectPath())->with(['toastr-msg-success' => 'Login successful!']);  
                } else {
                     //dd($user); 
                     Auth::logout();
                    return redirect($this->loginPath()) // Change this to redirect elsewhere
                        ->withInput($request->only('email', 'remember'))
                        ->withErrors([
                            'username' => 'Your acocunt is inactive or deleted!'
                        ]);
                }

            //return redirect()->intended($this->redirectPath());
        }

        return redirect($this->loginPath())
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }

 
  protected function validator(array $data)
    {
        return Validator::make($data, [ 
            'email' => 'required|email|max:255|unique:customers',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6'
        ]);
    }

    public function postRegister(Request $request)
    {
 

        $validator = $this->validator($request->all());

        

        if (!$validator->fails()) {
             $user = $this->create($request->only( 'email', 'password' )); 
             Auth::attempt($request->only( 'email', 'password' ));
             return redirect($this->loginPath())->with(['toastr-msg-success' => 'Registeration successful!']);  

             //$this->postLogin($request) ; 
        }else{
           return redirect($this->loginPath()) 
           ->withInput($request->all())
            ->withErrors($validator, 'register');
        }
 
    }
 
}
