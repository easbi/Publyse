n@extends('layouts.template')

@section('content')

<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Publyse</span>
        <h3 class="page-title">Input Master Pemeriksaan Non Konten</h3>
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
                            <form action="{{ route('masternonkonten.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="kegiatan">Bagian:</label>
                                    <input type="text" class="form-control" name="bagian_publikasi" required/>
                                </div>
                                <div class="form-group">
                                    <label for="rincian">Rincian</label>
                                    <!-- Quill editor as a div -->
                                    <div id="rincian" style="height: 200px;"></div>
                                    <!-- Hidden input for the Quill content -->
                                    <input type="hidden" name="rincian" id="rincian_input">
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

<!-- Add Quill CSS and JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
    // Initialize Quill editor for the "Rincian" field
    var quill = new Quill('#rincian', {
        theme: 'snow', // Theme for the editor
        placeholder: 'Masukkan rincian pemeriksaan sesuai pedoman publikasi 2023', // Placeholder text
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link'],
                [{ 'align': [] }],
                ['clean']
            ]
        }
    });

    // On form submission, capture Quill content and place it in the hidden input
    $('form').submit(function() {
        var rincianContent = quill.root.innerHTML; // Get Quill content
        $('#rincian_input').val(rincianContent); // Assign content to hidden input
    });
</script>

@endsection
