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
            /* border: 0.1pt dashed #eee; */ /* Aktifkan jika ingin melihat garis bantu potong */
        }

        .label-inner {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1mm;
        }

        .label-nama {
            font-size: 7pt;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 0.5mm;
            height: 2.2em;
            overflow: hidden;
            text-transform: uppercase;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .label-harga {
            font-size: 10pt;
            font-weight: bold;
            color: #000; /* Ganti ke red jika ingin warna merah */
        }

        .barcode-container {
            margin: 0.5mm 0;
            transform: scale(0.8); /* Mengecilkan barcode agar muat di label kecil */
            height: 5mm;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .label-id {
            font-size: 6pt;
            color: #555;
            margin-top: 0.2mm;
        }

        /* Menghilangkan margin default dari SVG barcode jika ada */
        .barcode-container svg {
            display: block;
        }
    </style>
</head>
<body>

@php
    $cols = 5;
    $rows = 8;
    $labelsPerPage = $cols * $rows; // 40 label per lembar A5

    $itemIndex = 0;
    $totalItems = count($barangs);

    // Hitung berapa halaman dibutuhkan berdasarkan start index
    $pagesNeeded = ceil(($totalItems + $start_index) / $labelsPerPage);
@endphp

@for ($page = 0; $page < $pagesNeeded; $page++)
    <div class="page">
        @for ($row = 0; $row < $rows; $row++)
            @for ($col = 0; $col < $cols; $col++)
                @php
                    // Posisi lokal halaman ini
                    $pos = ($row * $cols) + $col;

                    // Posisi global seluruh dokumen
                    $globalPos = $pos + ($page * $labelsPerPage);

                    // Koordinat presisi untuk TnJ 108
                    $left = 5 + ($col * (38 + 2.5));   // margin kiri 5mm + label 38mm + jarak horizontal 2.5mm
                    $top  = 5 + ($row * (18 + 0.5));   // margin atas 5mm + label 18mm + jarak vertikal 0.5mm

                    $showContent = ($globalPos >= $start_index) && ($itemIndex < $totalItems);
                @endphp

                <div class="label" style="left: {{ $left }}mm; top: {{ $top }}mm;">
                    @if ($showContent)
                        @php $barang = $barangs[$itemIndex]; @endphp
                        <div class="label-inner">
                            <div class="label-nama">{{ $barang->nama }}</div>
                            <div class="label-harga">Rp {{ number_format($barang->harga, 0, ',', '.') }}</div>
                            
                            <div class="barcode-container">
                                {!! $barang->barcode !!}
                            </div>
                            
                            <div class="label-id">{{ $barang->id_barang }}</div>
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