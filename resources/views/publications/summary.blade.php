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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-sm font-medium text-gray-500">Total Pemeriksaan</h4>
            <p class="text-3xl font-bold mt-1">{{ $overallStats['total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Semua versi</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-sm font-medium text-gray-500">Pemeriksaan Belum Ditindaklanjuti</h4>
            <p class="text-3xl font-bold mt-1 text-red-500">{{ $overallStats['open'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-sm font-medium text-gray-500">Pemeriksaan Selesai Tindaklanjut</h4>
            <p class="text-3xl font-bold mt-1 text-green-500">{{ $overallStats['done'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-sm font-medium text-gray-500">Total Versi Dokumen</h4>
            <p class="text-3xl font-bold mt-1 text-blue-500">{{ $additionalStats['total_versions'] }}</p>
        </div>
    </div>

    <!-- Reviewer Statistics Card -->
    @if(isset($reviewerStats) && $reviewerStats->isNotEmpty())
    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <h4 class="text-md font-semibold mb-4">Statistik Pemeriksa</h4>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @foreach($reviewerStats as $stat)
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">
                                {{ strtoupper(substr($stat['user']->fullname, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900 text-sm">{{ $stat['user']->fullname }}</h5>
                            <p class="text-xs text-gray-600">{{ $stat['user']->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <!-- Total Comments -->
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Pemeriksaan:</span>
                        <span class="font-semibold text-blue-600">{{ $stat['total_comments'] }}</span>
                    </div>

                    <!-- Done vs Open Comments -->
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Selesai ditindaklanjuti:</span>
                        <span class="font-semibold text-green-600">{{ $stat['done_comments'] }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Belum ditindaklanjuti:</span>
                        <span class="font-semibold text-orange-600">{{ $stat['open_comments'] }}</span>
                    </div>

                    <!-- Progress Bar -->
                    @if($stat['total_comments'] > 0)
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-600">Progress Penyelesaian TL :</span>
                            <span class="text-xs font-semibold text-gray-700">{{ $stat['completion_percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full transition-all duration-300"
                                 style="width: {{ $stat['completion_percentage'] }}%"></div>
                        </div>
                    </div>
                    @endif

                    <!-- Latest Activity -->
                    @if($stat['latest_comment_at'])
                    <div class="mt-2 pt-2 border-t border-blue-200">
                        <p class="text-xs text-gray-500">
                            Aktivitas terakhir: {{ $stat['latest_comment_at']->diffForHumans() }}
                        </p>
                    </div>
                    @else
                    <div class="mt-2 pt-2 border-t border-blue-200">
                        <p class="text-xs text-gray-400 italic">Belum ada aktivitas</p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary Stats for Reviewers -->
        @php
            $totalReviewers = $reviewerStats->count();
            $activeReviewers = $reviewerStats->where('total_comments', '>', 0)->count();
            $totalAllComments = $reviewerStats->sum('total_comments');
            $totalDoneComments = $reviewerStats->sum('done_comments');
            $overallCompletion = $totalAllComments > 0 ? round(($totalDoneComments / $totalAllComments) * 100) : 0;
        @endphp

        <div class="pt-4 border-t border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-lg font-bold text-gray-900">{{ $totalReviewers }}</div>
                    <div class="text-xs text-gray-600">Total Pemeriksa</div>
                </div>
                <div class="bg-green-50 rounded-lg p-3">
                    <div class="text-lg font-bold text-green-600">{{ $activeReviewers }}</div>
                    <div class="text-xs text-gray-600">Pemeriksa Aktif</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-3">
                    <div class="text-lg font-bold text-blue-600">{{ $totalAllComments }}</div>
                    <div class="text-xs text-gray-600">Total Komentar</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-3">
                    <div class="text-lg font-bold text-purple-600">{{ $overallCompletion }}%</div>
                    <div class="text-xs text-gray-600">Progress Keseluruhan</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Progress Bar -->
    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <h4 class="text-md font-semibold mb-3">Progres Penyelesaian (Semua Versi)</h4>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-blue-600 h-4 rounded-full text-center text-white text-xs leading-4" style="width: {{ $overallStats['percentage'] }}%">
                {{ $overallStats['percentage'] }}%
            </div>
        </div>
        <p class="text-sm text-gray-600 mt-2">
            {{ $overallStats['done'] }} dari {{ $overallStats['total'] }} komentar telah diselesaikan
        </p>
    </div>

    <!-- Statistik per Versi -->
    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <h4 class="text-md font-semibold mb-4">Statistik per Versi</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($statsByVersion as $version => $stats)
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Versi {{ $version }}
                    </span>
                    <span class="text-sm font-medium">{{ $stats['percentage'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $stats['percentage'] }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-600">
                    <span>Total: {{ $stats['total'] }}</span>
                    <span>Selesai: {{ $stats['done'] }}</span>
                    <span>Terbuka: {{ $stats['open'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Tabel Daftar Komentar -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Detail Pemeriksaan (Semua Versi)</h3>
            <div class="overflow-x-auto">
                <table id="commentsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Versi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hal.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemeriksa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($allComments as $comment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        v{{ $comment->document_version }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $comment->page_number ?: '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $comment->user->fullname }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800">
                                    <div class="max-w-xs truncate" title="{{ $comment->content }}">
                                        {{ Str::limit($comment->content, 100) }}
                                    </div>
                                    @if($comment->replies && $comment->replies->count() > 0)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $comment->replies->count() }} balasan
                                    </div>
                                    @endif
                                </td>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $comment->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Belum ada komentar untuk publikasi ini.
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

/* Custom styling for version badges */
.version-badge {
    background-color: #dbeafe;
    color: #1e40af;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

/* Hover effect for table rows */
tbody tr:hover {
    background-color: #f9fafb;
}

/* Responsive table scroll */
@media (max-width: 768px) {
    .overflow-x-auto {
        overflow-x: scroll;
    }

    table {
        min-width: 800px;
    }
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
        "order": [[ 0, "desc" ], [ 1, "asc" ]], // Sort by version desc, then page asc
        "columnDefs": [
            { "orderable": true, "targets": [0, 1, 2, 4, 5] }, // Version, Page, Reviewer, Status, Date sortable
            { "orderable": false, "targets": [3] }, // Comment content not sortable
            { "type": "num", "targets": [0, 1] } // Version and page as numbers
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
