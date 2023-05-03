<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
    * Login Using Facebook
    */
    public function loginUsingFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }
    
    /**
    * Facebook Callback
    */
    public function callbackFromFacebook()
    {
        try {
            $user = Socialite::driver('facebook')->stateless()->user();
        
            $saveUser = User::updateOrCreate([
                'provider' => 'facebook',
                'provider_id' => $user->getId(),
            ],[
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => Hash::make($user->getName().'@'.$user->getId())
            ]);
        
            Auth::loginUsingId($saveUser->id);
        
            return redirect()->route('home');

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}