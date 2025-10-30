<?php

namespace App\Livewire\Perifericos;

use App\Models\periferico;
use App\Models\tipoperiferico;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AllPeriferico extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $idPef, $idTpf, $ciuPef, $codigoInventarioPef, $marcaPef, $colorPef;
    public $periferico = [];

    public $modalTitle = '';
    public $modalMessage = '';
    public $modalIcon = 'bi bi-info-circle-fill text-info';

    public bool $llave = false;

    #[Url('Busqueda')]
    public $query = '';
    public $idTpfFiltro = null;
    public $estadoPefFiltro = null;
    public function limpiar()
    {
        // Limpia filtros y búsqueda
        $this->reset(['idPef', 'idTpf', 'ciuPef', 'codigoInventarioPef', 'marcaPef', 'colorPef', 'query', 'idTpfFiltro', 'estadoPefFiltro']);

        // Limpia validaciones
        $this->resetErrorBag();
        $this->resetValidation();

        // Vuelve a la página 1
        $this->resetPage();
    }

    public function selectInfo($id)
    {
        $this->idPef = $id;
        $pef = periferico::find($id);

        if ($pef) {
            $this->idTpf               = $pef->IdTpf;
            $this->ciuPef              = $pef->CiuPef;
            $this->codigoInventarioPef = $pef->CodigoInventarioPef;
            $this->marcaPef            = $pef->MarcaPef;
            $this->colorPef            = $pef->ColorPef;
        }
    }

    
    #[On('scanner:code-detected')]
    public function onCodeDetected(string $code): void
    {
        $this->query = trim($code);
        $this->resetPage();

        // Busca el equipo por su Código de Inventario
        $item = periferico::with('tipoperiferico')
            ->where('CodigoInventarioPef', $this->query)
            ->first();

        if ($item) {
            // ✅ sincroniza los selects
            $this->idTpfFiltro      = $item->IdTpf;
            $this->estadoPefFiltro  = (int) $item->EstadoPef;

            // Mensaje de confirmación
            $this->modalTitle   = 'Equipo encontrado';
            $this->modalMessage = sprintf(
                '%s • %s • %s',
                $item->CodigoInventarioPef,
                $item->tipoperiferico->NombreTpf ?? '—',
                $item->EstadoPef ? 'Activo' : 'Inactivo'
            );
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';

            // Abre el modal de confirmación (usa tu util existente)
            $this->dispatch('modal-open', payload: [
                'title'     => $this->modalTitle,
                'message'   => $this->modalMessage,
                'variant'   => 'success',
                'autoclose' => 2200, // cierra solo
            ]);
        } else {
            // Limpia filtros si no se halló
            $this->idTpfFiltro     = null;
            $this->estadoPefFiltro = null;

            $this->modalTitle   = 'Sin coincidencias';
            $this->modalMessage = 'No existe un periférico con ese código.';
            $this->modalIcon    = 'bi bi-exclamation-triangle-fill text-warning';

            $this->dispatch('modal-open', payload: [
                'title'     => $this->modalTitle,
                'message'   => $this->modalMessage,
                'variant'   => 'warning',
                'autoclose' => 2500,
            ]);
        }

        // (Opcional) por redundancia, cierra el modal del escáner si siguiera abierto
        $this->dispatch('cerrar-modal', modalId: 'scannerModal');
    }


    public function rules(): array
    {
        $pefTable = (new periferico)->getTable();
        $tpfTable = (new tipoperiferico)->getTable();

        return [
            'codigoInventarioPef' => [
                'required',
                'string',
                'max:100',
                Rule::unique($pefTable, 'CodigoInventarioPef')->ignore($this->idPef, 'IdPef'),
            ],
            'idTpf' => ['required', 'integer', "exists:{$tpfTable},IdTpf"],

            // Si CiuPef es foránea a otra tabla, cambia a 'integer' + exists:<tabla>,<columna>.
            'ciuPef' => ['nullable', 'string', 'max:100'],

            'marcaPef' => ['required', 'string', 'max:100'],
            'colorPef' => ['nullable', 'string', 'max:50'],
        ];
    }

    protected $messages = [
        'codigoInventarioPef.required' => 'El código de inventario es obligatorio.',
        'codigoInventarioPef.max'      => 'Máximo 100 caracteres para el código.',
        'codigoInventarioPef.unique'   => 'Ya existe un periférico con ese código.',

        'idTpf.required' => 'Selecciona el tipo de periférico.',
        'idTpf.exists'   => 'Tipo de periférico inválido.',

        'ciuPef.max'   => 'Máximo 100 caracteres para la ciudad/ubicación.',

        'marcaPef.required' => 'La marca es obligatoria.',
        'marcaPef.max'      => 'Máximo 100 caracteres para la marca.',

        'colorPef.max' => 'Máximo 50 caracteres para el color.',
    ];

    public function updated($prop): void
    {
        // Normaliza strings
        if (in_array($prop, ['codigoInventarioPef', 'marcaPef', 'colorPef', 'ciuPef'], true)) {
            $this->{$prop} = trim((string) $this->{$prop});
        }

        $this->validateOnly($prop, $this->rules(), $this->messages);
    }

    public function registrarPeriferico()
    {
        if ($this->llave) return;
        $this->llave = true;

        try {
            // Asegura strings limpios
            foreach (['codigoInventarioPef', 'marcaPef', 'colorPef', 'ciuPef'] as $k) {
                $this->{$k} = trim((string) $this->{$k});
            }

            $this->validate($this->rules(), $this->messages);

            periferico::create([
                'CodigoInventarioPef' => $this->codigoInventarioPef,
                'IdTpf'               => $this->idTpf,
                'CiuPef'              => $this->ciuPef,
                'MarcaPef'            => $this->marcaPef,
                'ColorPef'            => $this->colorPef,
                'EstadoPef'           => 1,
            ]);

            $this->reset(['idTpf', 'ciuPef', 'codigoInventarioPef', 'marcaPef', 'colorPef']);

            $this->dispatch('cerrar-modal', modalId: 'kt_modal_create_periferico');

            $this->modalTitle   = '¡Éxito!';
            $this->modalMessage = 'Periférico registrado correctamente.';
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error("Error al registrar periférico", ['mensaje' => $e->getMessage()]);
            $this->modalTitle   = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon    = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        } finally {
            $this->llave = false;
        }
    }

    public function editarPeriferico()
    {
        if ($this->llave) return;
        $this->llave = true;

        try {
            foreach (['codigoInventarioPef', 'marcaPef', 'colorPef', 'ciuPef'] as $k) {
                $this->{$k} = trim((string) $this->{$k});
            }

            $this->validate($this->rules(), $this->messages);

            $pef = periferico::findOrFail($this->idPef);

            $pef->update([
                'CodigoInventarioPef' => $this->codigoInventarioPef,
                'IdTpf'               => $this->idTpf,
                'CiuPef'              => $this->ciuPef,
                'MarcaPef'            => $this->marcaPef,
                'ColorPef'            => $this->colorPef,
                'EstadoPef'           => 1,
            ]);

            $this->reset(['idTpf', 'ciuPef', 'codigoInventarioPef', 'marcaPef', 'colorPef']);

            $this->dispatch('cerrar-modal', modalId: 'kt_modal_edit_periferico');

            $this->modalTitle   = '¡Éxito!';
            $this->modalMessage = 'Periférico editado correctamente.';
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error("Error al editar periférico", ['mensaje' => $e->getMessage()]);
            $this->modalTitle   = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon    = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        } finally {
            $this->llave = false;
        }
    }

    public function eliminarPeriferico()
    {
        try {
            if ($this->idPef) {
                $item = periferico::where('IdPef', $this->idPef)->first();

                if ($item) {
                    periferico::where('IdPef', $this->idPef)->delete();

                    $this->dispatch('cerrar-modal', modalId: 'kt_modal_eliminar_periferico');
                    $this->dispatch('toast-success', message: 'Periférico eliminado con éxito');
                }
            }

            $this->reset(['idTpf', 'ciuPef', 'codigoInventarioPef', 'marcaPef', 'colorPef']);

            $this->modalTitle   = '¡Éxito!';
            $this->modalMessage = 'Periférico eliminado correctamente.';
            $this->modalIcon    = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error("Error al eliminar un periférico", ['mensaje' => $e->getMessage()]);
            $this->modalTitle   = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon    = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        }
    }

    public function render()
    {
        $perifericos = periferico::with(['tipoperiferico'])
            ->search($this->query, $this->idTpfFiltro, null, null, null, $this->estadoPefFiltro) // Define este scope en tu modelo
            ->paginate(14);

        $tipoperifericos = tipoperiferico::get();

        return view('livewire.perifericos.all-periferico', [
            'perifericos'      => $perifericos,
            'tipoperifericos'  => $tipoperifericos,
        ]);
    }
}
