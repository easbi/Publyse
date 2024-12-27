@extends('layouts.template')
@section('content')

<!-- Page Header -->
<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Publyse</span>
        <h3 class="page-title">Daftar Rule Pemeriksaan</h3>
    </div>
</div>
<!-- End Page Header -->



<div class="row mb-4">
    <div class="col text-right">
        <a href="{{ route('masternonkonten.create') }}" class="btn btn-primary btn-sm">
            Tambahkan Rule
        </a>
    </div>
</div>

<style type="text/css">
    #example td:nth-child(3) {
        font-weight: normal; /* Pastikan teks bold terjaga */
    }

    #example td:nth-child(3) strong {
        font-weight: bold; /* Terapkan gaya bold */
    }

    #example td:nth-child(3) {
        line-height: 1; /* Atur sesuai kebutuhan, semakin kecil nilainya, semakin rapat barisnya */
    }

    #example td:nth-child(3) p {
        margin: 0; /* Menghapus margin default pada tag p */
        padding: 0; /* Menghapus padding jika ada */
    }

    #example td:nth-child(3) br {
        margin: 0; /* Menghapus margin pada elemen br */
    }
    
    /* Atur margin dan padding untuk memastikan bullet points muncul dengan benar */
    #example td:nth-child(3) ul {
        margin: 0;
        padding-left: 20px; /* Berikan indentasi agar bullet lebih rapi */
    }

    #example td:nth-child(3) li {
        list-style-type: disc; /* Menampilkan bullet */
        margin: 5px 0; /* Mengatur jarak antar item */
        padding: 0;
    }

    #example td:nth-child(3) {
        white-space: normal; /* Memastikan teks dapat dibungkus dengan baik */
        line-height: 1.4;
        font-size: 14px;
        word-wrap: break-word; /* Memastikan teks panjang dipecah dengan baik */
    }



</style>

<!-- Default Light Table -->
<div class="row">
    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Tabel Daftar Rule Pemeriksaan</h6>
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
                            <th>Bagian Publikasi</th>
                            <th>Rincian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rulepemeriksaan as $lap)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $lap->bagian_publikasi }}</td>
                            <td>
                                <!-- Render HTML yang ada di kolom rincian -->
                                <div style="white-space: pre-line; word-wrap: break-word; max-width: 800px;">
                                    {!! $lap->rincian !!}
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('masternonkonten.destroy',$lap->id) }}" method="POST">
                                    <a class="btn btn-primary btn-sm" href="{{ route('masternonkonten.edit',$lap->id) }}">Edit</a>
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
    $('#example').DataTable({
        scrollX: true,
        responsive: true,
        searching: false,
        ordering: true,
        paging: true,
        pageLength: 10,
        autoWidth: false,
        columnDefs: [
            {
                targets: 2, // Indeks kolom rincian, pastikan sesuai dengan posisi kolom
                render: function(data, type, row) {
                    // Untuk merender HTML, pastikan type adalah 'display'
                    if (type === 'display') {
                        return data; // Render HTML yang ada di kolom rincian
                    }
                    return data; // Untuk rendering yang lainnya
                }
            }
        ]
    });
});

</script>
@endpush
