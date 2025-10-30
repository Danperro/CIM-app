<?php

namespace App\Http\Controllers;

use App\Models\usuario;
use App\Models\persona;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Mail\ActivarUsuarioMail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'UsernameUsa' => ['required', 'string'],
            'PasswordUsa' => ['required', 'string'],
        ]);

        $credenciales = [
            'UsernameUsa' => $request->input('UsernameUsa'),
            'password'    => $request->input('PasswordUsa'),
            'EstadoUsa'   => 1 // solo activos
        ];

        if (Auth::attempt($credenciales, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/Control');
        }

        // Si el user existe pero no está activo, avisa
        $existe = usuario::where('UsernameUsa', $request->input('UsernameUsa'))->first();
        if ($existe && (int)$existe->EstadoUsa !== 1) {
            return back()->withErrors(['UsernameUsa' => 'Tu cuenta está pendiente de aprobación.'])->onlyInput('UsernameUsa');
        }

        return back()->withErrors(['UsernameUsa' => 'Credenciales incorrectas.'])
            ->onlyInput('UsernameUsa');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {

        $request->validate([
            // usuario
            'UsernameUsa' => ['required', 'string', 'max:100', 'unique:usuario,UsernameUsa'],
            'PasswordUsa' => ['required', 'string', 'min:8', 'confirmed'],

            // persona
            'NombrePer'          => ['required', 'regex:/^[\pL\s\-]+$/u'],
            'ApellidoPaternoPer' => ['required', 'regex:/^[\pL\s\-]+$/u'],
            'ApellidoMaternoPer' => ['required', 'regex:/^[\pL\s\-]+$/u'],
            'CorreoPer'          => ['required', 'email', 'unique:persona,CorreoPer'],
            'DniPer'             => ['required', 'digits:8', 'unique:persona,DniPer'],
            'TelefonoPer'        => ['required', 'regex:/^9\d{8}$/'],
            'FechaNacimientoPer' => ['required', 'date', 'before:' . now()->subYears(18)->toDateString()],
        ], [
            // mensajes cortos, adapta si quieres
            'UsernameUsa.unique' => 'El usuario ya existe.',
            'PasswordUsa.confirmed' => 'La confirmación no coincide.',
            'CorreoPer.unique' => 'Ese correo ya está registrado.',
            'DniPer.unique' => 'Ese DNI ya está registrado.',
        ]);


        // crea persona
        $per = persona::create([
            'NombrePer'          => trim($request->NombrePer),
            'ApellidoPaternoPer' => trim($request->ApellidoPaternoPer),
            'ApellidoMaternoPer' => trim($request->ApellidoMaternoPer),
            'FechaNacimientoPer' => $request->FechaNacimientoPer,
            'DniPer'             => preg_replace('/\D+/', '', $request->DniPer),
            'TelefonoPer'        => preg_replace('/\D+/', '', $request->TelefonoPer),
            'CorreoPer'          => strtolower($request->CorreoPer),
            'EstadoPer'          => 1,
        ]);

        // crea usuario pendiente de aprobación y ligado a la persona
        $user = usuario::create([
            'IdRol'       => 2, // rol por defecto
            'IdPer'       => $per->IdPer,
            'UsernameUsa' => $request->UsernameUsa,
            'PasswordUsa' => Hash::make($request->PasswordUsa),
            'EstadoUsa'   => 0, // pendiente
        ]);


        // Enlace firmado con expiración de 24h
        $url = URL::temporarySignedRoute('users.activate', now()->addHours(24), ['user' => $user->IdUsa]);

        // Enviar correo al admin con el enlace de activación
        Mail::to(config('mail.admin_address'))->send(new ActivarUsuarioMail($user, $url));

        return redirect()->route('login')->with('status', 'Registro enviado. Te avisaremos cuando activen tu cuenta.');
    }

    public function activar(Request $request, usuario $user)
    {
        if ($user->EstadoUsa === 1) {
            return redirect()->route('login')->with('status', 'El usuario ya está activo.');
        }

        $user->update(['EstadoUsa' => 1]);

        return redirect()->route('login')->with('status', 'Usuario activado. Ya puede iniciar sesión.');
    }
}
