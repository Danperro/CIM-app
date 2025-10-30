<?php

namespace App\Livewire\Control;

use App\Models\clasemantenimiento;
use App\Models\detalleequipo;
use App\Models\detallelaboratorio;
use App\Models\detallemantenimiento;
use App\Models\equipo;
use App\Models\incidencia;
use App\Models\incidenciaperiferico;
use App\Models\laboratorio;
use App\Models\mantenimiento;
use App\Models\periferico;
use App\Models\tipomantenimiento;
use App\Models\usuario;
use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

class Control extends Component
{

    use WithPagination;

    public $incSoft = [];
    public $incHard = [];
    public $incSel = [];       // checked items: [IdInc => true]
    public $incBloq = [];      // disabled items: [IdInc => true] o ["{IdInc}_{IdPef}" => true]
    public $incStatus = [];    // estado por par: ["{IdInc}_{IdPef}" => EstadoIpf]
    public $incStatusByInc = []; // estado por incidencia: [IdInc => EstadoIpf]
    public $showIncModal = false;
    public $idTpm, $idInc;
    public $idClm;
    public $idMan = [];
    public $idLab;
    public $busquedaEquipo = '';
    public $equiposcombo = [];
    public $mantsoft = [];
    public $manthard = [];
    public $mantenimientosFiltrados = [];
    public $mantenimientoRealizado = false;
    public $idEqo;
    public $observacionDtl, $realizadoDtl, $verificadoDtl, $fechaDtl, $estadoDtl;
    public $modalTitle = '';
    public $modalMessage = '';
    public $modalIcon = 'bi bi-info-circle-fill text-info';
    #[Url('Busqueda')]
    public $query = '';

    public function selectInfo($id)
    {
        $this->idLab = $id;
    }
    public function rules(): array
    {
        $rules = [
            'idLab' => ['required'],
            'idEqo' => ['required'],
            'idTpm' => ['required'],
        ];

        if ($this->idLab && $this->idEqo && $this->idTpm) {
            $rules['idMan'] = ['required', 'array', 'min:1'];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'idLab.required' => 'Seleccione un laboratorio.',
            'idEqo.required' => 'Seleccione un equipo.',
            'idTpm.required' => 'Seleccione un tipo de mantenimiento.',
            'idMan.required' => 'Seleccione al menos una tarea de mantenimiento.',
            'idMan.array' => 'Selección de tareas inválida.',
            'idMan.min' => 'Seleccione al menos una tarea de mantenimiento.',
        ];
    }
    public function updated($property)
    {
        if (in_array($property, ['idLab', 'idEqo', 'idTpm'])) {
            $this->resetErrorBag('idMan');
        }

        $this->validateOnly($property, $this->rules());
    }

    public function getPuedeRegistrarProperty(): bool
    {
        $validator = Validator::make(
            [
                'idLab' => $this->idLab,
                'idEqo' => $this->idEqo,
                'idTpm' => $this->idTpm,
                'idMan' => $this->idMan,
            ],
            $this->rules(),
            $this->messages()
        );

        return $validator->passes();
    }

    public function updatedIdLab()
    {
        $this->reset('idEqo', 'query');
        $this->equiposcombo = equipo::where('IdLab', $this->idLab)
            ->orderByRaw('CAST(SUBSTRING(NombreEqo,3)AS UNSIGNED)ASC')
            ->get();
        $this->observacionDtl = '';
        $this->verificadoDtl = '';
    }
    public function updatedQuery()
    {
        $this->reset('idEqo');

        if (preg_match('/^[0-9]{8,20}$/', trim($this->query ?? ''))) {
            $this->selectByBarcode($this->query);
        }
    }

    public function updatedIdTpm()
    {
        //aaaa
        if ((int)$this->idTpm === 2) {
            // Correctivo: sin preselección
            $this->idMan = [];
        } else {
            // Preventivo (u otros): precargar lo ya hecho hoy
            $this->idMan = $this->obtenerIdsMantenimientosHoy();
        }

        if ($this->idTpm && $this->idClm) {
            $this->mantenimientosFiltrados  = mantenimiento::where('IdTpm', $this->idTpm)
                ->where('IdClm', $this->idClm)
                ->get();
        }
    }
    //se cambio aqui
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

    public function updatedIdEqo()
    {
        //aaaa
        $this->idMan = ((int)$this->idTpm === 2)
            ? []                                   // Correctivo: vacíos
            : $this->obtenerIdsMantenimientosHoy(); // Preventivo: lo de hoy
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
        $this->validateOnly('idMan', $this->rules());
    }



    public function selectEquipoPorBusqueda(string $busqueda)
    {
        $this->query = trim($busqueda);
        $equipos = equipo::query()
            ->when(!empty($this->idLab), fn($q) => $q->where('IdLab', $this->idLab))
            ->search($this->query)
            ->orderBy('NombreEqo')
            ->get();

        $this->equiposcombo = $equipos;

        if ($equipos->count() === 1) {
            $this->idEqo = $equipos->first()->IdEqo;
        } else {
            if ($this->idEqo && !$equipos->pluck('IdEqo')->contains($this->idEqo)) {
                $this->idEqo = null;
            }
        }
    }

    public function selectByBarcode(string $codigo)
    {
        $codigo = trim($codigo);
        if ($codigo === '') return;

        $periferico = periferico::with('equipos')
            ->byInventario($codigo)
            ->first();

        if (!$periferico || $periferico->equipos->isEmpty()) {
            $this->modalTitle   = 'No encontrado';
            $this->modalMessage = "No existe equipo asociado al código: {$codigo}";
            $this->modalIcon    = 'bi bi-exclamation-triangle-fill text-warning';
            $this->dispatch('modal-open');
            return;
        }

        $equipo = $periferico->equipos->first();

        $this->idLab = $equipo->IdLab;
        $this->idEqo = $equipo->IdEqo;
        $this->equiposcombo = equipo::where('IdLab', $this->idLab)
            ->orderBy('NombreEqo')
            ->get(['IdEqo', 'NombreEqo']);

        $this->query = $codigo;
        //aaaa
        $this->idMan = ((int)$this->idTpm === 2)
            ? []                                   // Correctivo
            : $this->obtenerIdsMantenimientosHoy(); // Preventivo



        $this->modalTitle   = 'Equipo encontrado';
        $this->modalMessage = "Se seleccionó: {$equipo->NombreEqo} (Lab: {$equipo->IdLab})";
        $this->modalIcon    = 'bi bi-check-circle-fill text-success';
        $this->dispatch('modal-open');
    }

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

            // Notificaciones consistentes
            $this->dispatch('cerrar-modal', modalId: 'modalObservacion');
            $this->modalTitle = '¡Éxito!';
            $this->modalMessage = 'Mantenimiento registrado correctamente.';
            $this->modalIcon = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } else {
            session()->flash('error', '❌ No se encontró el registro de hoy.');
        }
    }

    public function realizarmantenimiento()
    {
        $this->validate();

        if (!$this->idEqo) {
            $this->modalTitle = 'Falta seleccionar equipo';
            $this->modalMessage = 'Selecciona un equipo antes de realizar el mantenimiento.';
            $this->modalIcon = 'bi bi-exclamation-triangle-fill text-warning';
            $this->dispatch('modal-open');
            return;
        }

        try {
            $registradosHoy = detallemantenimiento::where('IdEqo', $this->idEqo)
                ->whereDate('FechaDtm', now()->toDateString())
                ->pluck('IdMan')
                ->toArray();

            $nuevos       = array_diff($this->idMan, $registradosHoy);
            $desmarcados  = array_diff($registradosHoy, $this->idMan);
            if ((int)$this->idTpm !== 2) {
                if (empty($nuevos) && empty($desmarcados)) {
                    $this->modalTitle   = 'Sin cambios';
                    $this->modalMessage = 'Realice alguna cambio para guardar.';
                    $this->modalIcon    = 'bi bi-exclamation-triangle-fill text-warning';
                    $this->dispatch('modal-open');
                    return;
                }
            }


            // Inserta los nuevos de hoy

            foreach ($nuevos as $id) {
                detallemantenimiento::create([
                    'IdMan'    => $id,
                    'IdEqo'    => $this->idEqo,
                    'FechaDtm' => now(),
                    'EstadoDtm' => true,
                ]);
            }

            // Borra los que desmarcaste hoy
            foreach ($desmarcados as $id) {
                detallemantenimiento::where('IdEqo', $this->idEqo)
                    ->where('IdMan', $id)
                    ->whereDate('FechaDtm', now()->toDateString())
                    ->delete();
            }

            // Sólo para CORRECTIVO (IdTpm = 2): cierra incidencias abiertas relacionadas
            $cerradas = 0;

            if ((int)$this->idTpm === 2) {
                // Cerrar incidencias para las tareas correctivas seleccionadas,
                // hayan sido o no "nuevos" hoy
                $idsParaCerrar = mantenimiento::whereIn('IdMan', $this->idMan)
                    ->where('IdTpm', 2)
                    ->pluck('IdMan')
                    ->all();

                if (!empty($idsParaCerrar)) {
                    DB::beginTransaction();
                    try {
                        $cerradas = $this->cerrarIncidenciasAbiertasPorMantenimientos((int)$this->idEqo, $idsParaCerrar);
                        DB::commit();
                    } catch (\Throwable $e) {
                        DB::rollBack();
                        Log::error('Error al cerrar incidencias tras mantenimiento', ['mensaje' => $e->getMessage()]);
                    }
                }
            } else {
                // NO-correctivo = como ya lo tienes: solo si hay "nuevos"
                if (!empty($nuevos)) {
                    $idsManCorrectivos = mantenimiento::whereIn('IdMan', $nuevos)
                        ->where('IdTpm', 2)
                        ->pluck('IdMan')
                        ->all();
                    if (!empty($idsManCorrectivos)) {
                        DB::beginTransaction();
                        try {
                            $cerradas = $this->cerrarIncidenciasAbiertasPorMantenimientos((int)$this->idEqo, $idsManCorrectivos);
                            DB::commit();
                        } catch (\Throwable $e) {
                            DB::rollBack();
                            Log::error('Error al cerrar incidencias tras mantenimiento', ['mensaje' => $e->getMessage()]);
                        }
                    }
                }
            }
            //tengo que hacer una consulata a las incidencias las cuales
            if (collect($this->mantsoft)->isEmpty() && collect($this->manthard)->isEmpty()) {
                $this->equipo->EstadoEqo = 1;
                $this->equipo->save();

                $lista = detalleequipo::where('IdEqo', $this->idEqo)->pluck('IdPef');

                periferico::whereIn('IdPef', $lista)->update(['EstadoPef' => 1]);
                //los 4 perifericos de la lista los quiero cambiar el estadopef a 1 
            }

            // Verificar si no hay mantenimientos correctivos y cambiar el estado del periférico

            // Garantiza registro en detallelaboratorio (lo tuyo tal cual)
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

            // Reset de UI
            $this->reset('idEqo', 'idTpm', 'idClm', 'idMan', 'idLab', 'query');
            $this->mantenimientosFiltrados = [];
            $this->equiposcombo = [];
            $this->mantsoft = [];
            $this->manthard = [];
            $this->mantenimientoRealizado = true;

            $this->modalTitle = '¡Éxito!';
            $extra = ((int)$this->idTpm === 2) ? " Incidencias cerradas: {$cerradas}." : '';
            $this->modalMessage = 'Mantenimiento registrado correctamente.' . $extra;
            $this->modalIcon = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');
        } catch (\Throwable $e) {
            Log::error('Error al realizar mantenimiento', ['mensaje' => $e->getMessage()]);
            $this->modalTitle = 'Error';
            $this->modalMessage = 'Ocurrió un problema. Intenta más tarde.';
            $this->modalIcon = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        }
    }


    public function abrirModalIncidencias()
    {
        if (!$this->idEqo) {
            $this->modalTitle = 'Falta equipo';
            $this->modalMessage = 'Selecciona un equipo antes de diagnosticar.';
            $this->modalIcon = 'bi bi-exclamation-triangle-fill text-warning';
            $this->dispatch('modal-open');
            return;
        }

        $idEqo = (int) $this->idEqo;

        // 1) Periféricos del equipo
        $idsPefEquipo = detalleequipo::where('IdEqo', $idEqo)->pluck('IdPef');

        // Tipos de periférico presentes en el equipo
        $tiposDelEquipo = periferico::whereIn('IdPef', $idsPefEquipo)
            ->pluck('IdTpf')->unique();

        // 2) Cargar incidencias visibles (solo las que aplican a algún periférico del equipo)
        $this->incSoft = \App\Models\incidencia::whereHas('mantenimiento', fn($q) => $q->where('IdClm', 1))
            ->whereIn('IdTpf', $tiposDelEquipo)
            ->get();

        $this->incHard = \App\Models\incidencia::whereHas('mantenimiento', fn($q) => $q->where('IdClm', 2))
            ->whereIn('IdTpf', $tiposDelEquipo)
            ->get();

        // Subquery: máximos IdIpf por (IdInc, IdPef)
        $ultimos = DB::table('incidenciaperiferico as ipf')
            ->select('ipf.IdInc', 'ipf.IdPef', DB::raw('MAX(ipf.IdIpf) as max_id'))
            ->whereIn('ipf.IdPef', $idsPefEquipo)
            ->groupBy('ipf.IdInc', 'ipf.IdPef');


        // Obtener las filas reales correspondientes a esos máximos (para sacar EstadoIpf)
        $lastRows = DB::table('incidenciaperiferico as ipf')
            ->joinSub($ultimos, 'u', function ($j) {
                $j->on('ipf.IdInc', '=', 'u.IdInc')
                    ->on('ipf.IdPef', '=', 'u.IdPef')
                    ->on('ipf.IdIpf', '=', 'u.max_id'); // <- ahora por IdIpf
            })
            ->whereIn('ipf.IdPef', $idsPefEquipo)
            ->select('ipf.IdInc', 'ipf.IdPef', 'ipf.EstadoIpf')
            ->get();


        // Reiniciamos mapas
        $this->incStatus = [];
        $this->incStatusByInc = [];
        $this->incSel = [];
        $this->incBloq = [];

        foreach ($lastRows as $r) {
            $pairKey = "{$r->IdInc}_{$r->IdPef}";
            $estado = (int) $r->EstadoIpf;
            $this->incStatus[$pairKey] = $estado;

            // Si hay varios periféricos, queremos marcar "Abierta" si AL MENOS uno está abierto.
            // Por eso priorizamos establecer incStatusByInc a 1 si ya es 1.
            if (!isset($this->incStatusByInc[$r->IdInc]) || $this->incStatusByInc[$r->IdInc] !== 1) {
                $this->incStatusByInc[$r->IdInc] = $estado;
            }

            if ($estado === 1) {
                // Abierta → marcada y bloqueada (no editable)
                $this->incSel[$r->IdInc] = true;
                $this->incBloq[$r->IdInc] = true;
                $this->incBloq[$pairKey] = true;
            } else {
                // Cerrada → sin check (aseguramos que no haya selección ni bloqueo)
                unset($this->incSel[$r->IdInc]);
                // no tocamos incBloq aquí (solo se marca bloqueo si hay estado===1)
            }
        }

        $this->showIncModal = true;
        $this->dispatch('mostrar-modal-incidencias');
    }



    public function toggleIncidencia($idInc, $second = null, $third = null)
    {
        // Caso 1: solo se llamó con el id -> toggle server-side
        if (is_null($second) && is_null($third)) {
            if (isset($this->incSel[$idInc])) {
                unset($this->incSel[$idInc]);
            } else {
                $this->incSel[$idInc] = true;
            }
            return;
        }

        // Detectamos si el segundo parámetro es idPef (numérico) o el checked
        $idPef = null;
        $checked = null;

        if (is_numeric($second)) {
            $idPef = $second;
            $checked = $third;
        } else {
            $checked = $second;
        }

        // Si no viene checked (null), actuamos como toggle server-side
        if (is_null($checked)) {
            if (isset($this->incSel[$idInc])) {
                unset($this->incSel[$idInc]);
            } else {
                $this->incSel[$idInc] = true;
            }
            return;
        }

        // Normalizar a booleano (acepta "true"/"false"/1/0)
        $checked = filter_var($checked, FILTER_VALIDATE_BOOLEAN);

        // Ya no comprobamos incBloq aquí. Si necesitas bloqueos, vuelve a añadir la lógica.
        if ($checked) {
            $this->incSel[$idInc] = true;
        } else {
            unset($this->incSel[$idInc]);
        }
    }

    public function registrarIncidencias()
    {
        if (!$this->idEqo) {
            $this->modalTitle = 'Falta equipo';
            $this->modalMessage = 'Selecciona un equipo primero.';
            $this->modalIcon = 'bi bi-exclamation-triangle-fill text-warning';
            $this->dispatch('modal-open');
            return;
        }

        $idsSeleccionados = array_keys($this->incSel);
        if (empty($idsSeleccionados)) {
            $this->modalTitle = 'Nada seleccionado';
            $this->modalMessage = 'Marca al menos una incidencia.';
            $this->modalIcon = 'bi bi-exclamation-triangle-fill text-warning';
            $this->dispatch('modal-open');
            return;
        }

        // No intentes crear las que ya están abiertas
        $idsARegistrar = array_diff($idsSeleccionados, array_keys($this->incBloq));

        if (empty($idsARegistrar)) {
            $this->modalTitle = 'Sin cambios';
            $this->modalMessage = 'Todas las seleccionadas ya están abiertas.';
            $this->modalIcon = 'bi bi-exclamation-triangle-fill text-warning';
            $this->dispatch('modal-open');
            return;
        }

        // Incidencias seleccionadas
        $incidencias = incidencia::whereIn('IdInc', $idsARegistrar)->get()->keyBy('IdInc');

        $idEqo = (int) $this->idEqo;

        // Periféricos del equipo agrupados por tipo
        $perifericosPorTipo = periferico::whereHas('equipos', function ($q) use ($idEqo) {
            $q->where('equipo.IdEqo', $idEqo);
        })
            ->get()
            ->groupBy('IdTpf');

        DB::beginTransaction();
        try {
            $creadas = 0;

            foreach ($idsARegistrar as $idInc) {
                $inc = $incidencias[$idInc] ?? null;
                if (!$inc) continue;

                $idTpf = $inc->IdTpf ?? null;
                if (!$idTpf) continue;

                $lista = $perifericosPorTipo[$idTpf] ?? collect();
                $perif = $lista->first(); // si hay varios del mismo tipo y quieres registrar a todos, itera $lista

                if (!$perif) continue;

                // Si la última para este par (IdInc, IdPef) estuviera abierta, saltar
                $ultimo = incidenciaperiferico::where('IdInc', $inc->IdInc)
                    ->where('IdPef', $perif->IdPef)
                    ->orderByDesc('IdIpf')
                    ->lockForUpdate()
                    ->first();

                if ($ultimo && (int)$ultimo->EstadoIpf === 1) {
                    // ya está abierta; no duplica
                    continue;
                }

                incidenciaperiferico::create([
                    'IdInc'    => $inc->IdInc,
                    'IdPef'    => $perif->IdPef,
                    'FechaIpf' => now(),
                    'EstadoIpf' => 1, // abierta
                ]);

                $perif->update(['EstadoPef' => 0]);
                $creadas++;
            }

            DB::commit();

            $this->modalTitle = 'Diagnóstico registrado';
            $this->modalMessage = "Incidencias registradas: {$creadas}.";
            $this->modalIcon = 'bi bi-check-circle-fill text-success';
            $this->dispatch('modal-open');

            $this->showIncModal = false;
            $this->incSel = [];
            $this->incBloq = [];
            $this->dispatch('cerrar-modal-incidencias');
            // Reset de UI
            $this->reset('idEqo', 'idTpm', 'idClm', 'idMan', 'idLab', 'query');
            $this->mantenimientosFiltrados = [];
            $this->equiposcombo = [];
            $this->mantsoft = [];
            $this->manthard = [];
            $this->mantenimientoRealizado = true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al registrar incidencias', ['e' => $e->getMessage()]);
            $this->modalTitle = 'Error';
            $this->modalMessage = 'Algo se rompió. Intenta de nuevo.';
            $this->modalIcon = 'bi bi-x-circle-fill text-danger';
            $this->dispatch('modal-open');
        }
    }

    private function perifericoTieneIncidenciasAbiertas(int $idPef): bool
    {
        $sub = DB::table('incidenciaperiferico')
            ->select('IdInc', DB::raw('MAX(IdIpf) as max_id'))
            ->where('IdPef', $idPef)
            ->groupBy('IdInc');

        return DB::table('incidenciaperiferico as ipf')
            ->joinSub($sub, 'u', function ($join) {
                $join->on('ipf.IdInc', '=', 'u.IdInc')
                    ->on('ipf.IdIpf', '=', 'u.max_id');
            })
            ->where('ipf.IdPef', $idPef)
            ->where('ipf.EstadoIpf', 1)
            ->exists();
    }

    private function obtenerIncidenciasAbiertasParaPef(int $idPef): array
    {
        $sub = DB::table('incidenciaperiferico')
            ->select('IdInc', DB::raw('MAX(IdIpf) as max_id'))
            ->where('IdPef', $idPef)
            ->groupBy('IdInc');

        return DB::table('incidenciaperiferico as ipf')
            ->joinSub($sub, 'u', function ($j) {
                $j->on('ipf.IdInc', '=', 'u.IdInc')
                    ->on('ipf.IdIpf', '=', 'u.max_id');
            })
            ->where('ipf.IdPef', $idPef)
            ->where('ipf.EstadoIpf', 1)
            ->pluck('ipf.IdInc')
            ->unique()
            ->all();
    }

    private function cerrarIncidenciasAbiertasPorMantenimientos(int $idEqo, array $idsMan): int
    {
        $cerradas = 0;

        DB::beginTransaction();
        try {
            // obtener IdPef del equipo
            $idsPef = detalleequipo::where('IdEqo', $idEqo)->pluck('IdPef');

            if ($idsPef->isEmpty()) {
                DB::commit();
                return 0;
            }

            // Obtener las incidencias que corresponden a los mantenimientos solicitados
            // asumimos relación mantenimiento -> incidencias (hasMany) vía IdMan
            $incidencias = \App\Models\incidencia::whereIn('IdMan', $idsMan)->get();

            foreach ($incidencias as $inc) {
                // para cada incidencia buscamos un periférico del equipo cuyo tipo coincida
                // con el IdTpf de la incidencia
                $periferico = \App\Models\periferico::whereIn('IdPef', $idsPef)
                    ->where('IdTpf', $inc->IdTpf)
                    ->first();

                if (!$periferico) {
                    // no hay periférico de ese tipo para este equipo -> saltar
                    continue;
                }

                $ultimo = incidenciaperiferico::where('IdInc', $inc->IdInc)
                    ->where('IdPef', $periferico->IdPef)
                    ->orderByDesc('IdIpf')
                    ->lockForUpdate()
                    ->first();

                // Solo cerramos si existe y está abierta
                if (!$ultimo || (int)$ultimo->EstadoIpf !== 1) {
                    continue;
                }

                incidenciaperiferico::create([
                    'IdInc'    => $inc->IdInc,
                    'IdPef'    => $periferico->IdPef,
                    'FechaIpf' => now(),
                    'EstadoIpf' => 0,
                ]);

                $cerradas++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error cerrando incidencias por mantenimientos', ['mensaje' => $e->getMessage(), 'IdEqo' => $idEqo, 'idsMan' => $idsMan]);
        }

        return $cerradas;
    }

    private function cerrarIncidenciasParaPef(int $idPef): int
    {
        $cerradas = 0;

        DB::beginTransaction();
        try {
            $incAbiertas = $this->obtenerIncidenciasAbiertasParaPef($idPef);

            foreach ($incAbiertas as $idInc) {
                $ultimo = \App\Models\incidenciaperiferico::where('IdInc', $idInc)
                    ->where('IdPef', $idPef)
                    ->orderByDesc('IdIpf')
                    ->lockForUpdate()
                    ->first();

                if ($ultimo && (int)$ultimo->EstadoIpf === 1) {
                    \App\Models\incidenciaperiferico::create([
                        'IdInc'    => $idInc,
                        'IdPef'    => $idPef,
                        'FechaIpf' => now(),
                        'EstadoIpf' => 0,
                    ]);
                    $cerradas++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error cerrando incidencias para periférico: ' . $e->getMessage(), ['IdPef' => $idPef]);
        }

        return $cerradas;
    }
   
    public function seleccionarTodos($checked)
    {
        if ($checked) {
            $this->idMan = collect([$this->mantsoft, $this->manthard])
                ->flatten(1)
                ->map(function ($item) {
                    if (is_array($item) && isset($item['IdMan'])) return (int) $item['IdMan'];
                    if (is_object($item) && isset($item->IdMan)) return (int) $item->IdMan;
                    return (int) $item;
                })
                ->toArray();
        } else {
            // limpiar selección
            $this->idMan = [];
        }
    }


    public function render()
    {
        $this->mantenimientoRealizado = detallelaboratorio::where('IdLab', $this->idLab)
            ->whereDate('FechaDtl', now()->toDateString())
            ->exists();

        $idEqo = (int) $this->idEqo;

        if (!empty($this->idTpm)) {
            // Caso: tipo de mantenimiento seleccionado

            if ((int)$this->idTpm === 2 && $idEqo) {

                $idsPef = detalleequipo::where('IdEqo', $idEqo)->pluck('IdPef')->all();
                if (!empty($idsPef)) {

                    // Subquery: máximos IdIpf por (IdInc, IdPef) -> devuelve una query builder 
                    $subMax = DB::table('incidenciaperiferico')->select(DB::raw('MAX(IdIpf)'))->whereIn('IdPef', $idsPef)->groupBy('IdInc', 'IdPef'); // Opción A: Mantenciones de SOFTWARE correctivas que tienen alguna incidencia // cuya última fila (por IdInc+IdPef) está en EstadoIpf = 1 
                    $this->mantsoft = mantenimiento::where('IdTpm', 2)
                        // correctivo 
                        ->where('IdClm', 1)
                        // software 
                        ->whereHas('incidencias', function ($q) use ($idsPef, $subMax) {
                            $q->whereHas('incidenciasPeriferico', function ($qq) use ($idsPef, $subMax) {
                                $qq->whereIn('IdPef', $idsPef) // filtramos solo por los IdIpf que son los máximos por (IdInc,IdPef) 
                                    ->whereIn('IdIpf', $subMax)->where('EstadoIpf', 1);
                            });
                        })->distinct()->get(); // Igual para HARDWARE 
                    $this->manthard = mantenimiento::where('IdTpm', 2) // correctivo 
                        ->where('IdClm', 2) // hardware 
                        ->whereHas('incidencias', function ($q) use ($idsPef, $subMax) {
                            $q->whereHas('incidenciasPeriferico', function ($qq) use ($idsPef, $subMax) {
                                $qq->whereIn('IdPef', $idsPef)->whereIn('IdIpf', $subMax)->where('EstadoIpf', 1);
                            });
                        })->distinct()->get();
                }
            } else {
                // Comportamiento normal cuando no es correctivo o aún no hay equipo
                $this->mantsoft = mantenimiento::where('IdClm', 1)
                    ->where('IdTpm', $this->idTpm)
                    ->get();

                $this->manthard = mantenimiento::where('IdClm', 2)
                    ->where('IdTpm', $this->idTpm)
                    ->get();
            }
        } else {
            // No hay tipo de mantenimiento seleccionado: colecciones vacías
            $this->mantsoft = collect();
            $this->manthard = collect();
        }


        $labc = laboratorio::get();
        $tipoman = tipomantenimiento::get();
        $claseman = clasemantenimiento::get();
        $equiposcombo = collect();

        if (!empty($this->idLab)) {
            $q = equipo::where('IdLab', (string)$this->idLab);

            // Si ya hay equipo seleccionado, evita filtrar por query para no perder la opción seleccionada.
            if (empty($this->idEqo) && $this->query !== '') {
                $q->search($this->query);
            }

            $equiposcombo = $q->orderBy('NombreEqo')->get(['IdEqo', 'NombreEqo']);

            // Si hay selección y por algún motivo no está en la lista, añádela para que el <select> pueda marcarla.
            if (!empty($this->idEqo) && !$equiposcombo->pluck('IdEqo')->map(fn($v) => (string)$v)->contains((string)$this->idEqo)) {
                $sel = equipo::where('IdEqo', (string)$this->idEqo)
                    ->where('IdLab', (string)$this->idLab)
                    ->first(['IdEqo', 'NombreEqo']);
                if ($sel) $equiposcombo->push($sel);
            }

            // Autoselección si solo queda uno y no hay seleccionado aún
            if ($equiposcombo->count() === 1 && empty($this->idEqo)) {
                $this->idEqo = (string) $equiposcombo->first()->IdEqo;
            }
        }

        $usuarios = usuario::with(['persona', 'rol'])->where('IdRol', 2)->get();
        $idEqo = (int) $this->idEqo;

        // IdPef del equipo (pivote detalleequipo)
        $idsPefEquipo = DB::table('detalleequipo')
            ->where('IdEqo', $idEqo)
            ->pluck('IdPef');

        $perifericosDelEquipo = periferico::whereIn('IdPef', $idsPefEquipo)
            ->get()
            ->map(function ($p) {
                // asegúrate de exponer lo que necesite la vista
                return (object)[
                    'IdPef' => $p->IdPef,
                    'NombrePef' => $p->NombrePef ?? ("Pef {$p->IdPef}"),
                    'IdTpf' => $p->IdTpf,
                    // opcional: si tienes relaciones o quieres estado abierto
                    'tiene_abierta' => $this->perifericoTieneIncidenciasAbiertas((int)$p->IdPef),
                ];
            });
        $perifericos = periferico::whereIn('IdPef', $idsPefEquipo)->get();

        $perifericos = $perifericos->map(function ($p) {
            $p->tiene_abierta = $this->perifericoTieneIncidenciasAbiertas((int)$p->IdPef);
            return $p;
        });
        // Tipos de periférico presentes en el equipo
        $tiposDelEquipo = Periferico::whereIn('IdPef', $idsPefEquipo)
            ->pluck('IdTpf')->unique();

        $parejasBloqueadas = IncidenciaPeriferico::from('incidenciaperiferico as ipf')
            ->join('detalleequipo as de', 'de.IdPef', '=', 'ipf.IdPef')
            ->where('de.IdEqo', $idEqo)
            ->where('ipf.EstadoIpf', 1)
            ->pluck('ipf.IdPef') // periféricos con incidencia abierta
            ->unique();

        $tiposBloqueados = Periferico::whereIn('IdPef', $parejasBloqueadas)
            ->pluck('IdTpf')->unique();

        $incsof = Incidencia::whereHas('mantenimiento', fn($q) => $q->where('IdClm', 1))
            ->whereIn('IdTpf', $tiposDelEquipo)
            ->whereNotIn('IdTpf', $tiposBloqueados)
            ->get();

        $inchad = Incidencia::whereHas('mantenimiento', fn($q) => $q->where('IdClm', 2))
            ->whereIn('IdTpf', $tiposDelEquipo)
            ->whereNotIn('IdTpf', $tiposBloqueados)
            ->get();

        return view('livewire.Control.Control', [
            'labc' => $labc,
            'tipoman' => $tipoman,
            'claseman' => $claseman,
            'equiposcombo' => $equiposcombo,
            'mansoft' => $this->mantsoft,
            'manhard' => $this->manthard,
            'usuarios' => $usuarios,
            'incsof' => $incsof,
            'inchad' => $inchad,
            'perifericosDelEquipo' => $perifericosDelEquipo, // <- aquí
        ]);
    }
}
