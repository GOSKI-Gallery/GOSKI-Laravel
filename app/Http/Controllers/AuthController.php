<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\User as User;
use App\Services\SupabaseAuthService;

class AuthController extends Controller
{
    public function login()
    {
        return view('landing');
    }

    public function authenticate(Request $request, SupabaseAuthService $supabase)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $response = $supabase->signIn(
            $request->email, 
            $request->password
        );

        if (isset($response['error_code'])) {
            $message = $response['error_code'] === 'email_not_confirmed' 
            ? 'Por favor, confirme seu e-mail antes de logar.' 
            : 'Credenciais inválidas.';

            return back()->withErrors(['email' => $message])->withInput();
        }

        $supabaseUser = $supabase->getUser($response['access_token']);

        $user = User::where('id', $supabaseUser['id'] ?? null)->first();

        if($user){
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('feed');
        }

        return back()->withErrors([
            'email' => 'Email not found.',
            'password' => 'Password is incorrect.'
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('landingPage');
    }
}


