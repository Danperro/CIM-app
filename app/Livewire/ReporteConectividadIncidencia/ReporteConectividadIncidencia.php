<?php

namespace App\Livewire\ReporteConectividadIncidencia;

use App\Models\detallelaboratorio;
use App\Models\detallemantenimiento;
use App\Models\laboratorio;
use App\Models\mantenimiento;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ReporteConectividadIncidencia extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $idLab, $fechaDtl;
    #[Url(as: 'Busqueda', except: '')]  // ?user=daniel  (se quita si está vacío)
    public string $usuario = '';

    public function limpiarfiltros()
    {
        $this->reset([
            'idLab',
            'fechaDtl',
            'usuario'
        ]);
    }
    public function generarPDF($idDtl)
    {
        $detalleLab = \App\Models\detallelaboratorio::with('laboratorio')->findOrFail($idDtl);

        // Normalizamos fecha e ids
        $fecha = \Carbon\Carbon::parse($detalleLab->FechaDtl)->toDateString();
        $idLab = (int) $detalleLab->IdLab;

        // Equipos del laboratorio (PC1..PCN)
        $equipos = \App\Models\equipo::where('IdLab', $idLab)
            ->orderByRaw('CAST(SUBSTRING(NombreEqo, 3) AS UNSIGNED) ASC')
            ->get();

        // Mantenimientos base (de Conectividad e Incidencias) agrupados por clase
        $mantenimientosBase = \App\Models\mantenimiento::with(['clasemantenimiento', 'tipomantenimiento'])
            ->whereHas('tipomantenimiento', fn($q) => $q->where('NombreTpm', 'Conectividad e Incidencias'))
            ->get()
            ->groupBy(fn($m) => $m->clasemantenimiento->NombreClm);

        // === Mapear IdMan -> Estado simbolizado ===
        // IdMan y su tipo de visualización
        $tipoPorIdMan = [
            50 => 'ok_falla',      // Estado de Conectividad -> OK / F
            51 => 'conexion',      // Tipo de Conexión -> C / W
            52 => 'actualizacion', // Estado de Actualizaciones -> A / P
            53 => 'check_x',       // Incidencia de Seguridad Detectadas -> ✓ / X
            54 => 'check_x',       // Antivirus Activo -> ✓ / X
            55 => 'check_x',       // Acción Correctiva -> ✓ / X
        ];

        // IdMan relevantes
        $idsMan = \App\Models\mantenimiento::whereIn('IdMan', array_keys($tipoPorIdMan))->pluck('IdMan')->all();

        // Trae SOLO los detalles del día + lab + esos IdMan
        $detalles = \App\Models\detallemantenimiento::with('equipo:idEqo,IdLab')
            ->whereDate('FechaDtm', $fecha)
            ->whereIn('IdMan', $idsMan)
            ->whereHas('equipo', fn($q) => $q->where('IdLab', $idLab))
            ->get(['IdMan', 'IdEqo', 'EstadoDtm']);

        // Mapa [IdMan][IdEqo] => 0/1
        $mapEstado = [];
        foreach ($detalles as $d) {
            $mapEstado[(int)$d->IdMan][(int)$d->IdEqo] = (int)$d->EstadoDtm;
        }

        // Helper para traducir a símbolo
        $simboloDe = function (int $idMan, ?int $estado) use ($tipoPorIdMan): string {
            if (!array_key_exists($idMan, $tipoPorIdMan) || $estado === null) return '';

            switch ($tipoPorIdMan[$idMan]) {
                case 'ok_falla':
                    return $estado === 1 ? 'OK' : 'FALLA';
                case 'conexion':
                    return $estado === 1 ? 'C'  : 'W';
                case 'actualizacion':
                    return $estado === 1 ? 'A'  : 'P';
                case 'check_x':
                    return $estado === 1 ? '✓'  : 'X';
                default:
                    return '';
            }
        };

        // Construir matriz final: [$clase][$NombreMan][$IdEqo] = símbolo
        $mantenimientos = [];
        foreach ($mantenimientosBase as $clase => $listaMant) {
            foreach ($listaMant as $mant) {
                $mantenimientos[$clase][$mant->NombreMan] = [];
                $idMan = (int) $mant->IdMan;

                foreach ($equipos as $eq) {
                    $idEqo   = (int) $eq->IdEqo;
                    $estado  = $mapEstado[$idMan][$idEqo] ?? null; // 0/1 o null si no hubo registro
                    $symbol  = $simboloDe($idMan, $estado);
                    $mantenimientos[$clase][$mant->NombreMan][$idEqo] = $symbol;
                }
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reporte-conectividad-laboratorio', [
            'detalleLab'     => $detalleLab,
            'equipos'        => $equipos,
            'mantenimientos' => $mantenimientos,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('ReporteMantenimiento_Lab' . $detalleLab->IdLab . '.pdf');
    }




    public function render()
    {
        $laboratorios = laboratorio::get();
        $mostrarreportes = detallelaboratorio::with(['laboratorio'])
            ->whereIn('IdLab', function ($query) {
                $query->select('e.IdLab')
                    ->from('equipo as e')
                    ->whereIn('e.IdEqo', function ($subquery) {
                        $subquery->select('dm.IdEqo')
                            ->from('detallemantenimiento as dm')
                            ->join('mantenimiento as m', 'dm.IdMan', '=', 'm.IdMan')
                            ->where('m.IdTpm', 4);
                    });
            })
            ->search($this->idLab, $this->fechaDtl, $this->usuario)
            ->orderByDesc('FechaDtl')
            ->paginate(10);



        return view('livewire.reporte-conectividad-incidencia.reporte-conectividad-incidencia', [
            'laboratorios' => $laboratorios,
            'dtlab' => $mostrarreportes
        ]);
    }
}
