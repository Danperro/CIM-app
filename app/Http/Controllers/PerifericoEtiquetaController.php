<?php

namespace App\Http\Controllers;

use App\Models\Periferico;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Milon\Barcode\DNS1D;   // ← clase real (no Facade)

class PerifericoEtiquetaController extends Controller
{

    public function single(Periferico $pef)
    {
        [$type, $value] = $this->barcodeFor((string)$pef->CodigoInventarioPef);

        $gen = new DNS1D();

        // escala X ~1.2 y altura ~34 px dan bien en 80x40 mm
        $png = $gen->getBarcodePNG($value, $type, 1.2, 34, [0, 0, 0], false); // ← sin texto dentro
        $pdf = Pdf::loadView('perifericos.etiqueta', [
            'tpf'   => optional($pef->tipoperiferico)->NombreTpf,
            'marca' => $pef->MarcaPef,
            'ubic'  => $pef->CiuPef,
            'value' => $value,
            'png'   => $png,
        ]);

        // Dompdf usa puntos. 1mm = 72/25.4 pt
        $mm = fn($v) => $v * 72 / 25.4;
        $pdf->setPaper([0, 0, $mm(80), $mm(40)], 'portrait');

        return $pdf->stream('etiqueta.pdf');
    }

    public function bulkPdf(Request $request)
    {
        $ids = (array)$request->input('ids', []);
        if (!$ids) return back()->with('error', 'Selecciona al menos un periférico.');

        $gen = new DNS1D();
        $items = Periferico::with('tipoperiferico')
            ->whereIn('IdPef', $ids)
            ->get()
            ->map(function ($p) use ($gen) {
                [$type, $value] = $this->barcodeFor((string)$p->CodigoInventarioPef);
                return [
                    'tpf'   => optional($p->tipoperiferico)->NombreTpf,
                    'marca' => $p->MarcaPef,
                    'ubic'  => $p->CiuPef,
                    'value' => $value,
                    'png'   => $gen->getBarcodePNG($value, $type, 1.2, 34, [0, 0, 0], false),
                ];
            })
            ->values(); // ← reindexa a 0..n-1


        $pdf = Pdf::loadView('perifericos.etiquetas-pdf', ['items' => $items]);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('etiquetas.pdf');
    }


    private function barcodeFor(string $code): array
    {
        // Si quieres 12 de salida, NO uses EAN13
        if (preg_match('/^\d{12}$/', $code)) {
            return ['C128', $code];          // <- fuerza Code 128 para tus inventarios internos
        }

        // Si alguna vez te llega un EAN-13 real, respétalo
        if (preg_match('/^\d{13}$/', $code)) {
            return ['EAN13', $code];
        }

        // Por defecto Code 128
        return ['C128', $code];
    }


    private function ean13WithCheck(string $n12): string
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) $sum += ($i % 2 === 0) ? (int)$n12[$i] : (int)$n12[$i] * 3;
        return $n12 . ((10 - ($sum % 10)) % 10);
    }
}
