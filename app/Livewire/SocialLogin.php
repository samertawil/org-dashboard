<?php

namespace App\Livewire;


use App\Enums\GlobalSystemConstant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
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

               
                if ($user && $user->activation !=1) {
                  
                    throw ValidationException::withMessages([
                        Fortify::username() => ['Your account has been suspended. Please contact the administrator.'],
                    ]);
                }

                if (!$user) {
                    $user = User::create([
                        'name' => $socialUser->name,
                        'email' => $socialUser->email,
                        'password' => Hash::make(Str::random(8)),
                        'google_id' => $socialUser->id, // Ensure migration exists
                        'avatar' => $socialUser->avatar,
                        'activation' => GlobalSystemConstant::INACTIVE->value, // Set default activation status
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
        } catch (ValidationException $e) {
            return redirect()->route('login')->withErrors($e->errors());
        } catch (\Exception $e) {
            // Log error or handle it
            return redirect()->route('login')->withErrors(['email' => __('Login failed: ') . $e->getMessage()]);
        }
    }
}
