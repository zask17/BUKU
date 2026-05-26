@extends('layouts.admin.main')

@section('title-page', 'Students')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Student List</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Students Data</h4>
                    <a href="{{ route('admin.student.create') }}" class="btn btn-gradient-primary btn-fw btn-sm">
                        <i class="mdi mdi-account-plus me-1"></i> Add Student
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover" id="studentTable">
                        <thead>
                            <tr class="bg-light">
                                <th> No </th>
                                <th> Name </th>
                                <th> Email </th>
                                <th> NIM </th>
                                <th> Fakultas </th>
                                <th> Prodi </th>
                                <th class="text-center"> Action </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                            <tr>
                                <td> {{ sprintf('%02d', $index + 1) }} </td>
                                <td> {{ $student->user->nama_user ?? '-' }} </td>
                                <td> {{ $student->user->email ?? '-' }} </td>
                                <td> {{ $student->nim }} </td>
                                <td> {{ $student->fakultas }} </td>
                                <td> {{ $student->prodi }} </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.student.edit', $student->idstudent) }}" class="btn btn-inverse-info btn-sm btn-icon-text py-1 px-2">
                                        <i class="mdi mdi-pencil btn-icon-prepend"></i> Edit 
                                    </a>
                                    <button type="button" class="btn btn-inverse-danger btn-sm btn-icon-text py-1 px-2 btn-delete-student" data-id="{{ $student->idstudent }}" data-name="{{ $student->user->nama_user ?? 'Mahasiswa' }}">
                                        <i class="mdi mdi-delete btn-icon-prepend"></i> Delete 
                                    </button>
                                    <form id="delete-form-{{ $student->idstudent }}" action="{{ route('admin.student.delete', $student->idstudent) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No student data found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.btn-delete-student').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Are you sure?',
                text: `You will delete student profile for "${name}". This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#fe7c96',
                cancelButtonColor: '#a3a4a5',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#delete-form-${id}`).submit();
                }
            });
        });
    });
</script>
@endsection