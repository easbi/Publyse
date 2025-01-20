@extends('layouts.template')

@section('content')
<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Publyse</span>
        <h3 class="page-title">Input Pemeriksaan</h3>
    </div>
</div>

<!-- Content -->
<div class="row">
    <div class="col-12">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Form Inputs</h6>
            </div>
            <div class="card-body p-3">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form action="{{ route('pemeriksaan.storenonkonten') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="publikasi_id">Publikasi yang akan diperiksa:</label>
                        <select class="form-control" id="publikasi_id" name="publikasi_id" required>
                            <option value="" selected disabled>Pilih</option>
                            @foreach($publikasi as $item)
                            <option value="{{ $item->id_publikasi }}">{{ $item->nama_publikasi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pemeriksa_nip">Pemeriksa:</label>
                        <select class="form-control" id="pemeriksa_nip" name="pemeriksa_nip" required>
                            <option value="" selected disabled>Pilih</option>
                            @foreach($users as $item)
                            <option value="{{ $item->nip }}">{{ $item->fullname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tabel Pertanyaan -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Pertanyaan</th>
                                    <th>Hasil Pemeriksaan</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pertanyaan as $index => $p)
                                <tr>
                                    <td>{!! $p->rincian !!}</td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="pertanyaan_id[{{ $p->id }}]" value="Yes" id="yes_{{ $p->id }}" onclick="toggleKeterangan({{ $p->id }})" checked>
                                            <label class="form-check-label" for="yes_{{ $p->id }}">Sesuai</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="pertanyaan_id[{{ $p->id }}]" value="No" id="no_{{ $p->id }}" onclick="toggleKeterangan({{ $p->id }})">
                                            <label class="form-check-label" for="no_{{ $p->id }}">Tidak Sesuai</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div id="keterangan_{{ $p->id }}" class="mt-2" style="display:none;">
                                            <textarea name="keterangan[{{ $p->id }}]" id="keterangan_text_{{ $p->id }}" class="form-control" placeholder="Berikan keterangan jika tidak sesuai"></textarea>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-success">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End of Content -->

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    function toggleKeterangan(pertanyaanId) {
        var noRadio = document.getElementById('no_' + pertanyaanId);
        var keteranganDiv = document.getElementById('keterangan_' + pertanyaanId);

        if (noRadio.checked) {
            keteranganDiv.style.display = 'block';
        } else {
            keteranganDiv.style.display = 'none';
        }
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sukses!',
            text: '{{ session('success') }}'
        });
    @elseif(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Terjadi kesalahan!',
            text: '{{ session('error') }}'
        });
    @endif
</script>
@endpush
