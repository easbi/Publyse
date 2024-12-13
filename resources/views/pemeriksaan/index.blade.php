@extends('layouts.template')
@section('content')

<!-- Page Header -->
<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Publyse</span>
        <h3 class="page-title">Tabel Pemeriksaan</h3>
    </div>
</div>
<!-- End Page Header -->



<div class="row mb-4">
    <div class="col text-right">
        <a href="{{ route('pemeriksaan.create') }}" class="btn btn-primary btn-sm">
            Tambahkan Pemeriksaan
        </a>
    </div>
</div>


<!-- Default Light Table -->
<div class="row">
    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Tabel Pemeriksaan</h6>
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
                            <th>Nama Pemeriksa</th>
                            <th>Bagian Pemeriksaan</th>
                            <th>Halaman</th>
                            <th>Hasil Pemeriksaan</th>
                            <th>Tindak Lanjut</th>
                            <th>Penindak Lanjut</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pemeriksaan as $lap)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $lap->nama_publikasi }}</td>
                            <td>{{ $lap->nama_pemeriksa }}</td>
                            <td>{{ $lap->bagian_pemeriksaan }}</td>
                            <td>{{ $lap->halaman }}</td>
                            <td>{{ $lap->hasil_pemeriksaan }}</td>
                            <td>{{ $lap->is_tindak_lanjut }}</td>
                            <td>{{ $lap->user_nip_pl }}</td>
                            <td>
                                <form action="{{ route('pemeriksaan.destroy',$lap->id) }}" method="POST">
                                    <a href="#" class="btn btn-info btn-sm btn-show">Show</a>
                                    <a class="btn btn-primary btn-sm" href="{{ route('pemeriksaan.edit',$lap->id) }}">Edit</a>
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
