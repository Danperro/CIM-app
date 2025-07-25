<?php

namespace App\Livewire\Control;

use App\Models\clasemantenimiento;
use App\Models\detallelaboratorio;
use App\Models\detallemantenimiento;
use App\Models\equipo;
use App\Models\laboratorio;
use App\Models\mantenimiento;
use App\Models\tipomantenimiento;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Control extends Component
{

    use WithPagination;
    public $idTpm;  // tipo de mantenimiento
    public $idClm;  // clase de mantenimiento
    public $idMan = [];
    public $idLab;
    public $busquedaEquipo = '';
    public $equiposcombo = [];
    public $mantsoft = [];
    public $manthard = [];
    public $mantenimientosFiltrados = [];
    public $seleccionesPorClase = [];
    public $mantenimientoRealizado = false;
    public $idEqo;
    public $equipoSeleccionado = false;
    public $observacionDtl, $realizadoDtl, $verificadoDtl, $fechaDtl, $estadoDtl;
    public function selectInfo($id)
    {
        $this->idLab = $id;
    }
    public function updatedIdLab()
    {
        $this->equiposcombo = equipo::where('IdLab', $this->idLab)->get();
        $this->observacionDtl = '';
        $this->verificadoDtl = '';
    }
    public function updatedIdTpm()
    {
        if ($this->idTpm && $this->idClm) {
            $this->mantenimientosFiltrados  = mantenimiento::where('IdTpm', $this->idTpm)
                ->where('IdClm', $this->idClm)
                ->get();
        }
    }
    public function updatedBusquedaEquipo()
    {
        $this->busquedaequipo();
    }
    public function updatedIdEqo()
    {
        $this->idMan = $this->obtenerIdsMantenimientosHoy();
    }

    public function obtenerIdsMantenimientosHoy()
    {
        if ($this->idEqo) {
            return detallemantenimiento::where('IdEqo', $this->idEqo)
                ->whereDate('FechaDtm', now()->toDateString())
                ->pluck('IdMan')
                ->toArray();
        }
        return [];
    }

    public function busquedaequipo()
    {
        if (!empty($this->idLab)) {
            if (!empty($this->busquedaEquipo)) {
                $this->equiposcombo = equipo::where('NombreEqo', 'like', '%' . $this->busquedaEquipo . '%')
                    ->where('IdLab', $this->idLab)
                    ->get();
            } else {
                // Mostrar todos los equipos del laboratorio seleccionado
                $this->equiposcombo = equipo::where('IdLab', $this->idLab)->get();
            }
        } else {
            // Si no hay laboratorio seleccionado, vacía el combo
            $this->equiposcombo = [];
        }
    }


    public function actualizarSeleccion($idMantenimiento, $checked)
    {
        if ($checked) {
            if (!in_array($idMantenimiento, $this->idMan)) {
                $this->idMan[] = $idMantenimiento;
            }
        } else {
            $this->idMan = array_filter($this->idMan, function ($id) use ($idMantenimiento) {
                return $id != $idMantenimiento;
            });
        }
    }


    public function realizarmantenimiento()
    {
        if (!$this->idEqo) {
            session()->flash('error', 'Falto seleccionar un equipo');
            return;
        }

        try {
            // Obtiene los IDs de mantenimientos ya registrados hoy para este equipo
            $registradosHoy = detallemantenimiento::where('IdEqo', $this->idEqo)
                ->whereDate('FechaDtm', now()->toDateString())
                ->pluck('IdMan')
                ->toArray();

            // Solo registrar los nuevos que no estén duplicados
            $nuevos = array_diff($this->idMan, $registradosHoy);
            $desmarcados = array_diff($registradosHoy, $this->idMan);
            if (empty($nuevos) && empty($desmarcados)) {
                session()->flash('error', '⚠️ No hubo cambios en el mantenimiento.');
                return;
            }
            foreach ($nuevos as $id) {
                detallemantenimiento::create([
                    'IdMan' => $id,
                    'IdEqo' => $this->idEqo,
                    'FechaDtm' => now(),
                    'EstadoDtm' => true
                ]);
            }
            foreach ($desmarcados as $id) {
                detallemantenimiento::where('IdEqo', $this->idEqo)
                    ->where('IdMan', $id)
                    ->whereDate('FechaDtm', now()->toDateString())
                    ->delete();
            }

            $exdtl = detallelaboratorio::where('IdLab', $this->idLab)->whereDate('FechaDtl', now()->toDateString())->first();
            if (!$exdtl) {
                //crea el detallelaboratorio
                detallelaboratorio::create([
                    'IdLab' => $this->idLab,
                    'ObservaciónDtl' => '',
                    'RealizadoDtl' => 'SinUsuario', // ← simulación temporal
                    'VerificadoDtl' => '',          // ← se llenará manualmente después
                    'FechaDtl' => now(),
                    'EstadoDtl' => 1
                ]);
            }

            //si no hay un detalle creado en esta fecha actual y en este laboratorio 
            //entonces se crea con la fecha actual y el id lab
            //los datos que hay el idlab, la obsevacion quedaria vacia, el realizado se sacararia  del id del ususario
            //que inicio secion, el verificado queda vacio y la fecha seria la actual
            // 🔥 Resetear combos y listas
            $this->reset('idEqo', 'idTpm', 'idClm', 'idMan', 'idLab');
            $this->mantenimientosFiltrados = [];
            $this->equiposcombo = [];
            $this->mantsoft = [];
            $this->manthard = [];

            // 🔓 Habilitar botón de observaciones
            $this->mantenimientoRealizado = true;

            session()->flash('mensaje', '✅ Mantenimiento registrado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al realizar mantenimiento', ['mensaje' => $e->getMessage()]);
            $this->dispatch('toast-danger', message: 'Error. Comuníquese con soporte.');
        }
    }
    //observaciones en observaciones lo primero quiero buscar si en detallelaboratorio
    //hay una fecha y laboratorio si existe solo se hace un update y si no que se
    //cree un detalle laboratorio  

    public function mostrarObservacion()
    {
        $detalle = detallelaboratorio::where('IdLab', $this->idLab)
            ->whereDate('FechaDtl', now()->toDateString())
            ->first();

        if ($detalle) {
            $this->observacionDtl = $detalle->ObservaciónDtl;
            $this->verificadoDtl = $detalle->VerificadoDtl;
            $this->fechaDtl = $detalle->FechaDtl;
            $this->estadoDtl = $detalle->EstadoDtl;
        } else {
            $this->observacionDtl = '';
            $this->verificadoDtl = '';
        }
    }

    public function guardarObservacion()
    {
        $detalle = detallelaboratorio::where('IdLab', $this->idLab)
            ->whereDate('FechaDtl', now()->toDateString())
            ->first();

        if ($detalle) {
            $detalle->ObservaciónDtl = $this->observacionDtl;
            $detalle->VerificadoDtl = $this->verificadoDtl;
            $detalle->save();

            session()->flash('mensaje', '✅ Observación guardada correctamente.');
            $this->dispatch('toast-success');
        } else {
            session()->flash('error', '❌ No se encontró el registro de hoy.');
        }
    }

    public function render()
    {
        $this->mantenimientoRealizado = detallelaboratorio::where('IdLab', $this->idLab)
            ->whereDate('FechaDtl', now()->toDateString())
            ->exists();

        if (!empty($this->idTpm)) {
            $this->mantsoft = mantenimiento::where('IdClm', 1)->where('IdTpm', $this->idTpm)->get();
            $this->manthard = mantenimiento::where('IdClm', 2)->where('IdTpm', $this->idTpm)->get();
        }

        $labc = laboratorio::get();
        $tipoman = tipomantenimiento::get();
        $claseman = clasemantenimiento::get();
        return view('livewire.Control.Control', [
            'labc' => $labc,
            'tipoman' => $tipoman,
            'claseman' => $claseman,
            'equiposcombo' => $this->equiposcombo,
            'mansoft' => $this->mantsoft,
            'manhard' => $this->manthard
        ]);
    }
}
