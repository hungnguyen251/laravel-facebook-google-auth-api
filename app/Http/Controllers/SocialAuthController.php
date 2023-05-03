<?php

namespace App\Http\Controllers;

use App\Models\User;
use Mockery\Exception;
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

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    //Check Users Email If Already There
    public function checkUserExist($email)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            return true;
        }

        return false;
    }

    /**
    * Login Using Google
    */
    public function loginWithGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
    * Google Callback
    */
    public function callbackFromGoogle()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();

            // Check User Exists
            if(!$this->checkUserExist($user->getEmail())){

                $saveUser = User::updateOrCreate([
                    'provider' => 'google',
                    'provider_id' => $user->getId(),
                ],[
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'password' => Hash::make($user->getName().'@'.$user->getId())
                ]);

            }else{
                $saveUser = User::where('email',  $user->getEmail())->update([
                    'provider' => 'google',
                    'provider_id' => $user->getId(),
                ]);

                $saveUser = User::where('email', $user->getEmail())->first();
            }


            Auth::loginUsingId($saveUser->id);

            return redirect()->route('home');

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}