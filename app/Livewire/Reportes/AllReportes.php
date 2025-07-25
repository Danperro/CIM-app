<?php

namespace App\Livewire\Reportes;

use App\Models\detallelaboratorio;
use App\Models\detallemantenimiento;
use App\Models\laboratorio;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;

class AllReportes extends Component
{
    use WithPagination;
    public $idLab, $mostrarreportes = [], $fechaDtl;
    public function generarPDF($idDtl)
    {
        $detalleLab = \App\Models\detallelaboratorio::with('laboratorio')->findOrFail($idDtl);

        $equipos = \App\Models\equipo::where('IdLab', $detalleLab->IdLab)->get();

        // Trae todos los mantenimientos preventivos con su clase
        $mantenimientosBase = \App\Models\mantenimiento::with(['clasemantenimiento', 'tipomantenimiento'])
            ->whereHas('tipomantenimiento', fn($q) => $q->where('NombreTpm', 'Preventivo'))
            ->get()
            ->groupBy(fn($m) => $m->clasemantenimiento->NombreClm); // Agrupar por 'Hardware' o 'Software'

        // Trae los detalles realizados ese día
        $detallesRealizados = \App\Models\detallemantenimiento::with(['equipo', 'mantenimiento'])
            ->whereDate('FechaDtm', $detalleLab->FechaDtl)
            ->whereHas('equipo', fn($q) => $q->where('IdLab', $detalleLab->IdLab))
            ->get();

        // Prepara estructura final: cruzar mantenimientos base con detalles realizados
        $mantenimientos = [];

        foreach ($mantenimientosBase as $tipo => $listaMant) {
            foreach ($listaMant as $mant) {
                $mantenimientos[$tipo][$mant->NombreMan] = [];

                foreach ($equipos as $eq) {
                    $match = $detallesRealizados->first(
                        fn($d) =>
                        $d->IdEqo == $eq->IdEqo && $d->IdMan == $mant->IdMan
                    );

                    $mantenimientos[$tipo][$mant->NombreMan][$eq->IdEqo] = $match ? '✓' : '';
                }
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reporte-laboratorio', [
            'detalleLab' => $detalleLab,
            'equipos' => $equipos,
            'mantenimientos' => $mantenimientos,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('ReporteMantenimiento_Lab' . $detalleLab->IdLab . '.pdf');
    }




    public function render()
    {
        $laboratorios = laboratorio::get();
        if (empty($this->idLab || $this->fechaDtl)) {
            $this->mostrarreportes = detallelaboratorio::get(); // Todos los equipos
        } else if (empty($this->fechaDtl)) {
            $this->mostrarreportes = detallelaboratorio::where('IdLab', $this->idLab)->get(); // Filtrar por laboratorio
        } else {
            $this->mostrarreportes = detallelaboratorio::where('FechaDtl', $this->fechaDtl)->get(); // Filtrar por laboratorio
        }
        return view('livewire.reportes.all-reportes', [
            'laboratorios' => $laboratorios,
            'dtlab' => $this->mostrarreportes
        ]);
    }
}
