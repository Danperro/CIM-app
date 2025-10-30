<?php

namespace App\Livewire\Login;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\usuario;
use Livewire\Component;

class Login extends Component
{
    public $username = '';
    public $password = '';
    public $error = null;

    public function rules(): array
    {
        return [
            'username' => ['required'],
            'password' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Ingresa tu usuario.',
            'password.required' => 'Ingresa tu contraseña.',
        ];
    }

    public function login()
    {
        $this->validate();

        $user = usuario::where('UsernameUsa', $this->username)
            ->where('EstadoUsa', 1)
            ->first();

        if (!$user || !Hash::check($this->password, $user->PasswordUsa)) {
            $this->error = "Usuario o contraseña incorrectos o cuenta inactiva.";
            return;
        }

        // Asegúrate de que tu modelo `usuario` implemente Authenticatable y getAuthPassword()
        Auth::login($user);
        session()->regenerate();

        // Redirección directa desde el componente (mejor que flags)
        return redirect()->intended('/Control'); // cambia por tu ruta/dash
    }

    public function render()
    {
        return view('livewire.login.login');
    }
}
