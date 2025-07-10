@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 dark:bg-green-800/20 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Admin indicator --}}
            @if(isset($isAdmin) && $isAdmin)
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                    <strong>Mode Administrator:</strong> Anda dapat melihat semua publikasi dan detail pemeriksa.
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Kolom: Publikasi yang Saya Buat / Semua Publikasi (Admin) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Header kartu dibuat flex -->
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">
                                @if(isset($isAdmin) && $isAdmin)
                                    Semua Publikasi (Admin View)
                                @else
                                    Publikasi yang Saya Buat
                                @endif
                            </h3>
                            <a href="{{ route('publications.create') }}" class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-md hover:bg-green-600 flex-shrink-0">+ Tambah</a>
                        </div>
                        <div class="space-y-4">
                            @forelse ($myCreations as $publication)
                                <div class="p-4 border rounded-lg dark:border-gray-700">
                                    <p class="font-semibold">{{ $publication->name }}</p>

                                    {{-- Tampilan untuk Admin dengan tooltip --}}
                                    @if(isset($isAdmin) && $isAdmin)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Dibuat oleh: {{ $publication->creator->fullname ?? 'N/A' }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Jumlah Pemeriksa:
                                            <span class="tooltip-container">
                                                <span class="relative inline-block cursor-help font-medium text-blue-600 hover:text-blue-800 underline"
                                                      data-tooltip="tooltip-{{ $publication->id }}"
                                                      onmouseover="showTooltip('tooltip-{{ $publication->id }}')"
                                                      onmouseout="hideTooltip('tooltip-{{ $publication->id }}')">
                                                    {{ $publication->reviewers->count() }}
                                                </span>

                                                {{-- Tooltip content --}}
                                                <div id="tooltip-{{ $publication->id }}" class="tooltip-content">
                                                    @if($publication->reviewers->count() > 0)
                                                        <div style="font-weight: bold; margin-bottom: 4px;">Daftar Pemeriksa:</div>
                                                        @foreach($publication->reviewers as $reviewer)
                                                            <div style="padding: 2px 0;">• {{ $reviewer->fullname ?? $reviewer->name ?? 'Nama tidak tersedia' }}</div>
                                                        @endforeach
                                                    @else
                                                        <div>Belum ada pemeriksa yang ditugaskan</div>
                                                    @endif
                                                    <div class="tooltip-arrow"></div>
                                                </div>
                                            </span>
                                        </p>
                                    @else
                                        {{-- Tampilan untuk User biasa --}}
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Jumlah Pemeriksa: {{ $publication->reviewers->count() }}
                                        </p>
                                    @endif

                                    <!-- Tombol dibungkus dengan div yang bisa wrap -->
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @can('view-publication', $publication)
                                            <a href="{{ route('publications.summary', $publication) }}" class="px-3 py-1 bg-gray-500 text-white text-xs rounded-md hover:bg-gray-600">Ringkasan</a>
                                        @endcan
                                        @can('manage-publication', $publication)
                                            <a href="{{ route('publications.assign.form', $publication) }}" class="px-3 py-1 bg-yellow-500 text-white text-xs rounded-md hover:bg-yellow-600">Tugaskan</a>
                                            <a href="{{ route('publications.edit', $publication) }}" class="px-3 py-1 bg-slate-500 text-white text-xs rounded-md hover:bg-slate-600">Edit</a>
                                            <form action="{{ route('publications.destroy', $publication) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus publikasi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded-md hover:bg-red-700">Hapus</button>
                                            </form>
                                        @endcan
                                        @can('view-publication', $publication)
                                            <a href="{{ route('publications.show', $publication) }}" class="px-3 py-1 bg-blue-500 text-white text-xs rounded-md hover:bg-blue-600">Review</a>
                                        @endcan
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">
                                    @if(isset($isAdmin) && $isAdmin)
                                        Belum ada publikasi dalam sistem.
                                    @else
                                        Anda belum membuat publikasi apapun.
                                    @endif
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Kolom: Tugas Pemeriksaan Saya / Semua Tugas (Admin) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">
                            @if(isset($isAdmin) && $isAdmin)
                                Semua Publikasi untuk Pemeriksaan (Admin View)
                            @else
                                Tugas Pemeriksaan Saya
                            @endif
                        </h3>
                        <div class="space-y-4">
                            @forelse ($myAssignments as $publication)
                                <div class="p-4 border rounded-lg dark:border-gray-700">
                                    <p class="font-semibold">{{ $publication->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Dibuat oleh: {{ $publication->creator->fullname ?? 'N/A' }}
                                    </p>

                                    {{-- Tampilan jumlah pemeriksa untuk Admin dengan tooltip --}}
                                    @if(isset($isAdmin) && $isAdmin)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Jumlah Pemeriksa:
                                            <span class="tooltip-container">
                                                <span class="relative inline-block cursor-help font-medium text-blue-600 hover:text-blue-800 underline"
                                                      data-tooltip="tooltip-assign-{{ $publication->id }}"
                                                      onmouseover="showTooltip('tooltip-assign-{{ $publication->id }}')"
                                                      onmouseout="hideTooltip('tooltip-assign-{{ $publication->id }}')">
                                                    {{ $publication->reviewers->count() }}
                                                </span>

                                                {{-- Tooltip content --}}
                                                <div id="tooltip-assign-{{ $publication->id }}" class="tooltip-content">
                                                    @if($publication->reviewers->count() > 0)
                                                        <div style="font-weight: bold; margin-bottom: 4px;">Daftar Pemeriksa:</div>
                                                        @foreach($publication->reviewers as $reviewer)
                                                            <div style="padding: 2px 0;">• {{ $reviewer->fullname ?? $reviewer->name ?? 'Nama tidak tersedia' }}</div>
                                                        @endforeach
                                                    @else
                                                        <div>Belum ada pemeriksa yang ditugaskan</div>
                                                    @endif
                                                    <div class="tooltip-arrow"></div>
                                                </div>
                                            </span>
                                        </p>
                                    @endif

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <a href="{{ route('publications.summary', $publication) }}" class="px-3 py-1 bg-gray-500 text-white text-xs rounded-md hover:bg-gray-600">Ringkasan</a>
                                        <a href="{{ route('publications.show', $publication) }}" class="px-3 py-1 bg-indigo-500 text-white text-xs rounded-md hover:bg-indigo-600">
                                            @if(isset($isAdmin) && $isAdmin)
                                                Review (Admin)
                                            @else
                                                Mulai Review
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">
                                    @if(isset($isAdmin) && $isAdmin)
                                        Belum ada publikasi dalam sistem.
                                    @else
                                        Tidak ada tugas pemeriksaan untuk Anda saat ini.
                                    @endif
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- CSS untuk tooltip --}}
    <style>
        .tooltip-container {
            position: relative !important;
            display: inline-block !important;
        }

        .tooltip-content {
            position: fixed !important;
            background-color: #1f2937 !important;
            color: white !important;
            font-size: 12px !important;
            padding: 8px 12px !important;
            border-radius: 6px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3) !important;
            min-width: max-content !important;
            max-width: 300px !important;
            z-index: 99999 !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: opacity 0.2s ease !important;
            pointer-events: none !important;
            white-space: nowrap !important;
        }

        .tooltip-content.show {
            visibility: visible !important;
            opacity: 1 !important;
        }

        .tooltip-arrow {
            position: absolute !important;
            bottom: -5px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            width: 0 !important;
            height: 0 !important;
            border-left: 5px solid transparent !important;
            border-right: 5px solid transparent !important;
            border-top: 5px solid #1f2937 !important;
        }

        .tooltip-content * {
            color: white !important;
            margin: 0 !important;
        }

        .tooltip-content div {
            line-height: 1.4 !important;
        }
    </style>

    {{-- JavaScript untuk tooltip --}}
    <script>
        function showTooltip(tooltipId) {
            // Hide all other tooltips first
            const allTooltips = document.querySelectorAll('.tooltip-content');
            allTooltips.forEach(function(tip) {
                if (tip.id !== tooltipId) {
                    tip.style.visibility = 'hidden';
                    tip.style.opacity = '0';
                    tip.classList.remove('show');
                }
            });

            const tooltip = document.getElementById(tooltipId);
            const trigger = document.querySelector(`[data-tooltip="${tooltipId}"]`);

            if (tooltip && trigger) {
                // Get trigger position
                const rect = trigger.getBoundingClientRect();
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                // Position tooltip above trigger
                tooltip.style.left = (rect.left + rect.width / 2) + 'px';
                tooltip.style.top = (rect.top + scrollTop - 10) + 'px';
                tooltip.style.transform = 'translateX(-50%) translateY(-100%)';

                // Show tooltip
                tooltip.classList.add('show');
                tooltip.style.visibility = 'visible';
                tooltip.style.opacity = '1';
            }
        }

        function hideTooltip(tooltipId) {
            const tooltip = document.getElementById(tooltipId);
            if (tooltip) {
                tooltip.classList.remove('show');
                tooltip.style.visibility = 'hidden';
                tooltip.style.opacity = '0';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Ambil semua element yang memiliki tooltip
            const tooltipTriggers = document.querySelectorAll('[data-tooltip]');

            tooltipTriggers.forEach(function(trigger) {
                const tooltipId = trigger.getAttribute('data-tooltip');
                const tooltip = document.getElementById(tooltipId);

                if (tooltip) {
                    // Show tooltip on mouseenter
                    trigger.addEventListener('mouseenter', function() {
                        showTooltip(tooltipId);
                    });

                    // Hide tooltip on mouseleave
                    trigger.addEventListener('mouseleave', function() {
                        hideTooltip(tooltipId);
                    });

                    // Hide tooltip when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!trigger.contains(event.target) && !tooltip.contains(event.target)) {
                            hideTooltip(tooltipId);
                        }
                    });
                }
            });
        });
    </script>
@endsection
