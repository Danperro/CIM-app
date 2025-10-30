<?php

namespace App\Livewire\Usuarios;

use Illuminate\Validation\Rule; // Asegúrate de importar esto arriba

use App\Models\area;
use App\Models\detalleusuario;
use App\Models\laboratorio;
use App\Models\persona;
use App\Models\rol;
use App\Models\usuario;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AllUsuarios extends Component
{
    use WithPagination;
    public $idUsa, $idPer, $idRol, $idAre, $mostrarUsuarios = [];
    public $laboratoriosPorArea = [];
    public $laboratoriosSeleccionados = [];
    public $usernameUsa, $passwordUsa, $nombrePer, $apellidoPaternoPer, $apellidoMaternoPer, $correoPer, $dniPer,
        $telefonoPer, $fechaNacimientoPer;
    #[Url('Busqueda')]
    public $query = '';
    public function limpiar()
    {
        $this->reset();
        $this->resetErrorBag();
    }
    public function selectInfo($id)
    {
        $this->idUsa = $id;
        $usuario = usuario::find($id);
        $this->usernameUsa = $usuario->UsernameUsa;
    }
    public function limpiarfiltros()
    {
        $this->reset(['query', 'idRol']);
    }
    public function rules(): array
    {
        $isUpdate = filled($this->idUsa);

        return [
            'usernameUsa' => [
                'required',
                'string',
                Rule::unique('usuario', 'UsernameUsa')->ignore($this->idUsa, 'IdUsa'),
            ],
            'passwordUsa' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:6'],

            'nombrePer' => ['required', 'regex:/^[\pL\s\-]+$/u'],
            'apellidoPaternoPer' => ['required', 'regex:/^[\pL\s\-]+$/u'],
            'apellidoMaternoPer' => ['required', 'regex:/^[\pL\s\-]+$/u'],

            // Usa IGNORE por IdPer para que no tengas que validar “si cambió”
            'correoPer' => [
                'required',
                'email',
                Rule::unique('persona', 'CorreoPer')->ignore($this->idPer, 'IdPer')
            ],
            'dniPer' => [
                'required',
                'regex:/^\d{8}$/',
                Rule::unique('persona', 'DniPer')->ignore($this->idPer, 'IdPer')
            ],
            'telefonoPer' => ['required', 'regex:/^9\d{8}$/'],

            'idRol' => ['required', 'exists:rol,IdRol'],
            'idAre' => ['required', 'exists:area,IdAre'],
            'fechaNacimientoPer' => ['required', 'date', 'before:' . now()->subYears(18)->toDateString()],

            'laboratoriosSeleccionados'   => ['required', 'array', 'min:1'],
            'laboratoriosSeleccionados.*' => [
                Rule::exists('laboratorio', 'IdLab')
                    ->where(fn($q) => $q->where('IdAre', $this->idAre)),
            ],
        ];
    }
    public function cargarLaboratorios()
    {
        if (empty($this->idAre)) {
            $this->laboratoriosPorArea = collect();
            $this->laboratoriosSeleccionados = [];
            return;
        }
        $this->laboratoriosPorArea = laboratorio::where('IdAre', $this->idAre)->get();
        $this->laboratoriosSeleccionados = []; //limpiar
    }

    protected $messages = [
        'usernameUsa.required' => 'El campo Username es obligatorio.',
        'usernameUsa.unique' => 'Este username ya está en uso.',
        'passwordUsa.required' => 'El campo Password es obligatorio.',
        'passwordUsa.min' => 'La contraseña debe tener al menos 6 caracteres.',

        'nombrePer.required' => 'El nombre es obligatorio.',
        'nombrePer.regex' => 'El nombre solo debe contener letras y espacios.',

        'apellidoPaternoPer.required' => 'El apellido paterno es obligatorio.',
        'apellidoPaternoPer.regex' => 'El apellido paterno solo debe contener letras y espacios.',

        'apellidoMaternoPer.required' => 'El apellido materno es obligatorio.',
        'apellidoMaternoPer.regex' => 'El apellido materno solo debe contener letras y espacios.',

        'correoPer.required' => 'El correo es obligatorio.',
        'correoPer.email' => 'El correo no tiene un formato válido.',
        'correoPer.unique' => 'Este correo ya está registrado.',

        'dniPer.required' => 'El DNI es obligatorio.',
        'dniPer.regex' => 'El DNI debe tener exactamente 8 dígitos.',
        'dniPer.unique' => 'Este DNI ya está registrado.',

        'telefonoPer.required' => 'El teléfono es obligatorio.',
        'telefonoPer.regex' => 'El teléfono debe comenzar con 9 y tener 9 dígitos.',

        'idRol.required' => 'Debe seleccionar un rol.',
        'idRol.exists' => 'El rol seleccionado no es válido.',
        'idAre.required' => 'Debe seleccionar un área.',
        'idAre.exists' => 'El área seleccionada no es válida.',

        'fechaNacimientoPer.required' => 'Debe ingresar la fecha de nacimiento.',
        'fechaNacimientoPer.before' => 'Debe tener al menos 18 años cumplidos.',

        'laboratoriosSeleccionados.required' => 'Debe seleccionar al menos un laboratorio.',
        'laboratoriosSeleccionados.min'      => 'Seleccione al menos un laboratorio.',
        'laboratoriosSeleccionados.*.exists' => 'Uno o más laboratorios no pertenecen al área seleccionada.',
    ];


    public function cargarDatosParaEditar($id)
    {
        $this->idUsa = $id;
        $usuario = usuario::with(['persona', 'detalleusuario.laboratorio'])->findOrFail($id);

        $this->usernameUsa = $usuario->UsernameUsa;
        $this->idRol = $usuario->IdRol;
        $this->passwordUsa = ''; // no se muestra por seguridad

        // Datos persona
        $per = $usuario->persona;
        $this->nombrePer           = $per->NombrePer;
        $this->apellidoPaternoPer  = $per->ApellidoPaternoPer;
        $this->apellidoMaternoPer  = $per->ApellidoMaternoPer;
        $this->dniPer              = $per->DniPer;
        $this->telefonoPer         = $per->TelefonoPer;
        $this->correoPer           = $per->CorreoPer;
        $this->fechaNacimientoPer  = $per->FechaNacimientoPer;

        $this->idPer = $usuario->persona->IdPer; // <- clave para unique con ignore

        // área + labs
        $primerDet = $usuario->detalleusuario()->with('laboratorio')->first();
        $this->idAre = $primerDet?->laboratorio?->IdAre;
        $this->cargarLaboratorios();
        $this->laboratoriosSeleccionados = $usuario->detalleusuario()->pluck('IdLab')->toArray() ?? [];
    }

    public function updated($prop)
    {
        if (in_array($prop, ['usernameUsa', 'nombrePer', 'apellidoPaternoPer', 'apellidoMaternoPer'])) {
            $this->$prop = trim((string)$this->$prop);
        }
        if ($prop === 'correoPer') {
            $this->correoPer = strtolower(trim((string)$this->correoPer));
        }

        // Si cambian el área, recarga la lista y limpia selección
        if ($prop === 'idAre') {
            $this->cargarLaboratorios();
        }
        if ($prop === 'passwordUsa') {
            $this->passwordUsa = trim((string) $this->passwordUsa);
            if ($this->passwordUsa === '') {
                $this->passwordUsa = null;
            }
        }
        // Validación en vivo solo del campo modificado
        $this->validateOnly($prop, $this->rules(), $this->messages);
    }

    public function actualizarUsuario()
    {
        $pwd = trim((string) $this->passwordUsa);
        $this->passwordUsa = ($pwd === '') ? null : $pwd;

        $this->validate($this->rules(), $this->messages);

        try {
            $usuario = usuario::findOrFail($this->idUsa);
            $persona = $usuario->persona;


            // Actualizar persona
            $persona->update([
                'NombrePer' => $this->nombrePer,
                'ApellidoPaternoPer' => $this->apellidoPaternoPer,
                'ApellidoMaternoPer' => $this->apellidoMaternoPer,
                'FechaNacimientoPer' => $this->fechaNacimientoPer,
                'DniPer' => $this->dniPer,
                'TelefonoPer' => $this->telefonoPer,
                'CorreoPer' => $this->correoPer
            ]);

            // Actualizar usuario
            $usuario->IdRol = $this->idRol;
            $usuario->UsernameUsa = $this->usernameUsa;
            if (!is_null($this->passwordUsa)) {
                $usuario->PasswordUsa = Hash::make($this->passwordUsa);
            }

            $usuario->save();

            // Actualizar laboratorios
            detalleusuario::where('IdUsa', $this->idUsa)->delete();
            foreach ($this->laboratoriosSeleccionados as $idLab) {
                detalleusuario::create([
                    'IdUsa' => $usuario->IdUsa,
                    'IdLab' => $idLab,
                    'EstadoDtu' => 1
                ]);
            }

            $this->dispatch('cerrar-modal', modalId: 'kt_modal_editar_usuario');
            $this->dispatch('toast-success', [
                'title' => '¡Usuario actualizado correctamente!',
                'message' => 'Los datos del usuario fueron actualizados sin problemas.'
            ]);

            $this->limpiar();
        } catch (\Throwable $e) {
            Log::error("Error al actualizar usuario", [
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ]);
            $this->addError('general', 'Ocurrió un error al actualizar el usuario.');
        }
    }

    public function toggleEstado(int $idUsa): void
    {
        // Evita que alguien se suicide bloqueándose a sí mismo
        if (Auth::user()->IdUsa === $idUsa) {
            $this->dispatch('toast-danger', message: 'No puedes cambiar tu propio estado.');
            return;
        }

        $user = usuario::findOrFail($idUsa);

        $user->EstadoUsa = $user->EstadoUsa ? 0 : 1;
        $user->save();

        $this->dispatch(
            'toast-success',
            title: 'Estado actualizado',
            message: $user->EstadoUsa ? 'Usuario activado' : 'Usuario desactivado'
        );
        // No hace falta más: Livewire volverá a ejecutar render() y refrescará la tabla
    }


    public function registrarUsuario()
    {
        $this->idUsa = null;
        $this->validate($this->rules(), $this->messages);

        // Opcional: normalizar antes de guardar
        $this->dniPer = preg_replace('/\D+/', '', (string)$this->dniPer);
        $this->telefonoPer = preg_replace('/\D+/', '', (string)$this->telefonoPer);

        try {
            DB::transaction(function () {
                $persona = persona::create([
                    'NombrePer'          => $this->nombrePer,
                    'ApellidoPaternoPer' => $this->apellidoPaternoPer,
                    'ApellidoMaternoPer' => $this->apellidoMaternoPer,
                    'FechaNacimientoPer' => $this->fechaNacimientoPer,
                    'DniPer'             => $this->dniPer,
                    'TelefonoPer'        => $this->telefonoPer,
                    'CorreoPer'          => $this->correoPer,
                    'EstadoPer'          => 1,
                ]);

                $usuario = usuario::create([
                    'IdRol'       => $this->idRol,
                    'UsernameUsa' => $this->usernameUsa,
                    'PasswordUsa' => Hash::make($this->passwordUsa), // mejor que bcrypt()
                    'IdPer'       => $persona->IdPer,
                    'EstadoUsa'   => 1,
                ]);

                foreach ($this->laboratoriosSeleccionados as $idLab) {
                    detalleusuario::create([
                        'IdUsa'     => $usuario->IdUsa,
                        'IdLab'     => $idLab,
                        'EstadoDtu' => 1,
                    ]);
                }
            });

            $this->dispatch('cerrar-modal', modalId: 'kt_modal_create_usuario');
            $this->dispatch('toast-success', [
                'title'   => '¡Usuario registrado correctamente!',
                'message' => 'El usuario ha sido creado exitosamente en el sistema.',
            ]);
            $this->limpiar();
        } catch (\Throwable $e) {
            Log::error('Error al registrar usuario', ['mensaje' => $e->getMessage(), 'linea' => $e->getLine()]);
            $this->addError('general', 'Ocurrió un error al registrar el usuario. Por favor, intente nuevamente.');
        }
    }

    public function eliminarUsuario()
    {
        try {
            if ($this->idUsa) {
                $item = usuario::where('IdUsa', $this->idUsa)->first();
                if ($item) {
                    usuario::where('IdUsa', $this->idUsa)->delete();
                    // Cerrar el modal y mostrar mensaje de éxito
                    $this->dispatch('cerrar-modal', modalId: 'kt_modal_eliminar_usuario');
                    $this->dispatch('toast-success', message: 'Usuario eliminado con éxito');

                    // Limpiar los datos
                    $this->limpiar();
                }
            }
        } catch (\Throwable $e) {
            Log::error("Error al eliminar un usuario", ['mensaje' => $e->getMessage()]);
            $this->dispatch('toast-danger', message: 'Ocurrio al eliminar');
        }
    }
    public function render()
    {
        $roles = rol::get();
        $areas = area::get();
        $laboratorios = laboratorio::get();
        $usuarios = usuario::with(['persona', 'rol'])
            ->search($this->query, $this->idRol) // ← usa el scope
            ->paginate(10);

        return view('livewire.usuarios.all-usuarios', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'areas' => $areas,
            'laboratorios' => $laboratorios
        ]);
    }
}
