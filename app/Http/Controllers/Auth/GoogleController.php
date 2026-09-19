<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->id)
                    ->orWhere('email', $googleUser->email)
                    ->first();

        if ($user) {
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->id]);
            }
            Auth::login($user);
        } else {
            $user = User::create([
                'name'      => $googleUser->name,
                'email'     => $googleUser->email,
                'google_id' => $googleUser->id,
                'password'  => Hash::make(Str::random(16)),
            ]);

            Auth::login($user);
        }

        return redirect()->intended('/explore');

    } catch (\Exception $e) {
        return redirect()->route('login')->with('status', 'Gagal login menggunakan Google.');
    }
    }
}
