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
    <div class="col-lg-12 col-md-12">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Form Inputs</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-0 px-3 pt-3">
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
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <form action="{{ route('pemeriksaan.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="">Publikasi yang akan diperiksa:</label>                                  
                                    <select class="form-control" id="publikasi_id" name="publikasi_id" required>
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($publikasi as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama_publikasi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Pemeriksa:</label>                                  
                                    <select class="form-control" id="pemeriksa_nip" name="pemeriksa_nip" required>
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($users as $item)
                                        <option value="{{ $item->nip }}">{{ $item->fullname }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Loop pertanyaan -->
                                @foreach ($pertanyaan as $index => $p)
                                <div class="form-group row mb-4">
                                    <div class="col-md-6">
                                        <label for="pertanyaan_{{ $p->id }}">{!! $p->rincian !!}</label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-check-inline">
                                            <!-- Set radio button "Sesuai" sebagai default (checked) -->
                                            <input class="form-check-input" type="radio" name="pertanyaan_id[{{ $p->id }}]" value="Yes" id="yes_{{ $p->id }}" onclick="toggleKeterangan({{ $p->id }})" checked>
                                            <label class="form-check-label" for="yes_{{ $p->id }}">Sesuai</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="pertanyaan_id[{{ $p->id }}]" value="No" id="no_{{ $p->id }}" onclick="toggleKeterangan({{ $p->id }})">
                                            <label class="form-check-label" for="no_{{ $p->id }}">Tidak Sesuai</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="keterangan_{{ $p->id }}" style="display:none;">
                                        <label for="keterangan_{{ $p->id }}">Keterangan:</label>
                                        <textarea name="keterangan[{{ $p->id }}]" id="keterangan_text_{{ $p->id }}" class="form-control"></textarea>
                                    </div>
                                </div>

                                <!-- Garis Pemisah -->
                                <hr class="separator-line">
                                @endforeach


                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- End of Content -->

@endsection

@push('scripts')
<script type="text/javascript">
    // Fungsi untuk menampilkan atau menyembunyikan kolom keterangan
    function toggleKeterangan(pertanyaanId) {
        var yesRadio = document.getElementById('yes_' + pertanyaanId);
        var noRadio = document.getElementById('no_' + pertanyaanId);
        var keteranganDiv = document.getElementById('keterangan_' + pertanyaanId);

        // Menampilkan keterangan hanya jika "Tidak Sesuai" dipilih
        if (noRadio.checked) {
            keteranganDiv.style.display = 'block';  // Menampilkan kolom keterangan
        } else {
            keteranganDiv.style.display = 'none';  // Menyembunyikan kolom keterangan
        }
    }
</script>
@endpush
