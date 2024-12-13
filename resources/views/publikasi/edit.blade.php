@extends('layouts.template')

@section('content')
<style>
	.hidden {
		display: none;
	}
</style>

<div class="page-header row no-gutters py-4">
	<div class="col-12 col-sm-4 text-center text-sm-left mb-0">
		<span class="text-uppercase page-subtitle">Publyse</span>
		<h3 class="page-title">Edit Publikasi</h3>
	</div>
</div>

<!-- Content -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card card-small mb-4">
			<div class="card-header border-bottom">
				<h6 class="m-0">Form Edit</h6>
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
							<form action="{{ route('publikasi.update', $publikasi->id) }}" method="POST" enctype="multipart/form-data">
								@csrf
								@method('PUT')
								<div class="form-group">
                                    <label for="kegiatan">Nama Aplikasi:</label>
                                    <input type="text" class="form-control" name="nama_publikasi" value="{{$publikasi->nama_publikasi}}" required/>
                                </div>
                                <div class="form-group">
                                    <label for="batas_upload">Batas Upload</label>
                                    <input type="date" class="form-control form-control-lg mb-3" name="batas_upload" value="{{$publikasi->batas_upload}}" required>
                                </div>
                                <div class="form-group">
                                    <label for="waktu_rilis">Waktu Rilis</label>
                                    <input type="date" class="form-control form-control-lg mb-3" name="waktu_rilis" value="{{$publikasi->waktu_rilis}}" required>
                                </div>
                                <div class="form-group">
                                    <label for="batas_pemeriksaan">Batas Pemeriksaan</label>
                                    <input type="date" class="form-control form-control-lg mb-3" name="batas_pemeriksaan" value="{{$publikasi->batas_pemeriksaan}}" required>
                                </div>
                                <div class="form-group">
                                    <label for="batas_tl">Batas Tindak Lanjut</label>
                                    <input type="date" class="form-control form-control-lg mb-3" name="batas_tl" value="{{$publikasi->batas_tl}}" required>
                                </div>
                                <br>
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
