<?php

namespace App\Http\Controllers;

use App\Models\User;

class SignatureController extends Controller
{
    /**
     * Verify a digital signature by token.
     */
    public function verify($token)
    {
        $user = User::where('signature_token', $token)->first();

        return view('signature.verify', [
            'status' => $user ? 'valid' : 'invalid',
            'user' => $user,
            'message' => $user 
                ? 'Tanda tangan digital ini valid dan terdaftar di sistem SIBIMA.'
                : 'Token tanda tangan tidak valid atau tidak ditemukan.'
        ]);
    }
}
