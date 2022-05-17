<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    // Google login
    public function redirectToGoogle(Request $request)
    {
        return Socialite::driver('google')->stateless()->redirect();
    }
    public function handleGoogleCallback()
    {
        $userdata = Socialite::driver('google')->stateless()->user();
        dd($userdata);
//        $user = User::updateOrCreate([
//            'id' => $userdata->id,
//        ], [
//            'name' => $userdata->name,
//            'email' => $userdata->email,
        //   'github_token' => $userdata->token,
        // 'github_refresh_token' => $userdata->refreshToken,
//        ]);

        //Auth::login($user);

      //  return redirect('home');
        // $this->_registerOrLoginUser($user);
//
//        // Return home after login
//        return redirect()->route('home');
    }
//    public function _registerOrLoginUser($data)
//    {
//        $user = User::where('email', '=', $data->email)->first();
//        if (!$user) {
//            $user = new User();
//            $user->name = $data->name;
//            $user->email = $data->email;
//            //$user->password = $data->password;
//            $user->save();
//        }
//
//        Auth::login($user);
//    }

    // Facebook login
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    // Facebook callback
    public function handleFacebookCallback()
    {
        $userdata = Socialite::driver('facebook')->user();

        // Return home after login
        return redirect()->route('home');
    }
}
