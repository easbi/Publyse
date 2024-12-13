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
                                <div class="form-group">
                                    <label for="bagian_pemeriksaan">Bagian Pemeriksaan:</label>
                                    <input type="text" class="form-control" name="bagian_pemeriksaan" required/>
                                </div>
                                <div class="form-group">
                                    <label for="halaman">Halaman:</label>
                                    <input type="text" class="form-control" name="halaman" required/>
                                </div>
                                <div class="form-group">
                                    <label for="hasil_pemeriksaan">Hasil Pemeriksaan:</label>
                                    <textarea class="form-control" name="hasil_pemeriksaan" rows="4" required></textarea>
                                </div>
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
