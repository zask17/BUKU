@extends('layouts.admin.main')

@section('title-page', 'Manajemen Customer')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Customer</li>
@endsection

@section('content')
    <div class="container mt-4">
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Data Customer</h2>
            <div>
                <a href="{{ route('admin.customer.create1') }}" class="btn btn-primary">
                    <i class="mdi mdi-plus"></i> Tambah Customer 1 (BLOB)
                </a>
                <a href="{{ route('admin.customer.create2') }}" class="btn btn-success">
                    <i class="mdi mdi-plus"></i> Tambah Customer 2 (File)
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="20%">Nama Customer</th>
                                <th width="10%">Foto</th>
                                <th width="15%">Alamat</th>
                                <th width="15%">Dibuat</th>
                                <th width="15%">Diupdate</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $c)
                                <tr>
                                    <td>{{ $c->idcustomer }}</td>
                                    <td>{{ $c->nama_customer }}</td>
                                    <td>
                                        @if($c->foto_blob)
                                            @php
                                                // Handle hex-encoded blob from PostgreSQL
                                                $blobData = $c->foto_blob;
                                                
                                                // If it starts with hex prefix, convert it
                                                if (is_string($blobData) && strpos($blobData, '\\x') === 0) {
                                                    $blobData = hex2bin(substr($blobData, 2));
                                                } elseif (is_resource($blobData)) {
                                                    $blobData = stream_get_contents($blobData);
                                                }
                                                
                                                $base64Image = base64_encode($blobData);
                                            @endphp
                                            <img src="data:image/png;base64,{{ $base64Image }}"
                                                width="60" height="60" class="rounded cursor-pointer" alt="foto" data-bs-toggle="modal" data-bs-target="#fotoModal{{ $c->idcustomer }}">
                                        @elseif($c->foto_path)
                                            <img src="{{ Storage::url($c->foto_path) }}" width="60" height="60" class="rounded cursor-pointer" alt="foto" data-bs-toggle="modal" data-bs-target="#fotoModal{{ $c->idcustomer }}">
                                        @else
                                            <span class="badge bg-secondary">No Photo</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($c->alamat, 30) }}</td>
                                    <td>{{ $c->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $c->updated_at ? $c->updated_at->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.customer.edit', $c->idcustomer) }}"
                                            class="btn btn-sm btn-warning" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.customer.destroy', $c->idcustomer) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin hapus customer ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="mdi mdi-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal Preview Foto -->
                                <div class="modal fade" id="fotoModal{{ $c->idcustomer }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $c->nama_customer }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                @if($c->foto_blob)
                                                    @php
                                                        // Handle hex-encoded blob from PostgreSQL
                                                        $blobData = $c->foto_blob;
                                                        
                                                        // If it starts with hex prefix, convert it
                                                        if (is_string($blobData) && strpos($blobData, '\\x') === 0) {
                                                            $blobData = hex2bin(substr($blobData, 2));
                                                        } elseif (is_resource($blobData)) {
                                                            $blobData = stream_get_contents($blobData);
                                                        }
                                                        
                                                        $base64Image = base64_encode($blobData);
                                                    @endphp
                                                    <img src="data:image/png;base64,{{ $base64Image }}"
                                                        class="img-fluid rounded" alt="foto">
                                                @elseif($c->foto_path)
                                                    <img src="{{ Storage::url($c->foto_path) }}" class="img-fluid rounded" alt="foto">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="mdi mdi-information-outline"></i> Belum ada data customer
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection