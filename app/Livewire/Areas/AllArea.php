<?php

namespace App\Livewire\Areas;

use App\Models\area;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AllArea extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $idAre, $nombreAre;

    public $modalTitle = '';
    public $modalMessage = '';
    public $modalIcon = 'bi bi-info-circle-fill text-info';

    public bool $llave = false;

    #[Url('Busqueda')]
    public $query = '';
    public $idAreFiltro = '';
    public function limpiar()
    {
        $this->reset(['idAre', 'nombreAre', 'query']);
        $this->resetErrorBag();
        $this->resetValidation();
        $this->resetPage();
    }

    public function selectInfo($id)
    {
        $this->idAre = $id;
        $are = area::find($id);

        if ($are) {
            $this->nombreAre = $are->NombreAre;
        }
    }

    public function toggleEstado(int $idAre)
    {
        $ar = area::findOrFail($idAre);
        $ar->EstadoAre = $ar->EstadoAre ? 0 : 1;
        $ar->save();
        $this->dispatch(
            'toast-success',
            title: 'Estado actualizado',
            message: $ar->EstadoAre ? 'Area actiada' : 'Area desactivada'
        );
    }
    public function rules(): array
    {
        $table = (new area)->getTable();

        return [
            'nombreAre' => [
                'required',
                'string',
                'max:150',
                Rule::unique($table, 'NombreAre')->ignore($this->idAre, 'IdAre'),
            ],
        ];
    }

    protected $messages = [
        'nombreAre.required' => 'El nombre del área es obligatorio.',
        'nombreAre.max'      => 'Máximo 150 caracteres para el nombre.',
        'nombreAre.unique'   => 'Ya existe un área con ese nombre.',
    ];

    public function updated($prop): void
    {
        if ($prop === 'nombreAre') {
            $this->nombreAre = trim((string) $this->nombreAre);
        }

        $this->validateOnly($prop, $this->rules(), $this->messages);
    }

    public function registrarArea()
    {
        if ($this->llave) return;
        $this->llave = true;

        try {
            $this->nombreAre = trim((string) $this->nombreAre);

            $this->validate($this->rules(), $this->messages);

            area::create([
                'NombreAre' => $this->nombreAre,
                'EstadoAre' => 1,
            ]);

            $this->reset(['nombreAre']);

            $this->dispatch('cerrar-modal', modalId: 'kt_modal_create_area');

            $this->modalTitle   = '¡Éxito!';
            $this->modalMessage = 'Área registrada correctamente.';
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error("Error al registrar área", ['mensaje' => $e->getMessage()]);
            $this->modalTitle   = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon    = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        } finally {
            $this->llave = false;
        }
    }

    public function editarArea()
    {
        if ($this->llave) return;
        $this->llave = true;

        try {
            $this->nombreAre = trim((string) $this->nombreAre);

            $this->validate($this->rules(), $this->messages);

            $are = area::findOrFail($this->idAre);

            $are->update([
                'NombreAre' => $this->nombreAre,
                'EstadoAre' => 1,
            ]);

            $this->reset(['nombreAre']);

            $this->dispatch('cerrar-modal', modalId: 'kt_modal_edit_area');

            $this->modalTitle   = '¡Éxito!';
            $this->modalMessage = 'Área editada correctamente.';
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error("Error al editar área", ['mensaje' => $e->getMessage()]);
            $this->modalTitle   = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon    = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        } finally {
            $this->llave = false;
        }
    }

    public function eliminarArea()
    {
        try {
            if ($this->idAre) {
                $item = area::where('IdAre', $this->idAre)->first();

                if ($item) {
                    area::where('IdAre', $this->idAre)->delete();

                    $this->dispatch('cerrar-modal', modalId: 'kt_modal_eliminar_area');
                    $this->dispatch('toast-success', message: 'Área eliminada con éxito');
                }
            }

            $this->reset(['nombreAre']);

            $this->modalTitle   = '¡Éxito!';
            $this->modalMessage = 'Área eliminada correctamente.';
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error("Error al eliminar un área", ['mensaje' => $e->getMessage()]);
            $this->modalTitle   = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon    = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        }
    }

    public function render()
    {
        $areas = area::search($this->query, $this->idAreFiltro) // scopeSearch en el modelo
            ->paginate(10);

        return view('livewire.areas.all-area', [
            'areas' => $areas,
        ]);
    }
}
