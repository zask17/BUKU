<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Label Harga TnJ 108 - A5 Landscape</title>
    <style>
        @page {
            size: A5 landscape;  /* 210mm lebar × 148mm tinggi */
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #000;
        }

        .page {
            width: 210mm;
            height: 148mm;
            position: relative;
            page-break-after: always;
        }

        .label {
            position: absolute;
            width: 38mm;               /* Lebar label TnJ 108 */
            height: 18mm;              /* Tinggi label TnJ 108 */
            box-sizing: border-box;
            text-align: center;
            overflow: hidden;
            border: 0.3pt dashed #eee; /* garis bantu alignment (hapus jika sudah pas) */
        }

        .label-inner {
            /* padding: 1mm 1.5mm; */
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .label-id {
            font-size: 6pt;
            color: #666;
            margin-bottom: 0.5mm;
        }

        .label-nama {
            font-size: 8pt;
            font-weight: bold;
            line-height: 1.1;
            margin-bottom: 0.8mm;
            height: 2em;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
        }

        .label-harga {
            font-size: 10pt;
            font-weight: bold;
            /* color: #c62828; */
        }

        .label-divider {
            border: none;
            border-top: 0.4pt solid #ccc;
            margin: 0.8mm 0;
            width: 90%;
        }
    </style>
</head>
<body>

@php
    $cols = 5;
    $rows = 8;
    $labelsPerPage = $cols * $rows; // 40

    $itemIndex = 0;
    $totalItems = count($barangs);

    // Hitung berapa halaman dibutuhkan
    $pagesNeeded = ceil(($totalItems + $start_index) / $labelsPerPage);
@endphp

@for ($page = 0; $page < $pagesNeeded; $page++)
    <div class="page">
        @for ($row = 0; $row < $rows; $row++)
            @for ($col = 0; $col < $cols; $col++)
                @php
                    // Posisi lokal di halaman ini (0-based)
                    $pos = ($row * $cols) + $col;

                    // Posisi global di seluruh dokumen
                    $globalPos = $pos + ($page * $labelsPerPage);

                    // Koordinat mm untuk A5 landscape
                    // Margin kiri/atas disesuaikan agar pas di kebanyakan printer
                    $left = 5 + ($col * (38 + 2.5));   // margin kiri 5mm + label 38mm + spacer 2.5mm
                    $top  = 5 + ($row * (18 + 3));     // margin atas 5mm + label 18mm + spacer vertikal 3mm

                    $showContent = ($globalPos >= $start_index) && ($itemIndex < $totalItems);
                @endphp

                <div class="label" style="left: {{ $left }}mm; top: {{ $top }}mm;">
                    @if ($showContent)
                        <div class="label-inner">
                            <div class="label-id">{{ $barangs[$itemIndex]->id_barang }}</div>
                            <div class="label-nama">{{ $barangs[$itemIndex]->nama }}</div>
                            <hr class="label-divider">
                            <div class="label-harga">Rp {{ number_format($barangs[$itemIndex]->harga, 0, ',', '.') }}</div>
                        </div>
                        @php $itemIndex++; @endphp
                    @endif
                </div>
            @endfor
        @endfor
    </div>
@endfor

<script type="text/javascript">
    window.onload = function() {
        window.print();
    };
</script>

</body>
</html>