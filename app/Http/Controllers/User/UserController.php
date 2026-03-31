<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterUserRequest;
use App\Services\SupabaseService;

class UserController extends Controller
{
    public function register(RegisterUserRequest $request, SupabaseService $supabase)
    {
        $response = $supabase->signUp(
            $request->email, 
            $request->password,
            $request->username
        );

        if (isset($response['error_description']) || isset($response['error'])) {
            return back()->withInput()->withErrors([
                'supabase' => $response['error_description'] ?? $response['error']['message']
            ]);
        }
       
        return redirect()->route('landingPage')->with('success', 'Conta criada!');
    }
}
