<?php

namespace App\Livewire\Equipos;

use App\Models\detalleequipo;
use App\Models\equipo;
use App\Models\laboratorio;
use App\Models\periferico;
use App\Models\tipoperiferico;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use function Termwind\render;

class AllEquipos extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $idLabFiltro;
    public $idEqo, $idLab, $nombreEqo, $codigoEqo,
        $mostrarPerifericos = [],
        $mostrarEquipos = [],
        $idTpf,
        $mostrarPerifericosNoAsignados = [];
    public $perifericosSeleccionados = [];
    #[Url('Busqueda')]
    public $query = '';
    public function limpiar()
    {
        $this->reset();
        $this->resetErrorBag();
    }
    public function selectInfo($id)
    {
        $this->idEqo = $id;
        $equipo = equipo::find($id);
        $this->nombreEqo = $equipo->NombreEqo;
        $this->codigoEqo = $equipo->CodigoEqo;
        $this->mostrarPerifericos();
    }
    public function updated($prop)
    {
        // Si cambian el nombre pero no hay laboratorio, valida solo reglas básicas
        if ($prop === 'nombreEqo' && empty($this->idLab)) {
            $this->validateOnly('nombreEqo', [
                'nombreEqo' => ['bail', 'required', 'string', 'min:3', 'max:100'],
            ], $this->messages());
            return;
        }

        $this->validateOnly($prop, $this->rules(), $this->messages());
    }
    public function rules(): array
    {
        $isupdate = filled($this->idEqo);
        return [
            'nombreEqo' => array_filter([
                'bail',
                'required',
                'string',
                'min:3',
                'max:30',
                $this->idLab ?
                    Rule::unique('equipo', 'NombreEqo')
                    ->where(fn($q) => $q->where('IdLab', $this->idLab))
                    ->when($isupdate, fn($r) => $r->ignore($this->idEqo, 'IdEqo'))
                    : null
            ]),
            'codigoEqo' => ['nullable', 'string', 'max:50', 'regex:/^\d*$/'],
            'idLab' => ['required', 'integer', 'exists:laboratorio,IdLab'],
            'perifericosSeleccionados' => ['array', 'size:4'],
            'perifericosSeleccionados.*.IdPef' => ['required', 'integer', 'distinct', 'exists:periferico,IdPef'],
            'perifericosSeleccionados.*.IdTpf' => ['required', 'integer', 'in:1,2,3,4']
        ];
    }
    public function messages(): array
    {
        return [
            // nombre
            'nombreEqo.required' => 'Ingresa el nombre del equipo.',
            'nombreEqo.min'      => 'El nombre debe tener al menos :min caracteres.',
            'nombreEqo.max'      => 'El nombre no debe superar :max caracteres.',
            'nombreEqo.unique'   => 'Ya existe un equipo con ese nombre en este laboratorio.',

            // código
            'codigoEqo.regex' => 'El código solo puede contener números.',
            'codigoEqo.max'   => 'El código no debe pasar de :max dígitos.',

            // laboratorio
            'idLab.required' => 'Selecciona el laboratorio.',
            'idLab.exists'   => 'El laboratorio seleccionado no existe.',

            // periféricos
            'perifericosSeleccionados.size' => 'Debes seleccionar exactamente 4 periféricos.',
            'perifericosSeleccionados.*.IdPef.distinct' => 'Hay periféricos repetidos.',
            'perifericosSeleccionados.*.IdPef.exists'   => 'Uno de los periféricos ya no existe.',
            'perifericosSeleccionados.*.IdTpf.in'       => 'Solo se permiten Monitor, CPU, Teclado y Ratón.',
        ];
    }
    public function validationAttributes(): array
    {
        // Para que los mensajes automáticos usen estos nombres
        return [
            'nombreEqo' => 'nombre',
            'codigoEqo' => 'código',
            'idLab'     => 'laboratorio',
        ];
    }

    private function assertTiposCompletos(): void
    {
        $req = [1 => 'Monitor', 2 => 'CPU', 3 => 'Teclado', 4 => 'Ratón'];
        $tipos = collect($this->perifericosSeleccionados)->pluck('IdTpf');

        if ($tipos->unique()->count() !== $tipos->count()) {
            throw ValidationException::withMessages([
                'perifericosSeleccionados' => 'Solo se permite un periférico por tipo.',
            ]);
        }

        $faltan = collect(array_keys($req))->diff($tipos)->values();
        if ($faltan->isNotEmpty()) {
            $nombres = $faltan->map(fn($id) => $req[$id])->implode(', ');
            throw ValidationException::withMessages([
                'perifericosSeleccionados' => "Te faltan: $nombres.",
            ]);
        }
    }


    public function updatedCodigoEqo($val)
    {
        $val = preg_replace('/\D+/', '', (string) $val);
        $this->codigoEqo = ($val === '') ? null : $val;  // <- clave
    }
    public function selectEditarEquipo($id)
    {
        $this->idEqo = $id;
        $equipo = equipo::findOrFail($id);

        $this->nombreEqo = $equipo->NombreEqo;
        $this->codigoEqo = $equipo->CodigoEqo;
        $this->idLab = $equipo->IdLab;

        $detalles = detalleequipo::where('IdEqo', $id)->with('periferico.tipoperiferico')->get();

        $this->perifericosSeleccionados = $detalles->map(function ($detalle) {
            return $detalle->periferico->toArray();
        })->toArray();

        // Para recargar lista de disponibles
        $this->mostrarPerifericosDisponibles();

        $this->dispatch('abrir-modal', modalId: 'kt_modal_editar_equipo');
    }
    public function actualizarEquipo()
    {
        try {
            $this->codigoEqo = ($this->codigoEqo === '') ? null : $this->codigoEqo; // <- aquí
            $this->validate($this->rules(), $this->messages());
            $this->assertTiposCompletos();
            $equipo = equipo::findOrFail($this->idEqo);
            $equipo->update([
                'NombreEqo' => $this->nombreEqo,
                'CodigoEqo' => $this->codigoEqo,
                'IdLab' => $this->idLab,
            ]);

            // Eliminar y recrear periféricos
            detalleequipo::where('IdEqo', $this->idEqo)->delete();
            foreach ($this->perifericosSeleccionados as $perif) {
                detalleequipo::create([
                    'IdEqo' => $this->idEqo,
                    'IdPef' => $perif['IdPef'],
                    'EstadoDte' => 1
                ]);
            }

            $this->dispatch('cerrar-modal', modalId: 'kt_modal_editar_equipo');
            $this->dispatch('toast-success', message: 'Equipo actualizado correctamente');
            $this->limpiar();
        } catch (\Throwable $e) {
            Log::error("Error al actualizar equipo", ['mensaje' => $e->getMessage()]);
            $this->dispatch('toast-danger', message: 'Ocurrió un error al actualizar el equipo');
        }
    }

    public function registrarEquipo()
    {
        try {
            $this->idEqo = null;
            $this->codigoEqo = ($this->codigoEqo === '') ? null : $this->codigoEqo; // <- aquí
            $this->validate($this->rules(), $this->messages());
            $this->assertTiposCompletos();
  
            $equipo = equipo::create([
                'IdLab' => $this->idLab,
                'NombreEqo' => $this->nombreEqo,
                'CodigoEqo' => $this->codigoEqo,
                'EstadoEqo' => true,
            ]);

            // Guardar los detalles de periféricos (DTE)
            foreach ($this->perifericosSeleccionados as $periferico) {
                detalleequipo::create([
                    'IdEqo' => $equipo->IdEqo,
                    'IdPef' => $periferico['IdPef'],
                    'EstadoDte' => 1,
                ]);
            }

            // Aquí emitimos eventos al frontend
            $this->dispatch('cerrar-modal', modalId: 'kt_modal_create_equipo');
            $this->dispatch('toast-success', message: 'Equipo registrado correctamente');
            $this->reset(['nombreEqo', 'codigoEqo', 'idLab', 'idTpf', 'perifericosSeleccionados']);
        } catch (\Throwable $e) {
            Log::error("Error al registrar equipo", ['mensaje' => $e->getMessage()]);
            $this->dispatch('toast-danger', message: 'Ocurrió un error al registrar el equipo.');
        }

        $this->render();
    }

    public function eliminarEquipo()
    {
        try {
            if ($this->idEqo) {
                $item = equipo::where('IdEqo', $this->idEqo)->first();
                if ($item) {
                    equipo::where('IdEqo', $this->idEqo)->delete();
                    // Cerrar el modal y mostrar mensaje de éxito
                    $this->dispatch('cerrar-modal', modalId: 'kt_modal_eliminar_equipo');
                    $this->dispatch('toast-success', message: 'Equipo eliminado con éxito');

                    // Limpiar los datos
                    $this->limpiar();
                }
            }
        } catch (\Throwable $e) {
            Log::error("Error al eliminar equipo", ['mensaje' => $e->getMessage()]);
            $this->dispatch('toast-danger', message: 'Ocurrio al eliminar');
        }
    }
    public function mostrarPerifericos()
    {
        try {
            if ($this->idEqo) {
                $this->mostrarPerifericos = detalleequipo::where('IdEqo', $this->idEqo)->get();
            }
        } catch (\Throwable $e) {
            Log::error("Error al mostrar periferico", ['mensaje' => $e->getMessage()]);
            $this->dispatch('toast-danger', message: 'Ocurrio al mosrtar perifericos');
        }
    }
    public function mostrarPerifericosDisponibles()
    {
        try {
            // Obtener todos los IdPerif que ya están asignados a algún equipo
            $perifericosYaAsignados = detalleequipo::pluck('IdPef');
            // Mostrar todos los periféricos que no están asignados
            $this->mostrarPerifericosNoAsignados = periferico::whereNotIn('IdPef', $perifericosYaAsignados)->get();
        } catch (\Throwable $e) {
            Log::error("Error al mostrar periféricos disponibles", ['mensaje' => $e->getMessage()]);
            $this->dispatch('toast-danger', message: 'Error al cargar los periféricosaaaa');
        }
    }
    public function agregarPeriferico($idPef)
    {
        $perif = periferico::with('tipoperiferico')->find($idPef);
        if (!$perif) return;

        // No más de 4
        if (count($this->perifericosSeleccionados) >= 4) return;

        // Canoniza: un solo periférico por TIPO
        $sel = collect($this->perifericosSeleccionados)->keyBy('IdTpf');
        // si ya hay de ese tipo, este lo reemplaza; no se duplica
        $sel[$perif->IdTpf] = $perif->toArray();
        $this->perifericosSeleccionados = $sel->values()->all();

        // Saca el agregado de disponibles
        $this->mostrarPerifericosNoAsignados = collect($this->mostrarPerifericosNoAsignados)
            ->reject(fn($p) => $p->IdPef == $idPef)
            ->values()
            ->all();
    }


    public function quitarPeriferico($idPef)
    {
        $this->perifericosSeleccionados = collect($this->perifericosSeleccionados)
            ->reject(fn($p) => $p['IdPef'] == $idPef)
            ->values()
            ->all();

        if ($rep = periferico::with('tipoperiferico')->find($idPef)) {
            // Evita duplicarlo en disponibles si ya estaba
            $this->mostrarPerifericosNoAsignados = collect($this->mostrarPerifericosNoAsignados)
                ->keyBy('IdPef')
                ->put($idPef, $rep)
                ->values()
                ->all();
        }
    }


    public function render()
    {
        $laboratorios = laboratorio::get();
        $equipoquery = equipo::query()
            ->search($this->query)
            ->when($this->idLab, fn($q) => $q->where('IdLab', $this->idLab));
        $this->mostrarEquipos = $equipoquery->get();
        $this->mostrarPerifericos();
        if (empty($this->mostrarPerifericosNoAsignados)) {
            $this->mostrarPerifericosDisponibles();
        }
        $tiposperifericos = tipoperiferico::get();
        $equipos = equipo::with('laboratorio')
            ->search($this->query, $this->idLabFiltro)
            ->orderByRaw('LENGTH(NombreEqo) ASC, NombreEqo ASC')
            ->paginate(15);
        return view('livewire.equipos.all-equipos', [
            'equipos' => $equipos,
            'laboratorios' => $laboratorios,
            'mostrarPerifericos' => $this->mostrarPerifericos(),
            'mostrarPerifericosNoAsignados' => $this->mostrarPerifericosNoAsignados,
            'tiposperifericos' => $tiposperifericos
        ]);
    }
}
