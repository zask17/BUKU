@extends('layouts.admin.main')

@section('title-page', 'Absensi')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Scan Kartu NFC</li>
@endsection

@section('content')
<script>
    window.AttendanceConfig = {
        scanUrl: "{{ route('attendance.scan_api') }}",
        csrfToken: "{{ csrf_token() }}"
    };
</script>

<div id="scan-result" style="display: none; margin-bottom: 20px;"></div>

<div class="row">
    <div class="col-md-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title text-start">Scan Kartu NFC</h4>
                <p class="card-description text-start text-muted">Dekatkan kartu NFC mahasiswa ke perangkat, lalu tekan tombol Scan.</p>
                
                <div id="nfc-icon-wrapper" class="py-4 my-3 text-center border rounded bg-light" style="font-size: 80px; transition: all 0.3s;">
                    <i class="mdi mdi-nfc-variant text-primary"></i>
                </div>

                <div class="form-group text-start">
                    <label class="font-weight-bold">Serial Number Kartu</label>
                    <input type="text" class="form-control text-center text-dark" id="serial_number" placeholder="Akan terisi otomatis" readonly>
                </div>

                <div id="status-element" class="text-danger small mb-3 text-center font-weight-bold">Web NFC tidak didukung di browser ini. Gunakan Chrome di Android.</div>

                <button class="btn btn-block btn-lg btn-gradient-primary font-weight-bold w-100 mb-2" id="btn-scan-nfc">
                    <i class="mdi mdi-nfc"></i> Scan NFC
                </button>
                
                <button class="btn btn-block btn-lg btn-gradient-success font-weight-bold w-100" id="btn-submit" disabled>
                    <i class="mdi mdi-check-circle"></i> Catat Absensi
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Log Absensi Hari Ini</h4>
                    <span class="badge bg-gradient-danger text-white rounded-pill font-weight-bold px-3 py-2" id="attendance-count">{{ count($attendances) }}</span>
                </div>

                <div class="text-center py-5 shadow-sm rounded border border-dashed" id="empty-state" style="{{ count($attendances) > 0 ? 'display:none;' : '' }}">
                    <i class="mdi mdi-calendar-blank text-muted" style="font-size: 60px;"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada absensi hari ini.</p>
                    <small class="text-muted">Data akan muncul setelah mahasiswa melakukan scan kartu NFC.</small>
                </div>

                <div class="table-responsive" id="table-wrapper" style="{{ count($attendances) > 0 ? '' : 'display:none;' }}">
                    <table class="table table-striped align-middle text-center">
                        <thead>
                            <tr class="bg-light text-dark">
                                <th> No </th>
                                <th> Nama </th>
                                <th> NIM </th>
                                <th> Waktu </th>
                                <th> Status </th>
                            </tr>
                        </thead>
                        <tbody id="attendance-tbody">
                            @foreach($attendances as $key => $att)
                            <tr>
                                <td> {{ $key + 1 }} </td>
                                <td class="text-start"> {{ $att->student->user->nama_user ?? '-' }} </td>
                                <td> {{ $att->student->nim ?? '-' }} </td>
                                <td> <span class="badge bg-secondary text-dark">{{ \Carbon\Carbon::parse($att->scan_time)->format('H:i:s') }}</span> </td>
                                <td> <span class="badge bg-gradient-success text-white">Hadir</span> </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
(function () {
    const cfg = Object.assign({
        targetInput : '#serial_number',
        scanButton  : '#btn-scan-nfc',
        statusEl    : '#status-element',
        onScan      : null,
        onError     : null,
        autoStop    : true,
    }, window.NFCScannerConfig || {});

    if (!cfg.targetInput || !cfg.scanButton) {
        console.error('[nfc-scanner] targetInput and scanButton are required.');
        return;
    }

    if (!('NDEFReader' in window)) {
        console.warn('[nfc-scanner] Web NFC API is not supported in this browser.');
        setStatus('Web NFC tidak didukung di browser ini. Gunakan Chrome di Android.', 'danger');
        disableButton();
        return;
    } else {
        $('#status-element').removeClass('text-danger').addClass('text-muted').text('Tekan tombol Scan NFC untuk memulai.');
    }

    let reader = null;
    let isScanning = false;
    let abortCtrl = null;

    function getInput() { return document.querySelector(cfg.targetInput); }
    function getButton() { return document.querySelector(cfg.scanButton); }

    function setStatus(message, type) {
        if (!cfg.statusEl) return;
        const el = document.querySelector(cfg.statusEl);
        if (!el) return;

        const typeMap = {
            info    : 'text-info',
            success : 'text-success',
            danger  : 'text-danger',
            warning : 'text-warning',
            muted   : 'text-muted'
        };

        el.className = 'small mb-3 text-center font-weight-bold ' + (typeMap[type] || 'text-muted');
        el.textContent = message;
    }

    function disableButton() {
        const btn = getButton();
        if (!btn) return;
        btn.disabled = true;
        btn.classList.add('btn-secondary');
        btn.classList.remove('btn-gradient-primary');
    }

    function setButtonState(scanning) {
        const btn = getButton();
        if (!btn) return;

        if (scanning) {
            btn.innerHTML = '<i class="mdi mdi-nfc-search-variant"></i> Scanning...';
            btn.disabled = false;
            btn.classList.remove('btn-gradient-primary');
            btn.classList.add('btn-warning');
        } else {
            btn.innerHTML = '<i class="mdi mdi-nfc"></i> Scan NFC';
            btn.disabled = false;
            btn.classList.remove('btn-warning');
            btn.classList.add('btn-gradient-primary');
        }
    }

    async function startScan() {
        if (isScanning) {
            stopScan();
            return;
        }

        try {
            isScanning = true;
            abortCtrl = new AbortController();
            reader = new NDEFReader();

            setButtonState(true);
            setStatus('Mendekatkan kartu NFC...', 'info');

            await reader.scan({ signal: abortCtrl.signal });

            reader.addEventListener('reading', (event) => {
                // Some devices provide `serialNumber`; others may include an NDEF record with id.
                let serial = (event.serialNumber || '').toString();

                if (!serial) {
                    // Try to extract text payload from first NDEF record as fallback
                    try {
                        const records = event.message && event.message.records ? event.message.records : [];
                        if (records.length > 0) {
                            const rec = records[0];
                            // text records typically have TNF = 1 and payload with language code prefix
                            const textDecoder = new TextDecoder(rec.encoding || 'utf-8');
                            // If payload is a DataView/Uint8Array
                            let payload = rec.data || rec.payload || rec;
                            if (payload instanceof ArrayBuffer) payload = new Uint8Array(payload);
                            if (payload && payload.byteLength > 0) {
                                // If it's a typical text record, skip the language byte if present
                                let start = 0;
                                // payload[0] contains status byte for Text record (language length)
                                if (payload[0] && typeof payload[0] === 'number') {
                                    const langLength = payload[0] & 0x3F;
                                    start = 1 + langLength;
                                }
                                const textBytes = payload.slice(start);
                                serial = new TextDecoder().decode(textBytes || payload) || '';
                            }
                        }
                    } catch (ex) {
                        console.warn('Failed to read serial from NDEF record fallback', ex);
                    }
                }

                serial = (serial || '').toUpperCase().replace(/:/g, '');
                const input = getInput();
                if (input) {
                    input.value = serial;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }

                setStatus('Kartu terdeteksi: ' + serial, 'success');
                $('#btn-submit').prop('disabled', false);

                if (typeof cfg.onScan === 'function') { cfg.onScan(serial); }
                if (cfg.autoStop) { stopScan(); }
            });

            reader.addEventListener('readingerror', () => {
                const msg = 'Gagal membaca kartu NFC. Coba lagi.';
                setStatus(msg, 'danger');
                if (typeof cfg.onError === 'function') { cfg.onError(msg); }
                stopScan();
            });

        } catch (err) {
            isScanning = false;
            setButtonState(false);
            let msg = 'Gagal memulai scan NFC.';

            if (err.name === 'NotAllowedError') {
                msg = 'Izin NFC ditolak. Berikan izin NFC di browser.';
            } else if (err.name === 'NotSupportedError') {
                msg = 'NFC tidak tersedia di perangkat ini.';
            } else {
                msg = 'Error: ' + err.message;
            }

            setStatus(msg, 'danger');
            if (typeof cfg.onError === 'function') { cfg.onError(err); }
            console.error('[nfc-scanner]', err);
        }
    }

    function stopScan() {
        if (abortCtrl) {
            abortCtrl.abort();
            abortCtrl = null;
        }
        isScanning = false;
        reader = null;
        setButtonState(false);

        const input = getInput();
        if (input && !input.value) {
            setStatus('Scan dibatalkan.', 'muted');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const btn = getButton();
        if (!btn) return;

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            startScan();
        });
    });
})();

// === LOGIK MUTATION OBSERVER & SUBMIT ABSENSI VIA AXIOS ===
$(document).ready(function() {
    const $scanBtn = $('#btn-scan-nfc');
    const $wrapper = $('#nfc-icon-wrapper');
    const $icon = $wrapper.find('.mdi');
    const submitBtn = document.getElementById('btn-submit');

    const observer = new MutationObserver(function() {
        if ($scanBtn.hasClass('btn-warning')) {
            $wrapper.addClass('bg-inverse-warning text-warning').removeClass('bg-light bg-inverse-success bg-inverse-danger text-primary text-success text-danger');
            $icon.removeClass('mdi-nfc-variant mdi-check-circle mdi-alert-circle').addClass('mdi-nfc-search-variant');
        }
    });
    observer.observe($scanBtn[0], { attributes: true, attributeFilter: ['class'] });

    // Submit Attendance Event handler migrated to Axios
    $('#btn-submit').on('click', function() {
        const serial = $('#serial_number').val().trim();
        if (!serial) return;

        setButtonLoading(submitBtn, 'Menyimpan...');

        axios.post(window.AttendanceConfig.scanUrl, {
            serial_number: serial
        }, {
            headers: {
                'X-CSRF-TOKEN': window.AttendanceConfig.csrfToken
            }
        })
        .then(function(response) {
            resetButtonLoading(submitBtn);
            const res = response.data;
            showResult('success', res.message, res.student, res.nim, res.scan_time);
            appendRow(res.student, res.nim, res.scan_time);
            resetForm();
        })
        .catch(function(error) {
            resetButtonLoading(submitBtn);
            const res = (error.response && error.response.data) ? error.response.data : {};
            const msg = res.message || 'Terjadi kesalahan. Coba lagi.';
            showResult('danger', msg, res.student || null, null, null);
            resetForm();
        });
    });

    function showResult(type, message, studentName, nim, scanTime) {
        const iconMap = {
            success: 'mdi-check-circle',
            danger: 'mdi-alert-circle',
            warning: 'mdi-alert'
        };
        const icon = iconMap[type] || 'mdi-information';
        let extra = '';

        if (studentName) extra += `<br><strong>Mahasiswa:</strong> ${studentName}`;
        if (nim) extra += `&nbsp;&nbsp;<strong>NIM:</strong> ${nim}`;
        if (scanTime) extra += `&nbsp;&nbsp;<strong>Waktu:</strong> ${scanTime}`;

        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show shadow-sm" role="alert">
                <i class="mdi ${icon} me-2"></i> ${message} ${extra}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        $('#scan-result').html(alertHtml).show();

        $wrapper.removeClass('bg-inverse-warning text-warning');
        if (type === 'success') {
            $wrapper.addClass('bg-inverse-success text-success');
            $icon.removeClass('mdi-nfc-search-variant mdi-nfc-variant').addClass('mdi-check-circle');
        } else {
            $wrapper.addClass('bg-inverse-danger text-danger');
            $icon.removeClass('mdi-nfc-search-variant mdi-nfc-variant').addClass('mdi-alert-circle');
        }

        setTimeout(() => {
            $('#scan-result .alert').fadeOut(300, function() {
                $(this).remove();
                $('#scan-result').hide();
            });
        }, 6000);
    }

    function appendRow(name, nim, scanTime) {
        if ($('#empty-state').is(':visible')) {
            $('#empty-state').hide();
            $('#table-wrapper').show();
        }

        const count = $('#attendance-tbody tr').length + 1;
        const row = `
            <tr class="table-success text-dark font-weight-bold">
                <td>${count}</td>
                <td class="text-start">${name ?? '-'}</td>
                <td>${nim ?? '-'}</td>
                <td><span class="badge bg-secondary text-dark">${scanTime ?? '-'}</span></td>
                <td><span class="badge bg-gradient-success text-white">Hadir</span></td>
            </tr>
        `;

        $('#attendance-tbody').prepend(row);
        const current = parseInt($('#attendance-count').text()) || 0;
        $('#attendance-count').text(current + 1);
    }

    function resetForm() {
        $('#serial_number').val('');
        $('#btn-submit').prop('disabled', true).html('<i class="mdi mdi-check-circle"></i> Catat Absensi');
        $('#status-element').removeClass('text-success text-danger text-info').addClass('text-muted').text('Tekan tombol Scan NFC untuk memulai.');
        
        setTimeout(() => {
            $wrapper.removeClass('bg-inverse-success bg-inverse-danger text-success text-danger').addClass('bg-light text-primary');
            $icon.removeClass('mdi-check-circle mdi-alert-circle mdi-nfc-search-variant').addClass('mdi-nfc-variant');
        }, 3000);
    }
});
</script>
@endsection