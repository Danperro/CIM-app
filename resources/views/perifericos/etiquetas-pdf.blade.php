<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        /* Ancho útil = 210 - 20 = 190mm */
        table.sheet {
            width: 190mm;
            margin: 0 auto;
            border-collapse: separate;
            /* para usar border-spacing */
            border-spacing: 6mm 6mm;
            /* gap horizontal y vertical */
            table-layout: fixed;
            /* columnas fijas */
        }

        td.col {
            text-align: center;
        }

        .label {
            border: 1px dashed #bbb;
            padding: 3mm;
            height: 32mm;
            /* alto de cada etiqueta */
            box-sizing: border-box;
            page-break-inside: avoid;
            /* no cortar una etiqueta */
        }

        .top {
            font-size: 11pt;
            font-weight: 700;
            margin: 0 0 1mm;
        }

        .meta {
            font-size: 8pt;
            line-height: 1.2;
            margin: 0 0 2mm;
        }

        .code {
            font-size: 9pt;
            letter-spacing: .5px;
        }

        img.barcode {
            display: block;
            width: 100%;
            max-width: 60mm;
            height: 18mm;
            object-fit: contain;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @php
        $perCol = 7; // ← 6 por columna, cámbialo si quieres
    @endphp

    @foreach ($items->values()->chunk($perCol * 2) as $page)
        {{-- 12 por página --}}
        @php
            $left = $page->slice(0, $perCol); // primeras 6
            $right = $page->slice($perCol, $perCol); // siguientes 6
        @endphp

        <table class="sheet">
            <tbody>
                <tr>
                    <td class="col">
                        @foreach ($left as $it)
                            <div class="label">
                                <div class="top">INVENTARIO</div>
                                <div class="meta">Tipo: {{ $it['tpf'] }} | Marca: {{ $it['marca'] }} | CIU:
                                    {{ $it['ubic'] }}</div>
                                <img class="barcode" src="data:image/png;base64,{{ $it['png'] }}" alt="">
                                <div class="code">{{ $it['value'] }}</div>
                            </div>
                        @endforeach
                    </td>

                    <td class="col">
                        @foreach ($right as $it)
                            <div class="label">
                                <div class="top">INVENTARIO</div>
                                <div class="meta">Tipo: {{ $it['tpf'] }} | Marca: {{ $it['marca'] }} | CIU:
                                    {{ $it['ubic'] }}</div>
                                <img class="barcode" src="data:image/png;base64,{{ $it['png'] }}" alt="">
                                <div class="code">{{ $it['value'] }}</div>
                            </div>
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>
