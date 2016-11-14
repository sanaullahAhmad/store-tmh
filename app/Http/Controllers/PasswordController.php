<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Mail;
use App\User;
use App\PasswordReset;
use Illuminate\Http\Request; 
use Config; 

class PasswordController extends BaseController
{

       
    public function __construct()
    {
        parent::__construct(); 
        $this->middleware('guest'); 
    }


    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;


    public function getEmail($token = null)
    {
        $menu           = $this->menu;
        $footerMenu     = $this->footerMenu; 
        $device         = $this->device;
        $deviceName     = $this->deviceName;   

         return view('auth.passwords.email'  ,[ 'token' => $token, 'menu'=> $menu, 'footerMenu' => $footerMenu, 'device' => $device, 'deviceName' => $deviceName ]);
    }


    public function getReset($email = null, $token = null)
    {
        $menu           = $this->menu;
        $footerMenu     = $this->footerMenu; 
        $device         = $this->device;
        $deviceName     = $this->deviceName; 

         return view('auth.passwords.reset'  ,[ 'email' => $email, 'token' => $token, 'menu'=> $menu, 'footerMenu' => $footerMenu, 'device' => $device, 'deviceName' => $deviceName ]);
    }





    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:customers,email',
        ]);

        //invalidate old tokens
        PasswordReset::whereEmail($request->email)->delete();

        $email = $request->email;
        $reset = PasswordReset::create([
            'email' => $email,
            'token' => str_random(10),
        ]);

        $token = $reset->token;
        
        Mail::send('auth.emails.password', compact('email', 'token'), function ($mail) use ($email) {
            $mail->to($email)
            ->from('noreply@fashionhomerun.nl')
            ->subject('Password reset link');
        });

         

        return redirect('/passwordreset') 
           ->with(['success' => 'Password reset email sent! please check your inbox.']);
    }

    public function verify(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'token' => 'required',
        ]);

        $check = PasswordReset::whereEmail($request->email)
        ->whereToken($request->token)
        ->first();

        if (! $check) {
            return response()->error('Email does not exist', 422);
        }

        return response()->success(true);
    }

    public function reset(Request $request)
    {
        $this->validate($request, [ 
            'token'    => "required|exists:password_resets,token,email,{$request->email}",
            'password'    => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = User::whereEmail($request->email)->firstOrFail();
        $user->password = bcrypt($request->password);
        $user->save();

        //delete pending resets
        PasswordReset::whereEmail($request->email)->delete();

       return redirect('/mijn-account')
           ->with(['success' => 'Password reset successful']);
    }

    public function resetMyPassword(Request $request)
    {
        //dd($request);
        $this->validate($request, [ 
            'current_pass'    => "required|exists:customers,password,id,{$request->customer_id}",
            'new_pass'    => 'required|min:6|confirmed',
            'new_pass_confirmation' => 'required',
        ]);

        $user = User::where('password', $request->current_pass)->firstOrFail();
        $user->password = bcrypt($request->password);
        $user->save(); 

       return redirect('/mijn-account')
           ->with(['success' => 'Password reset successful']);
    }
 
}
