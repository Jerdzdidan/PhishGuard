<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Existing user - link Google ID if not yet linked
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }

                // Check if user is active
                if (!$user->status) {
                    return redirect()->route('auth.sign-in')->with('error', 'Your account is inactive.');
                }
            } else {
                // New user - create account with Google
                $nameParts = explode(' ', $googleUser->getName(), 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? '';

                $user = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'user_type' => 'USER',
                    'password' => Hash::make(Str::random(16)), 
                    'email_verified_at' => now(),
                ]);
            }
            
            Auth::login($user);

            // Redirect based on user type
            if ($user->user_type === 'USER') {
                return redirect()->route('user.home');
            } else {
                return redirect()->route('admin.home');
            }
            
        } catch (Exception $e) {
            return redirect()->route('auth.sign-in')->with('error', 'Failed to login with Google. Please try again.');
        }
    }
}
