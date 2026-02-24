<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            line-height: 1.3;
        }

        .container {
            width: 90%;
            max-width: 950px;
            text-align: center;
            padding: 20px 40px;
            box-sizing: border-box;
            position: relative;
        }

        /* Border emas */
        .gold-border {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 6px solid gold;
            pointer-events: none;
            border-radius: 12px;
        }

        .title {
            font-size: 38px;
            font-weight: bold;
            color: #4a148c;
            margin: 10px 0;
            letter-spacing: 2px;
        }

        .nomor {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }

        .kepada {
            font-size: 20px;
            margin: 10px 0;
        }

        .nama {
            font-size: 42px;
            font-weight: bold;
            color: #000;
            margin: 10px 0;
            text-transform: uppercase;
        }

        .partisipasi {
            font-size: 18px;
            margin: 10px 0;
        }

        .jabatan {
            font-size: 30px;
            font-weight: bold;
            color: #b71c1c;
            margin: 5px 0 30px;
        }

        .acara {
            font-size: 18px;
            line-height: 1.4;
            margin: 15px 0;
        }

        .ttd-container {
            width: 100%;
            max-width: 900px;           /* Membatasi lebar ttd agar tidak melebar */
            margin: 30px auto 0;        /* Tengah halaman */
        }

        .ttd-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .ttd-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            font-size: 14px;
            /* padding: 0 15px; */
            /* line-height: 1.2; */
        }

        .line {
            border-bottom: 2px solid #000;
            width: 70%;
            margin: 5px auto;
        }

        strong {
            display: block;
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="gold-border"></div>
    <div class="container">
        <div class="title">SERTIFIKAT</div>
        <div class="nomor">{{ $nomor }}</div>

        <div class="kepada">Diberikan kepada :</div>
        <div class="nama">{{ $nama }}</div>

        <div class="partisipasi">Atas Partisipasinya Sebagai:</div>
        <div class="jabatan">{{ $jabatan }}</div>

        <div class="acara">
            Dalam acara:<br>
            <strong>{{ $acara }}</strong><br>
            yang diselenggarakan oleh {{ $penyelenggara }}<br>
            pada {{ $tanggal }}.
        </div>

        <div class="ttd-container">
            <table class="ttd-table">
                <tr>
                    <td>
                        Dekan UNAIR<br>
                        <div class="line"></div>
                        <strong>{{ $dekan }}</strong><br>
                        NIP {{ $nip_dekan }}
                    </td>
                    <td>
                        Koordinator Prodi<br>
                        <div class="line"></div>
                        <strong>{{ $koordinator }}</strong><br>
                        NIK {{ $nik_koor }}
                    </td>
                    <td>
                        Ketua Pelaksana<br>
                        <div class="line"></div>
                        <strong>{{ $ketua }}</strong><br>
                        NIK {{ $nik_ketua }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>