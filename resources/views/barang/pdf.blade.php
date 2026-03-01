<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* Spesifikasi Kertas TnJ 108: A4 dengan margin khusus */
        @page {
            size: 210mm 297mm;
            /* Margin luar lembar label */
            margin-top: 13mm;
            margin-bottom: 13mm;
            margin-left: 4.75mm;
            margin-right: 4.75mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .label-grid {
            width: 200.5mm; /* Total lebar area cetak */
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* Tinggi standar satu baris label No. 108 adalah ~33.8mm */
        .label-grid tr {
            height: 33.8mm;
        }

        .col-label {
            width: 38mm; /* Lebar satu stiker */
            height: 33.8mm;
            vertical-align: middle;
            text-align: center;
            padding: 0;
        }

        /* Jarak horizontal antar stiker */
        .col-spacer {
            width: 2.5mm;
            height: 33.8mm;
            padding: 0;
        }

        .label-content {
            width: 38mm;
            height: 24mm; /* Tinggi area stiker yang bisa dilepas */
            margin: auto;
            border: 0.1pt solid #ddd; /* Garis bantu sangat tipis */
            display: table;
            text-align: center;
        }

        .label-inner {
            display: table-cell;
            vertical-align: middle;
            padding: 2mm;
        }

        .label-id {
            font-size: 6pt;
            color: #888;
            margin-bottom: 1mm;
        }

        .label-nama {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 1.5mm;
            word-break: break-word;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .label-divider {
            border: none;
            border-top: 0.5pt solid #ccc;
            margin: 1mm 2mm;
        }

        .label-harga {
            font-size: 11pt;
            font-weight: bold;
            color: #000;
        }

        .label-empty {
            width: 38mm;
            height: 24mm;
        }
    </style>
</head>
<body>
    @php
        // 40 slot kosong (5x8) 
        $cells = array_fill(0, 40, null);
        $idx = $start_index;

        // Isi slot mulai dari koordinat X dan Y yang dipilih
        foreach ($barangs as $barang) {
            if ($idx < 40) {
                $cells[$idx] = $barang;
                $idx++;
            }
        }
        $cell_index = 0;
    @endphp

    <table class="label-grid">
        @for($row = 0; $row < 8; $row++) {{-- 8 Baris  --}}
            <tr>
                @for($col = 0; $col < 5; $col++) {{-- 5 Kolom  --}}
                    <td class="col-label">
                        @if($cells[$cell_index] !== null)
                            <div class="label-content">
                                <div class="label-inner">
                                    <div class="label-id">{{ $cells[$cell_index]->id_barang }}</div>
                                    <div class="label-nama">{{ $cells[$cell_index]->nama }}</div>
                                    <hr class="label-divider">
                                    <div class="label-harga">Rp {{ number_format($cells[$cell_index]->harga, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="label-empty"></div>
                        @endif
                    </td>
                    @php $cell_index++; @endphp

                    {{-- Tambahkan spacer antar kolom stiker kecuali setelah kolom ke-5 --}}
                    @if($col < 4)
                        <td class="col-spacer"></td>
                    @endif
                @endfor
            </tr>
        @endfor
    </table>

    <script type="text/javascript">
        window.onload = function() { window.print(); }
    </script>
</body>
</html>