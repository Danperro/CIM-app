<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\usuario;
use Livewire\Component;

class LoginUsuario extends Component
{
    public $username, $password, $error;
    public $redirect = false;

    public function login()
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = usuario::where('UsernameUsa', $this->username)
            ->where('EstadoUsa', 1)
            ->first();

        if (!$user || !Hash::check($this->password, $user->PasswordUsa)) {
            $this->error = "Usuario o contraseña incorrectos o cuenta inactiva.";
            return;
        }

        Auth::login($user);
        session()->regenerate();

        $this->redirect = true; // <-- activa redirección
    }

    public function render()
    {
        return view('livewire.auth.login-usuario');
    }
}
