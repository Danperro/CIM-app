<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $credenciales = [
            'UsernameUsa' => $request->input('UsernameUsa'),
            'password' => $request->input('PasswordUsa'),
        ];

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();
            return redirect()->to('/Control'); // ESTA redirección es la segura
        }

        return back()->withErrors([
            'UsernameUsa' => 'Credenciales incorrectas.',
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
