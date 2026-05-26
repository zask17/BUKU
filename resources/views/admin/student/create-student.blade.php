@extends('layouts.admin.main')

@section('title-page', 'Add Student')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.student.index') }}">Student</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add Student</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Registration Form</h4>
                <p class="card-description"> Register user credentials along with their physical NFC hardware key </p>
                
                <form class="forms-sample" id="studentForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control form-control-sm border-secondary" placeholder="Enter Full Name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email address</label>
                            <input type="email" name="email" class="form-control form-control-sm border-secondary" placeholder="Enter Email" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control form-control-sm border-secondary" placeholder="Password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-sm border-secondary" placeholder="Confirm Password" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>NIM</label>
                            <input type="text" name="NIM" class="form-control form-control-sm border-secondary" placeholder="e.g. 434241038" required>
                        </div>
                        <div class="col-md-4">
                            <label>Fakultas</label>
                            <input type="text" name="fakultas" class="form-control form-control-sm border-secondary" placeholder="e.g. Vokasi" required>
                        </div>
                        <div class="col-md-4">
                            <label>Prodi</label>
                            <input type="text" name="prodi" class="form-control form-control-sm border-secondary" placeholder="e.g. Teknik Informatika" required>
                        </div>
                    </div>

                    <div class="card bg-light border-0 mb-4 shadow-sm text-center">
                        <div class="card-body">
                            <h5 class="text-primary"><i class="mdi mdi-nfc-variant"></i> NFC Card Binding</h5>
                            <p class="text-muted small">Please activate NFC in Android Settings, press the Scan button, and tap the student badge behind your smartphone device.</p>
                            
                            <div class="my-3">
                                <button type="button" class="btn btn-sm btn-gradient-warning" id="btn-scan-card">
                                    <i class="mdi mdi-scan-helper me-1"></i> Init Registration Scanner
                                </button>
                            </div>
                            <div class="mb-3 text-center">
                                <span id="scanner-state-info" class="badge bg-secondary py-2 px-3 fw-bold text-wrap text-dark">Scanner Engine State: Standby</span>
                            </div>
                            <div class="form-group text-start text-dark">
                                <label class="fw-bold">Serial Number Card UID</label>
                                <input type="text" name="serial_number" id="serial_number" class="form-control text-center bg-white text-primary border-primary font-weight-bold" placeholder="Card token identity will capture dynamically..." readonly required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.student.index') }}" class="btn btn-sm btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-sm btn-gradient-primary" id="btn-save-all">Submit Registration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const $btnScan = $('#btn-scan-card');
        const $stateText = $('#scanner-state-info');
        const $serialInput = $('#serial_number');
        const submitBtn = document.getElementById('btn-save-all');

        $btnScan.on('click', async function() {
            if (!('NDEFReader' in window)) {
                Swal.fire('API Unsupported', 'Web NFC API is not found inside this browser. Please debug via Android Chrome Device.', 'error');
                return;
            }

            try {
                const ndef = new NDEFReader();
                await ndef.scan();

                $stateText.removeClass('bg-secondary bg-success').addClass('bg-warning text-dark').text('Tap physical card on smartphone hardware sensor...');
                $btnScan.prop('disabled', true);

                ndef.addEventListener("reading", ({ serialNumber }) => {
                    const cleanUID = serialNumber.toUpperCase().replace(/:/g, '');
                    $serialInput.val(cleanUID);
                    
                    $stateText.removeClass('bg-warning').addClass('bg-success text-white').text('Token ID Bound successfully!');
                    $btnScan.prop('disabled', false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Card Identity Synced',
                        text: `UID: ${cleanUID} injected successfully into forms.`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                });

            } catch (err) {
                console.error(err);
                $stateText.removeClass('bg-warning').addClass('bg-danger text-white').text('Error initializing API: ' + err.message);
                $btnScan.prop('disabled', false);
            }
        });

        $('#studentForm').on('submit', function(e) {
            e.preventDefault();
            
            setButtonLoading(submitBtn, 'Saving Data...');

            const payload = new URLSearchParams(new FormData(this));

            axios.post("{{ route('admin.student.store') }}", payload)
                .then(function(response) {
                    resetButtonLoading(submitBtn);
                    if(response.data.success) {
                        Swal.fire('Success!', 'Configurations updated successfully.', 'success').then(() => {
                            window.location.href = response.data.redirect;
                        });
                    }
                })
                .catch(function(error) {
                    resetButtonLoading(submitBtn);
                    let msg = 'Failed to compile data profiles.';
                    if(error.response && error.response.data && error.response.data.message) {
                        msg = error.response.data.message;
                    }
                    Swal.fire('Error Handling', msg, 'error');
                });
        });
    });
</script>
@endsection