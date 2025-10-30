<?php

namespace App\Livewire\Laboratorios;

use App\Models\laboratorio;
use App\Models\area;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AllLaboratorios extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $idLab, $idAre, $nombreLab;

    public $modalTitle = '';
    public $modalMessage = '';
    public $modalIcon = 'bi bi-info-circle-fill text-info';

    public bool $llave = false;

    #[Url('Busqueda')]
    public $query = '';
    public $idAreFiltro = null;

    public function limpiar()
    {
        // Limpia filtros y búsqueda
        $this->reset(['idLab', 'idAre', 'nombreLab', 'query', 'idAreFiltro']);

        // Limpia validaciones
        $this->resetErrorBag();
        $this->resetValidation();

        // Vuelve a la página 1
        $this->resetPage();
    }

    public function selectInfo($id)
    {
        $this->idLab = $id;
        $lab = laboratorio::find($id);

        if ($lab) {
            $this->idAre     = $lab->IdAre;
            $this->nombreLab = $lab->NombreLab;
        }
    }

    public function toggleEstado(int $id)
    {
        $lab = laboratorio::findOrFail($id);
        $lab->EstadoLab = $lab->EstadoLab ? 0 : 1;
        $lab->save();
        $this->dispatch(
            'toast-success',
            title: 'Estado Actualizado',
            message: $lab->EstadoLab ? 'Laboratorio Activado' : 'Laboratorio Desactivado'
        );
    }
    public function rules(): array
    {
        $labTable  = (new laboratorio)->getTable();
        $areaTable = (new area)->getTable();

        return [
            'nombreLab' => [
                'required',
                'string',
                'max:150',
                Rule::unique($labTable, 'NombreLab')->ignore($this->idLab, 'IdLab'),
            ],
            'idAre' => ['required', 'integer', "exists:{$areaTable},IdAre"],
        ];
    }

    protected $messages = [
        'nombreLab.required' => 'El nombre del laboratorio es obligatorio.',
        'nombreLab.max'      => 'Máximo 150 caracteres para el nombre.',
        'nombreLab.unique'   => 'Ya existe un laboratorio con ese nombre.',

        'idAre.required' => 'Selecciona el área.',
        'idAre.exists'   => 'Área inválida.',
    ];

    public function updated($prop): void
    {
        // Normaliza strings
        if ($prop === 'nombreLab') {
            $this->nombreLab = trim((string) $this->nombreLab);
        }

        $this->validateOnly($prop, $this->rules(), $this->messages);
    }

    public function registrarLaboratorio()
    {
        if ($this->llave) return;
        $this->llave = true;

        try {
            // Asegura strings limpios
            $this->nombreLab = trim((string) $this->nombreLab);

            $this->validate($this->rules(), $this->messages);

            laboratorio::create([
                'NombreLab' => $this->nombreLab,
                'IdAre'     => $this->idAre,
                'EstadoLab' => 1,
            ]);

            $this->reset(['idAre', 'nombreLab']);

            $this->dispatch('cerrar-modal', modalId: 'kt_modal_create_laboratorio');

            $this->modalTitle   = '¡Éxito!';
            $this->modalMessage = 'Laboratorio registrado correctamente.';
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error("Error al registrar laboratorio", ['mensaje' => $e->getMessage()]);
            $this->modalTitle   = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon    = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        } finally {
            $this->llave = false;
        }
    }

    public function editarLaboratorio()
    {
        if ($this->llave) return;
        $this->llave = true;

        try {
            $this->nombreLab = trim((string) $this->nombreLab);

            $this->validate($this->rules(), $this->messages);

            $lab = laboratorio::findOrFail($this->idLab);

            $lab->update([
                'NombreLab' => $this->nombreLab,
                'IdAre'     => $this->idAre,
                'EstadoLab' => 1,
            ]);

            $this->reset(['idAre', 'nombreLab']);

            $this->dispatch('cerrar-modal', modalId: 'kt_modal_edit_laboratorio');

            $this->modalTitle   = '¡Éxito!';
            $this->modalMessage = 'Laboratorio editado correctamente.';
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error("Error al editar laboratorio", ['mensaje' => $e->getMessage()]);
            $this->modalTitle   = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon    = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        } finally {
            $this->llave = false;
        }
    }

    public function eliminarLaboratorio()
    {
        try {
            if ($this->idLab) {
                $item = laboratorio::where('IdLab', $this->idLab)->first();

                if ($item) {
                    laboratorio::where('IdLab', $this->idLab)->delete();

                    $this->dispatch('cerrar-modal', modalId: 'kt_modal_eliminar_laboratorio');
                    $this->dispatch('toast-success', message: 'Laboratorio eliminado con éxito');
                }
            }

            $this->reset(['idAre', 'nombreLab']);

            $this->modalTitle   = '¡Éxito!';
            $this->modalMessage = 'Laboratorio eliminado correctamente.';
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error("Error al eliminar un laboratorio", ['mensaje' => $e->getMessage()]);
            $this->modalTitle   = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon    = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        }
    }

    public function render()
    {
        $laboratorios = laboratorio::with(['area'])
            ->search($this->query, $this->idAreFiltro) // Define este scope en tu modelo
            ->paginate(10);

        $areas = area::get();

        return view('livewire.laboratorios.all-laboratorios', [
            'laboratorios' => $laboratorios,
            'areas'        => $areas,
        ]);
    }
}
