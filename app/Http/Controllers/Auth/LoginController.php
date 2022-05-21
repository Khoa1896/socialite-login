<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Actions\Users\CreateUser;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\Users\RegisterRequest;
use App\Http\Requests\Users\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Response;
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
        $user = Socialite::driver('google')->stateless()->user();
         $this->_registerOrLoginUser($user);
       // Return home after login
     //   return redirect()->route('home');
    }
    // Facebook login
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    // Facebook callback
    public function handleFacebookCallback()
    {
        $user = Socialite::driver('facebook')->stateless()->user();
        $this->_registerOrLoginUser($user);

    }
    protected function _registerOrLoginUser($data)
    {
        $user = User::where('email', '=', $data->email)->first();
        if (!$user) {
            $user = new User();
            $user->name = $data->name;
            $user->email = $data->email;
            $user->provider_id = $data->id;
            $user->avatar = $data->avatar;
            //$user->password = Hash::make(Str::random(24));
            $user->save();
        }
      // Auth::login($user);
    }



    public function getSocialRedirect()
    {
        $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();;
        return Response::ok([
            'url' => $url
        ]);
    }
    protected function respondWithToken($token, $user)
    {
        return Response::ok([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => UserResource::make($user)
        ]);
    }
    public function socialLogin()
    {
        $socialUser = Socialite::driver('google')->stateless()->user();

        if (!$socialUser->token) {
            return Response::unauthorized('unauthorized', 'Failed!');
        }

        $user = User::whereEmail($socialUser->email)->first();

        if (!$user) {
            $user = CreateUser::invoke([
                'email' => $socialUser->email,
                'name' => $socialUser->name,
                'password' => substr(md5(time()), 0, 12),
                'email_verified_at' => Carbon::now()
            ]);
        } else {
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Verified($user));
            }
        }

        $socialAccounts = $user->socialAccounts()->firstOrCreate([
            'provider' => $provider
        ], [
            'social_id' => $socialUser->id
        ]);

        if ($socialAccounts) {
            $token = auth()->login($user);
            return $this->respondWithToken($token, $user);
        }
    }
}
