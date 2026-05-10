<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Redirect to Google for authentication
    public function redirect() {
        return Socialite::driver('google')->redirect();
    }

    // Handle the callback from Google
    public function callback() {
        $googleUser = Socialite::driver('google')->stateless()->user();
        
        $user = User::updateOrCreate([
            'google_id' => $googleUser->id,
        ], [
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'avatar' => $googleUser->avatar,
        ]);

        Auth::login($user);
        return redirect()->route('dashboard');
    }
}