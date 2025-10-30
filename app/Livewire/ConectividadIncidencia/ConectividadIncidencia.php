<?php

namespace App\Livewire\ConectividadIncidencia;

use App\Models\detallelaboratorio;
use App\Models\equipo;
use App\Models\laboratorio;
use App\Models\mantenimiento;
use App\Models\periferico;
use App\Models\usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class ConectividadIncidencia extends Component
{
    public $conectividad = [];
    public $idLab = '';
    public $idEqo = '';
    public $codigo = '';
    public $query = '';

    // Modal UI
    public $modalTitle = '';
    public $modalMessage = '';
    public $modalIcon = '';

    public $inputs = [
        'estado_conectividad'     => null,    // OK | FALLA
        'tipo_conexion'           => null,    // CABLE | WIFI
        'estado_actualizaciones'  => null,    // ACTUALIZADO | PENDIENTE
        'incidencias_detectadas'  => false,   // bool
        'antivirus_activo'        => false,   // bool
        'accion_correctiva'       => false,   // bool
    ];
    // Dentro de tu clase ConectividadIncidencia

    private function mapeoIdManConectividad(): array
    {
        // IdMan => ['campo' => nombre en inputs, 'tipo' => 'radio_estado'|'radio_conexion'|'radio_ok'|'check']
        return [
            55 => ['campo' => 'accion_correctiva',      'tipo' => 'check'],
            54 => ['campo' => 'antivirus_activo',       'tipo' => 'check'],
            52 => ['campo' => 'estado_actualizaciones', 'tipo' => 'radio_estado'], // ACTUALIZADO/PENDIENTE
            50 => ['campo' => 'estado_conectividad',    'tipo' => 'radio_ok'],     // OK/FALLA
            53 => ['campo' => 'incidencias_detectadas', 'tipo' => 'check'],
            51 => ['campo' => 'tipo_conexion',          'tipo' => 'radio_conexion'], // CABLE/WIFI
        ];
    }

    private function cargarConectividadHoy(): void
    {
        // Reset por defecto (coincide con tu UI)
        $this->inputs = [
            'estado_conectividad'     => null,
            'tipo_conexion'           => null,
            'estado_actualizaciones'  => null,
            'incidencias_detectadas'  => false,
            'antivirus_activo'        => false,
            'accion_correctiva'       => false,
        ];

        if (!$this->idEqo) return;

        $mapeo = $this->mapeoIdManConectividad();
        $idsMan = array_keys($mapeo);

        $hoy = \App\Models\detallemantenimiento::where('IdEqo', $this->idEqo)
            ->whereIn('IdMan', $idsMan)
            ->whereDate('FechaDtm', now()->toDateString())
            ->get()
            ->mapWithKeys(fn($r) => [(int)$r->IdMan => (int)$r->EstadoDtm])
            ->toArray();

        foreach ($mapeo as $idMan => $cfg) {
            $valor = $hoy[$idMan] ?? null;
            $campo = $cfg['campo'];
            switch ($cfg['tipo']) {
                case 'check':
                    $this->inputs[$campo] = ($valor === 1);
                    break;
                case 'radio_estado': // ACTUALIZADO / PENDIENTE
                    $this->inputs[$campo] = $valor === null ? null : ($valor === 1 ? 'ACTUALIZADO' : 'PENDIENTE');
                    break;
                case 'radio_ok': // OK / FALLA
                    $this->inputs[$campo] = $valor === null ? null : ($valor === 1 ? 'OK' : 'FALLA');
                    break;
                case 'radio_conexion': // CABLE / WIFI
                    $this->inputs[$campo] = $valor === null ? null : ($valor === 1 ? 'CABLE' : 'WIFI');
                    break;
            }
        }
    }

    public function updatedIdEqo()
    {
        // Cada vez que cambias el equipo, traes lo de HOY y
        // tus radios/checkboxes quedan "pegados" con wire:model
        $this->cargarConectividadHoy();
        // Opcional: también puedes refrescar la lista:
        $this->MostrarConectividadIncidencia();
    }

    public function limpiar()
    {
        // Solo propiedades que existen y con el nombre correcto
        $this->reset([
            'idLab',
            'idEqo',
            'codigo',
            'query',
            'conectividad'
        ]);

        // Limpia el formulario visual
        $this->inputs = [
            'estado_conectividad'     => null,
            'tipo_conexion'           => null,
            'estado_actualizaciones'  => null,
            'incidencias_detectadas'  => false,
            'antivirus_activo'        => false,
            'accion_correctiva'       => false,
        ];

        // Limpia validaciones
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function MostrarConectividadIncidencia()
    {
        $this->conectividad = mantenimiento::where('IdTpm', 4)
            ->where('IdClm', 2)
            ->where('EstadoMan', 1)
            ->orderBy('NombreMan', 'ASC')
            ->get();
    }
    public function RegistrarConectividadIncidencia()
    {
        if (!$this->idEqo) {
            $this->modalTitle = 'Falta seleccionar equipo';
            $this->modalMessage = 'Selecciona un equipo antes de registrar.';
            $this->modalIcon = 'bi bi-exclamation-triangle-fill text-warning';
            $this->dispatch('modal-open');
            return;
        }

        try {
            // Mapea cada input a su IdMan y valor (1/0)
            // ⚠️ AJUSTA estos IdMan a los de tu base para IdTpm=4
            $mapeo = [
                // IdMan => valor 1/0
                55 => $this->inputs['accion_correctiva'] ? 1 : 0,                         // Acción Correctiva
                54 => $this->inputs['antivirus_activo'] ? 1 : 0,                          // Antivirus Activo
                52 => ($this->inputs['estado_actualizaciones'] === 'ACTUALIZADO') ? 1 : 0, // Estado de Actualizaciones
                50 => ($this->inputs['estado_conectividad'] === 'OK') ? 1 : 0,            // Estado de Conectividad
                53 => $this->inputs['incidencias_detectadas'] ? 1 : 0,                    // Incidencias de Seguridad Detectadas
                51 => ($this->inputs['tipo_conexion'] === 'CABLE') ? 1 : 0,               // Tipo de Conexión
            ];

            $idsMan = array_keys($mapeo);

            // Trae lo registrado hoy para este equipo y estos IdMan
            $registradosHoy = \App\Models\detallemantenimiento::where('IdEqo', $this->idEqo)
                ->whereIn('IdMan', $idsMan)
                ->whereDate('FechaDtm', now()->toDateString())
                ->get()
                ->mapWithKeys(function ($r) {
                    return [(int)$r->IdMan => (int)$r->EstadoDtm];
                })
                ->toArray();

            // ¿Hay cambios?
            $hayCambios = false;
            foreach ($mapeo as $idMan => $valor) {
                $prev = $registradosHoy[$idMan] ?? null;
                if ($prev === null || (int)$prev !== (int)$valor) {
                    $hayCambios = true;
                    break;
                }
            }

            if (!$hayCambios) {
                $this->modalTitle   = 'Sin cambios';
                $this->modalMessage = 'No hay modificaciones respecto a lo ya registrado hoy.';
                $this->modalIcon    = 'bi bi-exclamation-triangle-fill text-warning';
                $this->dispatch('modal-open');
                return;
            }

            // Transacción: BORRAR lo de hoy para cada IdMan y REINSERTAR con el estado actual
            DB::beginTransaction();

            foreach ($mapeo as $idMan => $estado) {
                // Borra el/los registro(s) de HOY de ese IdMan
                \App\Models\detallemantenimiento::where('IdEqo', $this->idEqo)
                    ->where('IdMan', $idMan)
                    ->whereDate('FechaDtm', now()->toDateString())
                    ->delete();

                // Inserta el registro de HOY con el estado actual
                \App\Models\detallemantenimiento::create([
                    'IdMan'     => $idMan,
                    'IdEqo'     => $this->idEqo,
                    'FechaDtm'  => now(),
                    'EstadoDtm' => (int)$estado,
                ]);
            }
            $exdtl = detallelaboratorio::where('IdLab', $this->idLab)
                ->whereDate('FechaDtl', now()->toDateString())
                ->first();

            if (!$exdtl) {
                $usuarioActual = Auth::user();
                $nombreRealizador = $usuarioActual
                    ? trim(($usuarioActual->persona->NombrePer ?? '') . ' ' . ($usuarioActual->persona->ApellidoPaternoPer ?? ''))
                    : ($usuarioActual->UsernameUsa ?? 'SinUsuario');

                $tecnico = usuario::with('persona')->where('IdRol', 2)->first();
                $nombreTecnico = $tecnico
                    ? trim(($tecnico->persona->NombrePer ?? '') . ' ' . ($tecnico->persona->ApellidoPaternoPer ?? ''))
                    : 'SinUsuario';

                detallelaboratorio::create([
                    'IdLab'           => $this->idLab,
                    'ObservaciónDtl'  => '',
                    'RealizadoDtl'    => $nombreRealizador,
                    'VerificadoDtl'   => $nombreTecnico,
                    'FechaDtl'        => now(),
                    'EstadoDtl'       => 1,
                ]);
            }

            DB::commit();

            // Limpia UI (no tocamos incidencias ni observaciones)
            $this->inputs = [
                'estado_conectividad'     => null,
                'tipo_conexion'           => null,
                'estado_actualizaciones'  => null,
                'incidencias_detectadas'  => false,
                'antivirus_activo'        => false,
                'accion_correctiva'       => false,
            ];

            // Modal de éxito
            $this->modalTitle = '¡Éxito!';
            $this->modalMessage = 'Conectividad e incidencias de red registradas/actualizadas para hoy.';
            $this->modalIcon = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
            $this->limpiar();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al registrar conectividad/incidencia', [
                'mensaje' => $e->getMessage(),
            ]);

            $this->modalTitle = 'Error';
            $this->modalMessage = 'Ocurrió un problema al registrar.';
            $this->modalIcon = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        }
    }

    #[On('barcode-scanned')]
    public function handleBarcode($payload): void
    {
        $raw  = is_array($payload) ? ($payload['codigo'] ?? '') : (string)$payload;
        $code = preg_replace('/\D+/', '', $raw);
        if ($code === '') return;

        static $last = null;            // antirebote
        if ($last === $code) return;
        $last = $code;

        $this->selectByBarcode($code);
    }

    /**
     * Escoge lab/equipo a partir del código del periférico,
     * carga estados de hoy y refresca la lista de mantenimientos.
     */

    public function selectByBarcode(string $codigo): void
    {
        $codigo = trim($codigo);
        if ($codigo === '') return;

        // (opcional) mostrar el valor en tu input
        $this->codigo = $codigo;

        // OJO: agrupa el orWhere con una closure para mantener la precedencia
        $periferico = periferico::with('equipos')
            ->where(function ($q) use ($codigo) {
                $q->where('CodigoInventarioPef', $codigo)
                    ->orWhere('CiuPef', $codigo);
            })
            ->first();

        if (!$periferico || $periferico->equipos->isEmpty()) {
            $this->modalTitle   = 'No encontrado';
            $this->modalMessage = "No existe equipo asociado al código: {$codigo}";
            $this->modalIcon    = 'bi bi-exclamation-triangle-fill text-warning';
            $this->dispatch('modal-open');
            return;
        }

        $equipo       = $periferico->equipos->first();
        $this->idLab  = $equipo->IdLab;   // esto hará que tu <select> de lab se marque
        // y este el de equipo
        // Dispara un evento para el siguiente render y recién ahí fija idEqo

        $this->idEqo = $equipo->IdEqo;

        // Cargar radios/checkboxes con lo de HOY y refrescar la tabla
        $this->cargarConectividadHoy();
        $this->MostrarConectividadIncidencia();

        // Cierra el modal de cámara (si estaba abierto)
        $this->dispatch('cerrar-modal', modalId: 'scannerModal');
        // Aviso (opcional)
        $this->modalTitle   = 'Equipo encontrado';
        $this->modalMessage = "Se seleccionó: {$equipo->NombreEqo} (Lab: {$equipo->IdLab})";
        $this->modalIcon    = 'bi bi-check-circle-fill text-success';
        $this->dispatch('modal-open');
    }

    public function render()
    {
        $laboratorios = laboratorio::orderBy('NombreLab')->get();
        $equipos = $this->idLab
            ? equipo::where('IdLab', $this->idLab)->orderByRaw('CAST(SUBSTRING(NombreEqo,3) AS UNSIGNED) ASC')->get()
            : collect();

        return view('livewire.conectividad-incidencia.conectividad-incidencia', [
            'laboratorios' => $laboratorios,
            'equipos' => $equipos,
            'conectividad' => $this->conectividad
        ]);
    }
}
