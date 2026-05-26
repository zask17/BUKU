@extends('layouts.admin.main')

@section('title-page', 'Edit Student')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.student.index') }}">Student</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Student Profile</h4>
                <p class="card-description"> Update department information and academic administration credentials </p>
                
                <form class="forms-sample" id="editStudentForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Full Name (Read-Only)</label>
                            <input type="text" class="form-control form-control-sm border-secondary bg-light" value="{{ $student->user->nama_user ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email address (Read-Only)</label>
                            <input type="email" class="form-control form-control-sm border-secondary bg-light" value="{{ $student->user->email ?? '-' }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>NIM</label>
                            <input type="text" name="NIM" class="form-control form-control-sm border-secondary" value="{{ $student->nim }}" placeholder="e.g. 434241038" required>
                        </div>
                        <div class="col-md-4">
                            <label>Fakultas</label>
                            <input type="text" name="fakultas" class="form-control form-control-sm border-secondary" value="{{ $student->fakultas }}" placeholder="e.g. Vokasi" required>
                        </div>
                        <div class="col-md-4">
                            <label>Prodi</label>
                            <input type="text" name="prodi" class="form-control form-control-sm border-secondary" value="{{ $student->prodi }}" placeholder="e.g. Teknik Informatika" required>
                        </div>
                    </div>

                    <div class="card bg-light border-0 mb-4 shadow-sm text-center">
                        <div class="card-body py-3">
                            <h6 class="text-muted mb-2"><i class="mdi mdi-nfc-variant"></i> Linked NFC Hardware Identity</h6>
                            <div class="form-group text-center mb-0">
                                <input type="text" class="form-control text-center bg-white border-0 font-weight-bold text-success shadow-sm w-50 mx-auto" value="{{ $student->nfcCard->serial_number ?? 'NO CARD LINKED' }}" readonly>
                            </div>
                            <small class="text-muted d-block mt-2">Untuk mengganti mapping kartu NFC baru, disarankan melakukan re-registrasi atau rilis token sekuritas.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.student.index') }}" class="btn btn-sm btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-sm btn-gradient-primary" id="btn-update-student">Update Profile</button>
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
        const submitBtn = document.getElementById('btn-update-student');

        // Form Update Submission migrated to Axios
        $('#editStudentForm').on('submit', function(e) {
            e.preventDefault();
            
            setButtonLoading(submitBtn, 'Updating Data...');

            const payload = new URLSearchParams(new FormData(this));

            axios.post("{{ route('admin.student.update', $student->idstudent) }}", payload)
                .then(function(response) {
                    resetButtonLoading(submitBtn);
                    if(response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Profile Updated',
                            text: 'Student academic records saved into server core.',
                            confirmButtonColor: '#b66dff'
                        }).then(() => {
                            window.location.href = response.data.redirect;
                        });
                    }
                })
                .catch(function(error) {
                    resetButtonLoading(submitBtn);
                    let msg = 'Failed to synchronize data updates.';
                    if(error.response && error.response.data && error.response.data.message) {
                        msg = error.response.data.message;
                    }
                    Swal.fire('Update Failed', msg, 'error');
                });
        });
    });
</script>
@endsection