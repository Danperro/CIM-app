<?php

namespace App\Livewire\Perfil;

use Livewire\Component;
use App\Models\usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AllPerfil extends Component
{

    public $idUsa, $idPer;

    // Datos de usuario/persona
    public $usernameUsa, $nombrePer, $apellidoPaternoPer, $apellidoMaternoPer;
    public $correoPer, $dniPer, $telefonoPer, $fechaNacimientoPer;

    // Lectura sola
    public $rolNombre = '';
    public $laboratorios = []; // nombres de labs del usuario

    // Cambio de contraseña
    public $passwordActual, $passwordNueva, $passwordConfirmacion;

    protected $messages = [
        'usernameUsa.required' => 'El username es obligatorio.',
        'usernameUsa.unique'   => 'Este username ya está en uso.',
        'nombrePer.required'   => 'El nombre es obligatorio.',
        'nombrePer.regex'      => 'Solo letras y espacios.',
        'apellidoPaternoPer.required' => 'El apellido paterno es obligatorio.',
        'apellidoPaternoPer.regex'    => 'Solo letras y espacios.',
        'apellidoMaternoPer.required' => 'El apellido materno es obligatorio.',
        'apellidoMaternoPer.regex'    => 'Solo letras y espacios.',
        'correoPer.required'   => 'El correo es obligatorio.',
        'correoPer.email'      => 'Formato de correo inválido.',
        'correoPer.unique'     => 'Este correo ya está registrado.',
        'dniPer.required'      => 'El DNI es obligatorio.',
        'dniPer.regex'         => 'El DNI debe tener 8 dígitos.',
        'dniPer.unique'        => 'Este DNI ya está registrado.',
        'telefonoPer.required' => 'El teléfono es obligatorio.',
        'telefonoPer.regex'    => 'Debe iniciar con 9 y tener 9 dígitos.',
        'fechaNacimientoPer.required' => 'La fecha de nacimiento es obligatoria.',
        'fechaNacimientoPer.before'   => 'Debes tener al menos 18 años.',

        'passwordActual.required'      => 'Ingresa tu contraseña actual.',
        'passwordNueva.required'       => 'Ingresa la nueva contraseña.',
        'passwordNueva.min'            => 'Mínimo 6 caracteres.',
        'passwordConfirmacion.same'    => 'Las contraseñas no coinciden.',
    ];

    public function mount()
    {
        $user = Auth::user()
            ->load(['persona', 'rol', 'detalleusuario.laboratorio']);

        $this->idUsa  = $user->IdUsa;
        $this->idPer  = $user->persona->IdPer;

        $this->usernameUsa         = $user->UsernameUsa;
        $this->nombrePer           = $user->persona->NombrePer;
        $this->apellidoPaternoPer  = $user->persona->ApellidoPaternoPer;
        $this->apellidoMaternoPer  = $user->persona->ApellidoMaternoPer;
        $this->correoPer           = $user->persona->CorreoPer;
        $this->dniPer              = $user->persona->DniPer;
        $this->telefonoPer         = $user->persona->TelefonoPer;
        $this->fechaNacimientoPer  = $user->persona->FechaNacimientoPer;

        $this->rolNombre = $user->rol?->NombreRol ?? '—';
        $this->laboratorios = $user->detalleusuario
            ->map(fn($d) => $d->laboratorio?->NombreLab)
            ->filter()
            ->values()
            ->toArray();
    }

    public function rules()
    {
        return [
            'usernameUsa' => [
                'required',
                'string',
                Rule::unique('usuario', 'UsernameUsa')->ignore($this->idUsa, 'IdUsa'),
            ],
            'nombrePer' => ['required', 'regex:/^[\pL\s\-]+$/u'],
            'apellidoPaternoPer' => ['required', 'regex:/^[\pL\s\-]+$/u'],
            'apellidoMaternoPer' => ['required', 'regex:/^[\pL\s\-]+$/u'],
            'correoPer' => [
                'required',
                'email',
                Rule::unique('persona', 'CorreoPer')->ignore($this->idPer, 'IdPer'),
            ],
            'dniPer' => [
                'required',
                'regex:/^\d{8}$/',
                Rule::unique('persona', 'DniPer')->ignore($this->idPer, 'IdPer'),
            ],
            'telefonoPer' => ['required', 'regex:/^9\d{8}$/'],
            'fechaNacimientoPer' => ['required', 'date', 'before:' . now()->subYears(18)->toDateString()],
        ];
    }

    public function updated($prop)
    {
        if (in_array($prop, ['usernameUsa', 'nombrePer', 'apellidoPaternoPer', 'apellidoMaternoPer'])) {
            $this->$prop = trim((string)$this->$prop);
        }
        if ($prop === 'correoPer') {
            $this->correoPer = strtolower(trim((string)$this->correoPer));
        }
        $this->validateOnly($prop, $this->rules(), $this->messages);
    }

    public function guardarPerfil()
    {
        // normalizaciones suaves
        $this->dniPer = preg_replace('/\D+/', '', (string)$this->dniPer);
        $this->telefonoPer = preg_replace('/\D+/', '', (string)$this->telefonoPer);

        $this->validate($this->rules(), $this->messages);

        try {
            $user = usuario::with('persona')->findOrFail($this->idUsa);

            // persona
            $user->persona->update([
                'NombrePer'          => $this->nombrePer,
                'ApellidoPaternoPer' => $this->apellidoPaternoPer,
                'ApellidoMaternoPer' => $this->apellidoMaternoPer,
                'CorreoPer'          => $this->correoPer,
                'DniPer'             => $this->dniPer,
                'TelefonoPer'        => $this->telefonoPer,
                'FechaNacimientoPer' => $this->fechaNacimientoPer,
            ]);

            // usuario
            $user->UsernameUsa = $this->usernameUsa;
            $user->save();

            $this->dispatch('toast-success', [
                'title' => 'Perfil actualizado',
                'message' => 'Se guardaron tus cambios correctamente.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar perfil', ['msg' => $e->getMessage()]);
            $this->dispatch('toast-danger', message: 'No se pudo guardar el perfil.');
        }
    }

    public function cambiarPassword()
    {
        $this->validate([
            'passwordActual'      => ['required'],
            'passwordNueva'       => ['required', 'string', 'min:6'],
            'passwordConfirmacion' => ['same:passwordNueva'],
        ], $this->messages);

        $user = usuario::findOrFail($this->idUsa);

        if (!Hash::check((string)$this->passwordActual, $user->PasswordUsa)) {
            $this->addError('passwordActual', 'La contraseña actual es incorrecta.');
            return;
        }

        try {
            $user->PasswordUsa = Hash::make((string)$this->passwordNueva);
            $user->save();

            $this->reset(['passwordActual', 'passwordNueva', 'passwordConfirmacion']);
            $this->dispatch('toast-success', [
                'title' => 'Contraseña actualizada',
                'message' => 'Tu contraseña fue cambiada correctamente.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al cambiar contraseña', ['msg' => $e->getMessage()]);
            $this->dispatch('toast-danger', message: 'No se pudo cambiar la contraseña.');
        }
    }

    public function render()
    {
        return view('livewire.perfil.all-perfil');
    }
}
