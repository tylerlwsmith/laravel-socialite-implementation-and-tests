<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as OAuth2User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/google/callback', function () {
    try {
        /** @var OAuth2User $google_user */
        $google_user = Socialite::driver('google')->user();
    } catch (InvalidStateException $exception) {
        abort(400, $exception->getMessage());
    }

    $user = User::updateOrCreate([
        'email' => $google_user->email,
    ], [
        'google_id' => $google_user->id,
        'name' => $google_user->name,
        'google_token' => $google_user->token,
        'google_refresh_token' => $google_user->refreshToken,
    ]);

    Auth::login($user);
    return redirect('/');
});

Route::get('/auth/logout', function () {
    Auth::logout();
    return redirect('/');
});
