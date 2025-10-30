<?php

namespace App\Livewire\Mantenimiento;

use App\Models\clasemantenimiento;
use App\Models\mantenimiento;
use App\Models\tipomantenimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class AllMantenimiento extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $idTpm, $idClm, $idMan;
    public $idTpmFiltro, $idClmFiltro;
    public $nombreMan;
    public $modalTitle = '';
    public $modalMessage = '';
    public $modalIcon = 'bi bi-info-circle-fill text-info';
    public bool $llave = false;
    #[Url('Busqueda')]
    public $query = '';

    public function limpiar()
    {
        // Limpia filtros y búsqueda
        $this->reset(['idMan', 'idTpm', 'idClm', 'nombreMan', 'query', 'idTpmFiltro', 'idClmFiltro']);

        // Limpia validaciones
        $this->resetErrorBag();
        $this->resetValidation();

        // Vuelve a la página 1 de la paginación
        $this->resetPage();
    }

    public function selectInfo($id)
    {
        $this->idMan = $id;
        $man = mantenimiento::find($id);
        $this->nombreMan = $man->NombreMan;
        $this->idTpm = $man->IdTpm;
        $this->idClm = $man->IdClm;
    }

    public function rules(): array
    {
        $mantTable = (new mantenimiento)->getTable();
        $tpmTable  = (new tipomantenimiento)->getTable();
        $clmTable  = (new clasemantenimiento)->getTable();
        return [
            'nombreMan' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($mantTable) {
                    // normaliza: trim + colapsa espacios + minúsculas + quita espacios
                    $norm = Str::of($value)->trim()->squish()->lower()->replace(' ', '');

                    $exists = DB::table($mantTable)
                        ->when($this->idMan, fn($q) => $q->where('IdMan', '<>', $this->idMan)) // excluir el actual si editas
                        ->whereRaw("REPLACE(LOWER(TRIM(NombreMan)),' ','') = ?", [$norm])
                        ->exists();

                    if ($exists) {
                        $fail('Ya existe un mantenimiento con ese nombre.');
                    }
                },
            ],
            'idTpm' => ['required', 'integer', "exists:{$tpmTable},IdTpm"],
            'idClm' => ['required', 'integer', "exists:{$clmTable},IdClm"],
        ];
    }
    protected $messages = [
        'nombreMan.required' => 'El nombre es obligatorio.',
        'nombreMan.max'      => 'Máximo 255 caracteres.',
        'nombreMan.unique'   => 'Ya existe un mantenimiento con ese nombre.',
        'idTpm.required'     => 'Selecciona el tipo.',
        'idTpm.exists'       => 'Tipo inválido.',
        'idClm.required'     => 'Selecciona la clase.',
        'idClm.exists'       => 'Clase inválida.',
    ];
    /*
    public function updated($prop): void
    {
        if ($prop === 'nombreMan') {
            $v = (string) $this->nombreMan;
            $v = trim($v);
            $v = preg_replace('/\s+/', ' ', $v);
            $this->nombreMan = $v;
        }
        $this->validateOnly($prop, $this->rules(), $this->messages);
    }
    */
    public function updatedNombreMan()
    {
        $this->validateOnly('nombreMan', $this->rules(), $this->messages);
    }

    public function registrarMantenimiento()
    {
        if ($this->llave) return;
        $this->llave = true;

        try {
            $this->nombreMan = (string)Str::of($this->nombreMan)->trim()->squish();
            $this->validate($this->rules(), $this->messages);
            mantenimiento::create([
                'NombreMan' => $this->nombreMan,
                'IdTpm' => $this->idTpm,
                'IdClm' => $this->idClm,
                'EstadoMan' => 1,
            ]);

            $this->reset(['nombreMan', 'idTpm', 'idClm']);

            $this->dispatch('cerrar-modal', modalId: 'kt_modal_create_mantenimiento');

            $this->modalTitle = '¡Éxito!';
            $this->modalMessage = 'Mantenimiento registrado correctamente.';
            $this->modalIcon = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error("Error al registrar mantenimiento", ['mensaje' => $e->getMessage()]);
            $this->modalTitle = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        } finally {
            $this->llave = false;
        }
    }
    public function editarMantenimiento()
    {
        if ($this->llave) return;
        $this->llave = true;
        try {
            $this->nombreMan = trim((string)$this->nombreMan);
            $this->validate($this->rules(), $this->messages);
            $man = mantenimiento::findOrFail($this->idMan);
            $man->update([
                'NombreMan' => $this->nombreMan,
                'IdTpm' => $this->idTpm,
                'IdClm' => $this->idClm,
                'EstadoMan' => 1,
            ]);
            $this->reset(['nombreMan', 'idTpm', 'idClm']);
            $this->dispatch('cerrar-modal', modalId: 'kt_modal_edit_mantenimiento');

            $this->modalTitle = '¡Éxito!';
            $this->modalMessage = 'Mantenimiento editado correctamente.';
            $this->modalIcon = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error("Error al editar mantenimiento", ['mensaje' => $e->getMessage()]);
            $this->modalTitle = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        } finally {
            $this->llave = false;
        }
    }
    public function eliminarMantenimiento()
    {
        try {
            if ($this->idMan) {
                $item = mantenimiento::where('IdMan', $this->idMan)->first();
                if ($item) {
                    mantenimiento::where('IdMan', $this->idMan)->delete();
                    $this->dispatch('cerrar-modal', modalId: 'kt_modal_eliminar_mantenimiento');
                    $this->dispatch('toast-success', message: 'Mantenimiento eliminado con éxito');
                }
            }
            $this->reset(['nombreMan', 'idTpm', 'idClm']);
            $this->modalTitle = '¡Éxito!';
            $this->modalMessage = 'Mantenimiento eliminado correctamente.';
            $this->modalIcon = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error("Error al eliminar un mantenimiento", ['mensaje' => $e->getMessage()]);
            $this->modalTitle = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        }
    }
    public function toggleEstado(int $idMan): void
    {

        $man = mantenimiento::findOrFail($idMan);

        $man->EstadoMan = $man->EstadoMan ? 0 : 1;
        $man->save();

        $this->dispatch(
            'toast-success',
            title: 'Estado actualizado',
            message: $man->EstadoMan ? 'Incidencia activado' : 'Mantenimiento desactivado'
        );
        // No hace falta más: Livewire volverá a ejecutar render() y refrescará la tabla
    }
    public function render()
    {
        $mantenimientos = mantenimiento::with(['tipomantenimiento', 'clasemantenimiento'])
            ->search($this->query, $this->idTpmFiltro, $this->idClmFiltro)
            ->paginate(10);
        $clasemantenimientos = clasemantenimiento::get();
        $tipomantenimientos = tipomantenimiento::get();
        return view('livewire.mantenimiento.all-mantenimiento', [
            'mantenimientos' => $mantenimientos,
            'clasemantenimientos' => $clasemantenimientos,
            'tipomantenimientos' => $tipomantenimientos,
        ]);
    }
}
