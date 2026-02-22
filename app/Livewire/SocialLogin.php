<?php

namespace App\Livewire;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Livewire\Component;

class SocialLogin extends Component
{


    public function socialRedirect($provider)
    {
 
        return Socialite::driver($provider)->redirect();
    }

    public function socialCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            if ($socialUser) {
                $user = User::where('email', $socialUser->email)->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $socialUser->name,
                        'email' => $socialUser->email,
                        'password' => Hash::make(Str::random(8)),
                        'google_id' => $socialUser->id, // Ensure migration exists
                        'avatar' => $socialUser->avatar,
                    ]);
                } else {
                    // Update Google info for existing users
                    $user->update([
                        'google_id' => $socialUser->id,
                        'avatar' => $socialUser->avatar,
                    ]);
                }

                Auth::login($user, true);

                return redirect()->intended(route('dashboard'));
            }
        } catch (\Exception $e) {
            // Log error or handle it
            return redirect()->route('login')->with('error', 'Login failed: ' . $e->getMessage());
        }
    }


    
}
