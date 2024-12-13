@extends('layouts.template')
@section('content')

<!-- Page Header -->
<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Publyse</span>
        <h3 class="page-title">Daftar Publikasi</h3>
    </div>
</div>
<!-- End Page Header -->



<div class="row mb-4">
    <div class="col text-right">
        <a href="{{ route('publikasi.create') }}" class="btn btn-primary btn-sm">
            Tambahkan Publikasi
        </a>
    </div>
</div>


<!-- Default Light Table -->
<div class="row">
    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Tabel Daftar Publikasi</h6>
            </div>
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <i class="fa fa-check mx-2"></i>
                <strong>Success!</strong> {{ $message }}
            </div>
            @endif
            <div class="card-body d-flex flex-column">
                <table id="example" class="display responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Publikasi</th>
                            <th>Batas Upload</th>
                            <th>Waktu Rilis</th>
                            <th>Batas Pemeriksaan</th>
                            <th>Batas Tindak Lanjut</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($publikasi as $lap)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $lap->nama_publikasi }}</td>
                            <td>{{ \Carbon\Carbon::parse($lap->batas_upload)->format('d-F-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($lap->waktu_rilis)->format('d-F-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($lap->batas_pemeriksaan)->format('d-F-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($lap->batas_tl)->format('d-F-Y') }}</td>
                            <td>
                                <form action="{{ route('publikasi.destroy',$lap->id) }}" method="POST">
                                    <a href="#" class="btn btn-info btn-sm btn-show">Show</a>
                                    <a class="btn btn-primary btn-sm" href="{{ route('publikasi.edit',$lap->id) }}">Edit</a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Delete</button>
                                </form>                                 
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End Default Light Table -->

@endsection

@push('scripts')
<script type="text/javascript">
$(document).ready(function() {
    // DataTable setup
    $('#example').DataTable({
        scrollX: true,
        responsive: true,
        searching: false,
        ordering: true,
        paging: true,
        pageLength: 10,
        autoWidth: false
    });

    
});
</script>
@endpush
