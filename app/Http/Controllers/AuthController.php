<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

class AuthController extends Controller
{
    public function redirect()
    {
        /** @var GoogleProvider $provider */
        $provider = Socialite::driver('google');

        return $provider
            ->redirectUrl(route('auth.google.callback'))
            ->redirect();
    }

    public function callback()
    {
        /** @var GoogleProvider $provider */
        $provider = Socialite::driver('google');
        $googleUser = $provider
            ->redirectUrl(route('auth.google.callback'))
            ->stateless()
            ->user();

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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
