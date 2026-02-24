<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Undangan</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 14pt;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 13pt;
        }

        .header p {
            margin: 3px 0;
            font-size: 11pt;
        }

        .kop {
            margin-bottom: 30px;
        }

        table.info {
            width: 100%;
            margin-bottom: 20px;
        }

        table.info td:first-child {
            width: 140px;
            vertical-align: top;
            font-weight: bold;
        }

        .kepada {
            margin: 20px 0 10px;
            font-weight: bold;
        }

        .isi {
            line-height: 1.5;
            text-align: justify;
            margin-bottom: 30px;
        }

        .acara-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .acara-table td {
            padding: 6px 8px;
            border: 1px solid #888;
            vertical-align: top;
        }

        .acara-table td:first-child {
            width: 180px;
            font-weight: bold;
        }

        .ttd {
            text-align: right;
            margin-top: 60px;
        }

        .ttd .nama {
            margin-top: 60px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="kop">
        <div class="header">
            <h1>UNIVERSITAS AIRLANGGA</h1>
            <h2>FAKULTAS VOKASI</h2>
            <p>Kampus B Jl. Dharmawangsa Dalam Surabaya 60286</p>
            <p>Telp. (031) 5033869 Fax (031) 5053156</p>
            <p>Laman: https://vokasi.unair.ac.id | e-mail: info@vokasi.unair.ac.id</p>
        </div>

        <table class="info">
            <tr>
                <td>Nomor</td>
                <td>: {{ $nomor ?? '-' }}</td>
                <td style="text-align:right">{{ $tanggal ?? '-' }}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>: Satu Lembar</td>
                <td></td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: {{ $perihal ?? '-' }}</td>
                <td></td>
            </tr>
        </table>

        <div class="kepada">Yth.</div>
        <ol style="margin-left:20px; padding-left:20px;">
            <li>Para Wakil Dekan</li>
            <li>Para Ketua Departemen</li>
            <li>Para Sekretaris Departemen</li>
            <li>Para Koordinator Program Studi</li>
            <li>Kepala Bagian Tata Usaha</li>
            <li>Para Kepala Subbagian</li>
            <li>Seluruh Dosen</li>
            <li>Seluruh Tenaga Kependidikan</li>
            <li>Fakultas Vokasi Universitas Airlangga</li>
        </ol>

        <div class="isi">
            <p>{{ $isi_konten ?? 'Dalam rangka mempererat tali silaturahmi serta mengawali kegiatan tahun 2026, Fakultas Vokasi Universitas Airlangga akan menyelenggarakan Silaturahmi Awal Tahun Keluarga Besar Fakultas Vokasi.' }}
            </p>
            <p>Sehubungan dengan hal tersebut, kami mengundang Bapak/Ibu untuk hadir pada kegiatan yang akan
                dilaksanakan pada:</p>

            <table class="acara-table">
                <tr>
                    <td>Hari, Tanggal</td>
                    <td>: {{ $tanggal ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>: {{ $acara_waktu ?? '08.00 - selesai WIB' }}</td>
                </tr> <!-- Default jika kosong -->
                <tr>
                    <td>Tempat</td>
                    <td>: {{ $acara_tempat ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Agenda</td>
                    <td>: {{ $acara_agenda ?? '-' }}</td>
                </tr>
            </table>

            <p>Demikian undangan ini kami sampaikan. Atas perhatian dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.
            </p>
        </div>

        <div class="ttd">
            <p>Dekan,</p>
            <p class="nama">{{ $dekan ?? '-' }}</p>
            <p>NIP {{ $nip_dekan ?? '-' }}</p>
        </div>
    </div>
</body>

</html>