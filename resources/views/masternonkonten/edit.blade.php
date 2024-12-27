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
		<h3 class="page-title">Edit Rule Pemeriksaan</h3>
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
							<form action="{{ route('masternonkonten.update', $masternonkonten->id) }}" method="POST" enctype="multipart/form-data">
								@csrf
								@method('PUT')
								<div class="form-group">
									<label for="kegiatan">Bagian Publikasi:</label>
									<input type="text" class="form-control" name="bagian_publikasi" value="{{$masternonkonten->bagian_publikasi}}" required/>
								</div>
								<div class="form-group">
									<label for="keterangan_kegiatan"><b>Keterangan Pemeriksaan:</b></label>
									<div id="editor-container"></div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />

<script>
	// Inisialisasi Quill
	var quill = new Quill('#editor-container', {
		theme: 'snow',
		placeholder: 'Masukkan keterangan kegiatan...',
	});
	quill.root.innerHTML = {!! json_encode($masternonkonten->rincian) !!};
	$('form').on('submit', function () {
		var content = quill.root.innerHTML;
		$('<input>').attr({
			type: 'hidden',
			name: 'rincian',
			value: content
		}).appendTo(this);
	});
</script>

@endsection
