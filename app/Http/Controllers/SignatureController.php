<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    /**
     * Verify a digital signature by token.
     * Publicly accessible for document validation.
     */
    public function verify($token)
    {
        $user = User::where('signature_token', $token)->first();

        if (!$user) {
            return view('signature.verify', [
                'status' => 'invalid',
                'message' => 'Token tanda tangan tidak valid atau tidak ditemukan.'
            ]);
        }

        return view('signature.verify', [
            'status' => 'valid',
            'user' => $user,
            'message' => 'Tanda tangan digital ini valid dan terdaftar di sistem SIBIMA.'
        ]);
    }
}
