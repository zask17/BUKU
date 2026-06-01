<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nomor Antrian - {{ $antrian->nomor }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .tiket-card {
            max-width: 480px;
            width: 100%;
            border-radius: 24px;
            border: 3px dashed #198754;
            background: #fff;
        }
        .nomor-antrian {
            font-size: 5rem;
            font-weight: 900;
            color: #1e3c72;
            letter-spacing: 4px;
        }
        .label-poli {
            background: #e8f5e9;
            color: #198754;
            border-radius: 50px;
            padding: 6px 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .btn-cetak {
            background: #1e3c72;
            color: #fff;
            border-radius: 50px;
            padding: 12px 32px;
            font-weight: 700;
        }
        .btn-cetak:hover {
            background: #2a5298;
            color: #fff;
        }
        .watermark-icon {
            font-size: 4rem;
            opacity: 0.1;
            position: absolute;
            right: 20px;
            bottom: 20px;
        }
        @media print {
            .no-print { display: none !important; }
            .tiket-card { border: 2px solid #333; }
        }
    </style>
</head>
<body>
    <div class="position-relative tiket-card p-5 text-center shadow">
        <div class="watermark-icon">🎫</div>

        <div class="mb-3">
            <span class="label-poli">{{ $antrian->nama_poli }}</span>
        </div>

        <h5 class="text-muted text-uppercase small fw-bold tracking-wider mb-1">Nomor Antrian Anda</h5>
        <div class="nomor-antrian my-3">{{ $antrian->nomor }}</div>

        <hr class="my-4" style="border-style: dashed;">

        <h4 class="fw-bold text-dark text-capitalize mb-1">{{ $antrian->nama }}</h4>
        <p class="text-muted small mb-4">
            {{ \Carbon\Carbon::parse($antrian->created_at)->format('d F Y - H:i') }} WIB
        </p>

        <div class="d-flex gap-2 justify-content-center no-print">
            <button class="btn btn-cetak" onclick="window.print()">
                🖨️ Cetak Tiket
            </button>
            <button class="btn btn-outline-secondary rounded-pill px-4" onclick="window.close()">
                Tutup
            </button>
        </div>

        <p class="text-muted small mt-4 mb-0 no-print">
            Simpan nomor ini untuk dipanggil. <br>
            Harap tunggu di ruang tunggu poli terkait.
        </p>
    </div>
</body>
</html>
