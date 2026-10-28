<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                Password::defaults(),
                'confirmed',
                function (string $attribute, mixed $value, \Closure $fail) use ($user) {
                    if ($user && filled($user->identifier) && (string) $value === (string) $user->identifier) {
                        $fail('Password baru tidak boleh sama dengan NPM/NIDN Anda.');
                    }
                },
            ],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $request->session()->forget('using_default_password');

        return back()->with('status', 'password-updated');
    }
}
