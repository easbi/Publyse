@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Ringkasan Pemeriksaan</h2>
            <p class="text-gray-600">Publikasi: {{ $publication->name }}</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Dashboard</a>
    </div>

    <!-- Kartu Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-sm font-medium text-gray-500">Total Pemeriksaan</h4>
            <p class="text-3xl font-bold mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-sm font-medium text-gray-500">Pemeriksaan Belum Ditindaklanjuti</h4>
            <p class="text-3xl font-bold mt-1 text-red-500">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-sm font-medium text-gray-500">Pemeriksaan Selesai Tindaklanjut</h4>
            <p class="text-3xl font-bold mt-1 text-green-500">{{ $stats['done'] }}</p>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <h4 class="text-md font-semibold mb-3">Progres Penyelesaian</h4>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-blue-600 h-4 rounded-full text-center text-white text-xs leading-4" style="width: {{ $stats['percentage'] }}%">
                {{ $stats['percentage'] }}%
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Komentar -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Detail Pemeriksaan</h3>
            <div class="overflow-x-auto">
                <table id="commentsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hal.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemeriksa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($comments as $comment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $comment->page_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $comment->user->fullname }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800">{{ $comment->content }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($comment->status == 'done')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Terbuka
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Belum ada komentar untuk dokumen ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<!-- Custom CSS -->
<style>
.dataTables_wrapper {
    margin-top: 20px;
}

.dataTables_filter {
    margin-bottom: 20px;
}

.dataTables_filter input {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    margin-left: 8px;
}

.dataTables_filter input:focus {
    outline: none;
    ring: 2px;
    ring-color: #3b82f6;
}

.dataTables_length select {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    margin: 0 8px;
}

.dataTables_length select:focus {
    outline: none;
    ring: 2px;
    ring-color: #3b82f6;
}

.dataTables_info {
    padding-top: 8px;
    color: #6b7280;
}

.dataTables_paginate {
    padding-top: 8px;
}

.dataTables_paginate .paginate_button {
    padding: 8px 12px;
    margin: 0 2px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    text-decoration: none;
    display: inline-block;
    color: #374151;
}

.dataTables_paginate .paginate_button:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
}

.dataTables_paginate .paginate_button.current {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.dataTables_paginate .paginate_button.disabled {
    color: #9ca3af;
    cursor: not-allowed;
}

.dataTables_paginate .paginate_button.disabled:hover {
    background-color: transparent;
    border-color: #d1d5db;
}

table.dataTable thead th {
    border-bottom: 2px solid #e5e7eb;
}

table.dataTable tbody td {
    border-bottom: 1px solid #f3f4f6;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    color: #374151;
}
</style>

<!-- jQuery (pastikan tidak ada conflict dengan yang sudah ada) -->
<script>
if (typeof jQuery === 'undefined') {
    document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
}
</script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
// Pastikan DOM ready
$(document).ready(function() {
    // Check if table exists
    if ($('#commentsTable').length === 0) {
        console.log('Table not found');
        return;
    }

    // Initialize DataTable
    $('#commentsTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "lengthChange": true,
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "order": [[ 0, "asc" ]],
        "columnDefs": [
            { "orderable": false, "targets": 3 }
        ],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            },
            "zeroRecords": "Tidak ada data yang ditemukan",
            "emptyTable": "Tidak ada data tersedia dalam tabel",
            "loadingRecords": "Memuat..."
        }
    });
});
</script>
@endsection
