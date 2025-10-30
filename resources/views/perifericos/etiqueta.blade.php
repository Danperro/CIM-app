<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: 80mm 40mm;
            margin: 3mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .label {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .top {
            font-size: 10pt;
            font-weight: 700;
        }

        .meta {
            font-size: 7pt;
            line-height: 1.1;
        }

        .bar {
            text-align: center;
        }

        .code {
            text-align: center;
            font-size: 9pt;
            letter-spacing: 1px;
        }

        img.barcode {
            width: 52mm;
            height: 18mm;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="label">
        <div class="top">INVENTARIO</div>
        <div class="meta">Tipo: {{ $tpf }} | Marca: {{ $marca }} | CIU: {{ $ubic }}</div>
        <div class="bar">
            <img class="barcode" src="data:image/png;base64,{{ $png }}" alt="barcode">
        </div>
        <div class="code">{{ $value }}</div>
    </div>
</body>

</html>
