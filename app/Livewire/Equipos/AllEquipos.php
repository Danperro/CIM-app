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

use function Termwind\render;

class AllEquipos extends Component
{
    use WithPagination;
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
        $this->validate([
            'nombreEqo' => 'required|string',
            'codigoEqo' => 'required|string',
            'idLab' => 'required|exists:laboratorio,IdLab',
            'perifericosSeleccionados' => 'required|array|min:1',
        ]);

        try {
            $equipo = equipo::findOrFail($this->idEqo);

            // Validar que no se repita el nombre en el mismo laboratorio
            $equipoExistente = equipo::where('NombreEqo', $this->nombreEqo)
                ->where('IdLab', $this->idLab)
                ->where('IdEqo', '!=', $this->idEqo)
                ->first();

            if ($equipoExistente) {
                $this->dispatch('toast-danger', message: 'Ya existe un equipo con ese nombre en este laboratorio');
                return;
            }

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
            // ✅ Punto 3: Validar que haya periféricos seleccionados
            if (count($this->perifericosSeleccionados) == 0) {
                $this->dispatch('toast-danger', message: 'Debe seleccionar al menos un periférico.');
                return;
            }

            // Validar si ya existe un equipo con mismo nombre en ese laboratorio
            $equipoex = equipo::where('NombreEqo', $this->nombreEqo)
                ->where('IdLab', $this->idLab)
                ->first();

            if (!$equipoex) {
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
            } else {
                $this->dispatch('toast-danger', message: 'Ya existe un equipo con ese nombre en este laboratorio');
            }
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

        // Verificar si ya hay un periférico de ese tipo en los seleccionados
        $yaExisteTipo = collect($this->perifericosSeleccionados)
            ->contains(function ($p) use ($perif) {
                return $p['IdTpf'] == $perif->IdTpf;
            });

        if ($yaExisteTipo) {
            $this->dispatch('toast-danger', message: 'Ya has seleccionado un periférico de tipo: ' . $perif->tipoperiferico->NombreTpf);
            return;
        }

        // Verificar si ya existe ese mismo periférico por ID
        if (!collect($this->perifericosSeleccionados)->contains('IdPef', $idPef)) {
            $this->perifericosSeleccionados[] = $perif->toArray();

            $this->mostrarPerifericosNoAsignados = collect($this->mostrarPerifericosNoAsignados)
                ->reject(fn($p) => $p->IdPef == $idPef)
                ->values()
                ->all();
        }
    }

    public function quitarPeriferico($idPef)
    {
        // Buscarlo en seleccionados
        $perif = collect($this->perifericosSeleccionados)
            ->firstWhere('IdPef', $idPef);

        if ($perif) {
            // Quitar de seleccionados
            $this->perifericosSeleccionados = collect($this->perifericosSeleccionados)
                ->reject(fn($p) => $p['IdPef'] == $idPef)
                ->values()
                ->all();

            // Volver a añadir a disponibles
            $this->mostrarPerifericosNoAsignados[] = (object) $perif;
        }
    }

    public function render()
    {
        $laboratorios = laboratorio::get();
        if (empty($this->idLab)) {
            $this->mostrarEquipos = equipo::get(); // Todos los equipos
        } else {
            $this->mostrarEquipos = equipo::where('IdLab', $this->idLab)->get(); // Filtrar por laboratorio
        }
        $this->mostrarPerifericos();
        if (empty($this->mostrarPerifericosNoAsignados)) {
            $this->mostrarPerifericosDisponibles();
        }
        $tiposperifericos = tipoperiferico::get();
        return view('livewire.equipos.all-equipos', [
            'equipos' => $this->mostrarEquipos,
            'laboratorios' => $laboratorios,
            'mostrarPerifericos' => $this->mostrarPerifericos(),
            'mostrarPerifericosNoAsignados' => $this->mostrarPerifericosNoAsignados,
            'tiposperifericos' => $tiposperifericos
        ]);
    }
}
