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
    public function cargarLaboratorios()
    {
        $this->laboratoriosPorArea = laboratorio::where('IdAre', $this->idAre)->get();
        $this->laboratoriosSeleccionados = []; //limpiar
    }

    public function rulesCrear()
    {
        return [
            'usernameUsa' => 'required|string|unique:usuario,UsernameUsa',
            'passwordUsa' => 'required|string|min:6',

            'nombrePer' => 'required|regex:/^[\pL\s\-]+$/u',
            'apellidoPaternoPer' => 'required|regex:/^[\pL\s\-]+$/u',
            'apellidoMaternoPer' => 'required|regex:/^[\pL\s\-]+$/u',

            'correoPer' => 'required|email|unique:persona,CorreoPer',
            'dniPer' => 'required|regex:/^\d{8}$/|unique:persona,DniPer',
            'telefonoPer' => ['required', 'regex:/^9\d{8}$/'],

            'idRol' => 'required|exists:rol,IdRol',
            'idAre' => 'required|exists:area,IdAre',
            'fechaNacimientoPer' => 'required|date|before:2007-01-01',

            'laboratoriosSeleccionados' => 'required|array|min:1',
            'laboratoriosSeleccionados.*' => 'exists:laboratorio,IdLab'
        ];
    }

    public function rulesEditar()
    {
        return [
            'usernameUsa' => [
                'required',
                'string',
                Rule::unique('usuario', 'UsernameUsa')->ignore($this->idUsa, 'IdUsa'),
            ],
            'passwordUsa' => 'nullable|string|min:6',

            'nombrePer' => 'required|regex:/^[\pL\s\-]+$/u',
            'apellidoPaternoPer' => 'required|regex:/^[\pL\s\-]+$/u',
            'apellidoMaternoPer' => 'required|regex:/^[\pL\s\-]+$/u',

            'correoPer' => 'required|email', // <<< ya sin unique
            'dniPer' => ['required', 'regex:/^\d{8}$/'], // <<< ya sin unique

            'telefonoPer' => ['required', 'regex:/^9\d{8}$/'],
            'idRol' => 'required|exists:rol,IdRol',
            'idAre' => 'required|exists:area,IdAre',
            'fechaNacimientoPer' => 'required|date|before:2007-01-01',

            'laboratoriosSeleccionados' => 'required|array|min:1',
            'laboratoriosSeleccionados.*' => 'exists:laboratorio,IdLab'
        ];
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
        'laboratoriosSeleccionados.min' => 'Seleccione al menos un laboratorio.',
    ];


    public function cargarDatosParaEditar($id)
    {
        $this->idUsa = $id;
        $usuario = usuario::with(['persona', 'detalleusuario'])->findOrFail($id);

        $this->usernameUsa = $usuario->UsernameUsa;
        $this->idRol = $usuario->IdRol;
        $this->passwordUsa = ''; // no se muestra por seguridad

        // Datos persona
        $this->nombrePer = $usuario->persona->NombrePer;
        $this->apellidoPaternoPer = $usuario->persona->ApellidoPaternoPer;
        $this->apellidoMaternoPer = $usuario->persona->ApellidoMaternoPer;
        $this->dniPer = $usuario->persona->DniPer;
        $this->telefonoPer = $usuario->persona->TelefonoPer;
        $this->correoPer = $usuario->persona->CorreoPer;
        $this->fechaNacimientoPer = $usuario->persona->FechaNacimientoPer;

        // Área y laboratorios
        $this->idAre = optional($usuario->detalleusuario->first()->laboratorio)->IdAre;
        $this->cargarLaboratorios();
        $this->laboratoriosSeleccionados = $usuario->detalleusuario->pluck('IdLab')->toArray();
    }

    public function actualizarUsuario()
    {
        $this->validate($this->rulesEditar());

        try {
            $usuario = usuario::findOrFail($this->idUsa);
            $persona = $usuario->persona;

            // Validar solo si se cambió el correo
            if ($this->correoPer !== $persona->CorreoPer) {
                $this->validateOnly('correoPer', [
                    'correoPer' => 'required|email|unique:persona,CorreoPer',
                ]);
            }

            // Validar solo si se cambió el DNI
            if ($this->dniPer !== $persona->DniPer) {
                $this->validateOnly('dniPer', [
                    'dniPer' => 'required|regex:/^\d{8}$/|unique:persona,DniPer',
                ]);
            }

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
            if (!empty($this->passwordUsa)) {
                $usuario->PasswordUsa = bcrypt($this->passwordUsa);
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


    public function registrarUsuario()
    {
        $this->validate($this->rulesCrear());

        try {
            $persona = persona::create([
                'NombrePer' => $this->nombrePer,
                'ApellidoPaternoPer' => $this->apellidoPaternoPer,
                'ApellidoMaternoPer' => $this->apellidoMaternoPer,
                'FechaNacimientoPer' => $this->fechaNacimientoPer,
                'DniPer' => $this->dniPer,
                'TelefonoPer' => $this->telefonoPer,
                'CorreoPer' => $this->correoPer,
                'EstadoPer' => 1
            ]);
            $usuario = usuario::create([
                'IdRol' => $this->idRol,
                'UsernameUsa' => $this->usernameUsa,
                'PasswordUsa' => bcrypt($this->passwordUsa),
                'IdPer' => $persona->IdPer,
                'EstadoUsa' => 1,
            ]);
            foreach ($this->laboratoriosSeleccionados as $idLab) {
                detalleusuario::create([
                    'IdUsa' => $usuario->IdUsa,
                    'IdLab' => $idLab,
                    'EstadoDtu' => 1
                ]);
            }


            // Solo cerrar modal y limpiar si todo fue exitoso
            $this->dispatch('cerrar-modal', modalId: 'kt_modal_create_usuario');
            $this->dispatch('toast-success', [
                'title' => '¡Usuario registrado correctamente!',
                'message' => 'El usuario ha sido creado exitosamente en el sistema.'
            ]);

            $this->limpiar();
        } catch (\Throwable $e) {
            Log::error("Error al registrar usuario", [
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ]);

            // Agregar error personalizado si es necesario
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
                    $this->dispatch('toast-success', message: 'Equipo eliminado con éxito');

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
        $queryUsuarios = usuario::with(['persona', 'rol']);

        if (!empty($this->query)) {
            $queryUsuarios->whereHas('persona', function ($q) {
                $q->where('NombrePer', 'like', '%' . $this->query . '%')
                    ->orWhere('ApellidoPaternoPer', 'like', '%' . $this->query . '%')
                    ->orWhere('ApellidoMaternoPer', 'like', '%' . $this->query . '%');
            })->orWhere('UsernameUsa', 'like', '%' . $this->query . '%');
        }

        // Filtro por rol si está seleccionado
        if (!empty($this->idRol)) {
            $queryUsuarios->where('IdRol', $this->idRol);
        }

        $usuarios = $queryUsuarios->paginate(10);
        return view('livewire.usuarios.all-usuarios', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'areas' => $areas,
            'laboratorios' => $laboratorios
        ]);
    }
}
