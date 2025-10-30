<?php

namespace App\Livewire\Incidencia;

use App\Models\incidencia;
use App\Models\mantenimiento;
use App\Models\tipoperiferico;
use App\Models\usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AllIncidencia extends Component
{
    use WithPagination;

    // Campos
    public $idInc, $idMan, $idTpf, $nombreInc;

    // Estado de modales
    public $modalTitle = '';
    public $modalMessage = '';
    public $modalIcon = 'bi bi-info-circle-fill text-info';

    // Llave para evitar doble submit
    public bool $llave = false;

    // Filtros en URL
    #[Url('Busqueda')]
    public $query = '';
    public $idManFiltro = null;
    public $idTpfFiltro = null;

    /**
     * Limpia filtros, búsqueda y validaciones
     */
    public function limpiar()
    {
        $this->reset([
            'idInc',
            'idMan',
            'idTpf',
            'nombreInc',
            'query',
            'idManFiltro',
            'idTpfFiltro'
        ]);

        $this->resetErrorBag();
        $this->resetValidation();
        $this->resetPage();
    }

    /**
     * Carga info de una incidencia seleccionada
     */
    public function selectInfo($id): void
    {
        $this->idInc = $id;
        $inc = incidencia::find($id);

        if ($inc) {
            $this->idMan     = $inc->IdMan;
            $this->idTpf     = $inc->IdTpf;
            $this->nombreInc = $inc->NombreInc;
        }
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        $incTable  = (new incidencia)->getTable();
        $manTable  = (new mantenimiento)->getTable();
        $tpfTable  = (new tipoperiferico)->getTable();

        return [
            'nombreInc' => [
                'required',
                'string',
                'max:150',
                Rule::unique($incTable, 'NombreInc')->ignore($this->idInc, 'IdInc'),
            ],
            'idMan' => ['required', 'integer', "exists:{$manTable},IdMan"],
            'idTpf' => ['required', 'integer', "exists:{$tpfTable},IdTpf"],
        ];
    }

    /**
     * Mensajes de validación
     */
    protected $messages = [
        'nombreInc.required' => 'El nombre de la incidencia es obligatorio.',
        'nombreInc.max'      => 'Máximo 150 caracteres para el nombre.',
        'nombreInc.unique'   => 'Ya existe una incidencia con ese nombre.',

        'idMan.required' => 'Selecciona el mantenimiento.',
        'idMan.exists'   => 'Mantenimiento inválido.',

        'idTpf.required' => 'Selecciona el tipo de periferico.',
        'idTpf.exists'   => 'Tipo de periferico inválido.',
    ];

    /**
     * Validación reactiva y normalización
     */
    public function updated($prop): void
    {
        
        $this->validateOnly($prop, $this->rules(), $this->messages);
    }

    /**
     * Crear incidencia
     */
    public function registrarIncidencia()
    {
        if ($this->llave) return;
        $this->llave = true;

        try {
            $this->nombreInc = trim((string) $this->nombreInc);
            $this->validate($this->rules(), $this->messages);

            incidencia::create([
                'NombreInc' => strtoupper($this->nombreInc),
                'IdMan'     => $this->idMan,
                'IdTpf'     => $this->idTpf,
                'EstadoInc' => 1,
            ]);

            $this->reset(['idMan', 'idTpf', 'nombreInc']);

            $this->dispatch('cerrar-modal', modalId: 'kt_modal_create_incidencia');

            $this->modalTitle   = '¡Éxito!';
            $this->modalMessage = 'Incidencia registrada correctamente.';
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error('Error al registrar incidencia', ['mensaje' => $e->getMessage()]);
            $this->modalTitle   = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon    = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        } finally {
            $this->llave = false;
        }
    }

    /**
     * Editar incidencia
     */
    public function editarIncidencia()
    {
        if ($this->llave) return;
        $this->llave = true;

        try {
            $this->nombreInc = trim((string) $this->nombreInc);
            $this->validate($this->rules(), $this->messages);

            $inc = incidencia::findOrFail($this->idInc);

            $inc->update([
                'NombreInc' => strtoupper($this->nombreInc),
                'IdMan'     => $this->idMan,
                'IdTpf'     => $this->idTpf,
                'EstadoInc' => 1,
            ]);

            $this->reset(['idMan', 'idTpf', 'nombreInc']);

            $this->dispatch('cerrar-modal', modalId: 'kt_modal_edit_incidencia');

            $this->modalTitle   = '¡Éxito!';
            $this->modalMessage = 'Incidencia editada correctamente.';
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error('Error al editar incidencia', ['mensaje' => $e->getMessage()]);
            $this->modalTitle   = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon    = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        } finally {
            $this->llave = false;
        }
    }

    /**
     * Eliminar incidencia
     */
    public function eliminarIncidencia()
    {
        try {
            if ($this->idInc) {
                $item = incidencia::where('IdInc', $this->idInc)->first();

                if ($item) {
                    incidencia::where('IdInc', $this->idInc)->delete();

                    $this->dispatch('cerrar-modal', modalId: 'kt_modal_eliminar_incidencia');
                    $this->dispatch('toast-success', message: 'Incidencia eliminada con éxito');
                }
            }

            $this->reset(['idMan', 'idTpf', 'nombreInc']);

            $this->modalTitle   = '¡Éxito!';
            $this->modalMessage = 'Incidencia eliminada correctamente.';
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar una incidencia', ['mensaje' => $e->getMessage()]);
            $this->modalTitle   = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon    = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        }
    }

    public function toggleEstado(int $idInc): void
    {
        
        $inc = incidencia::findOrFail($idInc);

        $inc->EstadoInc = $inc->EstadoInc ? 0 : 1;
        $inc->save();

        $this->dispatch(
            'toast-success',
            title: 'Estado actualizado',
            message: $inc->EstadoInc ? 'Incidencia activado' : 'Incidencia desactivado'
        );
        // No hace falta más: Livewire volverá a ejecutar render() y refrescará la tabla
    }

    /**
     * Render
     */
    public function render()
    {
        $incidencias = incidencia::with(['mantenimiento', 'tipoperiferico'])
            ->search($this->query, $this->idManFiltro, $this->idTpfFiltro) // Define este scope en tu modelo
            ->paginate(10);

        $mantenimientos = mantenimiento::where('IdTpm', 2)
            ->whereNotIn('IdMan', incidencia::where('EstadoInc', 1)->select('IdMan'))
            ->get();
        $tiposperiferico     = tipoperiferico::get();

        return view('livewire.incidencia.all-incidencia', [
            'incidencias'     => $incidencias,
            'mantenimientos'  => $mantenimientos,
            'tiposperiferico'      => $tiposperiferico,
        ]);
    }
}
