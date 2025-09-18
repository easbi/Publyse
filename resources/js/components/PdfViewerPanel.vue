<!-- resources/js/components/PdfViewerPanel.vue -->
<template>
    <main class="pdf-viewer-container flex flex-col bg-gray-100 overflow-hidden">
        <!-- Header Kontrol -->
        <header class="flex items-center justify-between p-4 bg-white border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center gap-4">
                <h1 class="text-xl font-bold text-gray-700">Versi: {{ document.version }}</h1>
            </div>

            <!-- Search Box PDF -->
            <div class="flex-1 max-w-md mx-4">
                <div class="relative">
                    <!-- Search Navigation -->
                    <div v-if="searchMatches.length > 0" class="absolute right-2 top-1.5 flex items-center gap-1">
                        <span class="text-xs text-gray-500">{{ currentMatchIndex + 1 }}/{{ searchMatches.length }}</span>
                        <button
                            @click="goToPreviousMatch"
                            :disabled="searchMatches.length <= 1"
                            class="p-1 hover:bg-gray-200 rounded disabled:opacity-50"
                            title="Hasil sebelumnya"
                        >
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <button
                            @click="goToNextMatch"
                            :disabled="searchMatches.length <= 1"
                            class="p-1 hover:bg-gray-200 rounded disabled:opacity-50"
                            title="Hasil selanjutnya"
                        >
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Clear Search -->
                    <button
                        v-if="pdfSearchQuery"
                        @click="clearPdfSearch"
                        class="absolute right-2 top-2 p-1 hover:bg-gray-200 rounded-full"
                        title="Hapus pencarian"
                    >
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Kontrol Navigasi PDF -->
            <div v-if="pdfDoc" class="flex items-center gap-3">
                <button
                    @click="changePage(-1)"
                    :disabled="currentPageNum <= 1"
                    class="px-3 py-2 bg-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300 transition-colors"
                    aria-label="Halaman sebelumnya"
                    title="Halaman sebelumnya">
                    &lt;
                </button>
                <span class="flex items-center gap-1">
                    Halaman
                    <input
                        type="number"
                        v-model.number="currentPageNumInput"
                        @change="goToPdfPage(currentPageNumInput)"
                        @keyup.enter="goToPdfPage(currentPageNumInput)"
                        class="w-12 text-center bg-gray-50 rounded-md p-1 text-sm"
                        :min="1"
                        :max="totalPages"
                        aria-label="Nomor halaman">
                    / {{ totalPages }}
                </span>
                <button
                    @click="changePage(1)"
                    :disabled="currentPageNum >= totalPages"
                    class="px-3 py-2 bg-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300 transition-colors"
                    aria-label="Halaman selanjutnya"
                    title="Halaman selanjutnya">
                    &gt;
                </button>
            </div>

            <!-- Kontrol Zoom -->
            <div v-if="pdfDoc" class="flex items-center gap-2 border border-gray-300 rounded-lg p-1">
                <button
                    @click="$emit('zoom-out')"
                    :disabled="scale <= minScale"
                    class="p-2 rounded-md hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    title="Zoom Out"
                    aria-label="Perkecil tampilan">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        <path d="M4 6.5h6a.5.5 0 0 1 0 1H4a.5.5 0 0 1 0-1z"/>
                    </svg>
                </button>

                <select
                    v-model="localScale"
                    @change="onZoomChange"
                    class="text-sm border-0 bg-transparent focus:ring-0 cursor-pointer min-w-[70px]"
                    aria-label="Level zoom">
                    <option v-for="zoomLevel in zoomLevels" :key="zoomLevel.value" :value="zoomLevel.value">
                        {{ zoomLevel.label }}
                    </option>
                </select>

                <button
                    @click="$emit('zoom-in')"
                    :disabled="scale >= maxScale"
                    class="p-2 rounded-md hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    title="Zoom In"
                    aria-label="Perbesar tampilan">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        <path d="M6.5 4a.5.5 0 0 1 .5.5v2h2a.5.5 0 0 1 0 1h-2v2a.5.5 0 0 1-1 0v-2h-2a.5.5 0 0 1 0-1h2v-2A.5.5 0 0 1 6.5 4z"/>
                    </svg>
                </button>

                <div class="border-l border-gray-300 h-6 mx-1"></div>

                <button
                    @click="$emit('fit-to-width')"
                    class="px-3 py-2 text-xs font-medium rounded-md hover:bg-gray-100 transition-colors border border-gray-300"
                    title="Fit to Width"
                    aria-label="Sesuaikan dengan lebar">
                    ↔
                </button>

                <button
                    @click="$emit('fit-to-page')"
                    class="px-3 py-2 text-xs font-medium rounded-md hover:bg-gray-100 transition-colors border border-gray-300"
                    title="Fit to Page"
                    aria-label="Sesuaikan dengan halaman">
                    ⤢
                </button>
            </div>

            <!-- Kontrol Alat Anotasi -->
            <div v-if="pdfDoc" class="flex items-center gap-2 border border-gray-300 rounded-lg p-1">
                <button
                    @click="setAnnotationTool('point')"
                    :class="[
                        annotationTool === 'point' ? 'bg-blue-500 text-white' : 'bg-white',
                        'hover:bg-blue-100 transition-colors'
                    ]"
                    class="p-2 rounded-md"
                    title="Komentar Titik"
                    aria-label="Alat komentar titik"
                    :aria-pressed="annotationTool === 'point'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                </button>
                <button
                    @click="setAnnotationTool('area')"
                    :class="[
                        annotationTool === 'area' ? 'bg-blue-500 text-white' : 'bg-white',
                        'hover:bg-blue-100 transition-colors'
                    ]"
                    class="p-2 rounded-md"
                    title="Komentar Area"
                    aria-label="Alat komentar area"
                    :aria-pressed="annotationTool === 'area'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm-5 0H6v3h4V4zM6 0v3H1v1h5V0zM1 4h4v3H1V4zm4 4H1v1h4V8zm-4 4h4v3H1v-3zm5 3v-3h4v3h-4zm5-3h4v3h-4v-3zM11 4h4V1h-4v3z"/>
                    </svg>
                </button>
            </div>

            <!-- Tombol Unduh -->
            <div v-if="pdfDoc" class="flex items-center gap-2">
                <button
                    @click="downloadPdf"
                    :disabled="isDownloading"
                    class="flex items-center gap-2 px-3 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 disabled:bg-green-300 disabled:cursor-not-allowed transition-colors"
                    title="Unduh PDF"
                    aria-label="Unduh file PDF">
                    <svg v-if="!isDownloading" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                    </svg>
                    <svg v-else class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                        <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
                    </svg>
                    <span class="text-sm">{{ isDownloading ? 'Mengunduh...' : 'Unduh' }}</span>
                </button>
            </div>
        </header>

        <!-- Search Box PDF - Ini yang dipake -->
        <div v-if="pdfDoc" class="px-4 py-3 bg-gray-50 border-b border-gray-200">
            <div class="max-w-md mx-auto">
                <div class="relative">
                    <input
                        v-model="pdfSearchQuery"
                        type="text"
                        placeholder="Jangan Cari teks dalam PDF..."
                        @keyup.enter="searchInPdf"
                        @input="handleSearchInput"
                        class="w-full px-3 py-2 pl-10 pr-24 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:border-blue-400 bg-white"
                    >

                    <!-- Search Navigation -->
                    <div v-if="searchMatches.length > 0" class="absolute right-2 top-1.5 flex items-center gap-1">
                        <span class="text-xs text-gray-500">{{ currentMatchIndex + 1 }}/{{ searchMatches.length }}</span>
                        <button
                            @click="goToPreviousMatch"
                            :disabled="searchMatches.length <= 1"
                            class="p-1 hover:bg-gray-200 rounded disabled:opacity-50"
                            title="Hasil sebelumnya"
                        >
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <button
                            @click="goToNextMatch"
                            :disabled="searchMatches.length <= 1"
                            class="p-1 hover:bg-gray-200 rounded disabled:opacity-50"
                            title="Hasil selanjutnya"
                        >
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Clear Search -->
                    <button
                        v-if="pdfSearchQuery"
                        @click="clearPdfSearch"
                        class="absolute right-2 top-2 p-1 hover:bg-gray-200 rounded-full"
                        title="Hapus pencarian"
                    >
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Search Results Info -->
                <div v-if="pdfSearchQuery && !isSearching" class="mt-2 text-xs text-center">
                    <span v-if="searchMatches.length > 0" class="text-green-600">
                        {{ searchMatches.length }} hasil ditemukan
                    </span>
                    <span v-else class="text-gray-500">
                        Tidak ada hasil ditemukan
                    </span>
                </div>
                <div v-if="isSearching" class="mt-2 text-xs text-center text-blue-500">
                    Mencari...
                </div>
            </div>
        </div>

        <!-- Area Tampilan PDF -->
        <div class="flex-grow p-4 overflow-auto flex justify-center items-start bg-gray-100" ref="pdfContainer">
            <div v-if="isLoading" class="flex items-center justify-center h-full">
                <div class="flex flex-col items-center gap-3">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    <span class="text-gray-500">Memuat PDF...</span>
                </div>
            </div>

            <div v-else-if="loadingError" class="flex items-center justify-center h-full">
                <div class="text-center p-8">
                    <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Gagal Memuat PDF</h3>
                    <p class="text-gray-500 mb-4">{{ loadingError }}</p>
                    <button
                        @click="retryLoading"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                        Coba Lagi
                    </button>
                </div>
            </div>

            <div v-if="pdfDoc" id="pdf-container" class="relative" :style="{ transform: `scale(${displayScale})`, transformOrigin: 'top center' }">
                <canvas id="pdf-canvas" class="shadow-lg"></canvas>

                <!-- Text Layer untuk PDF Search -->
                <div id="text-layer" class="text-layer"
                     :style="{width: canvasSize.width + 'px', height: canvasSize.height + 'px'}">
                </div>

                <svg id="annotation-layer"
                    @mousedown="handleMouseDown"
                    @mousemove="handleMouseMove"
                    @mouseup="handleMouseUp"
                    @click.self="handleLayerClick"
                    @wheel="handleWheel"
                    :width="canvasSize.width"
                    :height="canvasSize.height"
                    class="annotation-svg">

                    <g v-for="comment in commentsOnCurrentPage" :key="comment.id" @click="handleCommentClick(comment)">
                        <!-- UPDATED: Render Point Annotation dengan Scale-Aware Position -->
                        <g v-if="comment.type === 'point' && getCommentPosition(comment)"
                           :transform="`translate(${getCommentPosition(comment).x || 0}, ${getCommentPosition(comment).y || 0})`"
                           class="cursor-pointer annotation-group">

                            <!-- Scale indicator circle (jika berbeda dari current scale) -->
                            <circle v-if="comment.created_at_scale && comment.created_at_scale !== scale"
                                    r="18"
                                    :fill="comment.status === 'done' ? '#16a34a' : '#3b82f6'"
                                    fill-opacity="0.2"
                                    stroke="#fbbf24"
                                    stroke-width="2"
                                    stroke-dasharray="4,2"
                                    class="scale-indicator">
                            </circle>

                            <circle r="12"
                                    :fill="comment.status === 'done' ? '#16a34a' : 'red'"
                                    fill-opacity="0.3"
                                    class="annotation-background">
                            </circle>

                            <path :class="{
                                    'highlighted': comment.id === highlightedCommentId && comment.status === 'open',
                                    'highlighted-done': comment.id === highlightedCommentId && comment.status === 'done',
                                    'done-point': comment.status === 'done'
                                }"
                                class="annotation-point"
                                d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"
                                :fill="comment.status === 'done' ? '#16a34a' : '#ef4444'"
                                transform="translate(-8, -16) scale(1)">
                            </path>

                            <!-- Scale label (jika berbeda dari current scale) -->
                            <text v-if="comment.created_at_scale && comment.created_at_scale !== scale"
                                  x="0" y="-25"
                                  text-anchor="middle"
                                  class="scale-label text-xs"
                                  fill="#1f2937"
                                  font-weight="bold">
                                {{ Math.round(comment.created_at_scale * 100) }}%
                            </text>
                        </g>

                        <!-- UPDATED: Render Area Annotation dengan Scale-Aware Position -->
                        <rect v-if="comment.type === 'area' && getCommentPosition(comment)"
                            :class="{
                                'highlighted-area': comment.id === highlightedCommentId && comment.status === 'open',
                                'highlighted-area-done': comment.id === highlightedCommentId && comment.status === 'done',
                                'done-area': comment.status === 'done',
                                'scale-adjusted-area': comment.created_at_scale && comment.created_at_scale !== scale
                            }"
                            class="annotation-area cursor-pointer"
                            :x="getCommentPosition(comment).x || 0"
                            :y="getCommentPosition(comment).y || 0"
                            :width="getCommentPosition(comment).width || 0"
                            :height="getCommentPosition(comment).height || 0"
                            :stroke="comment.status === 'done' ? '#16a34a' : (comment.created_at_scale && comment.created_at_scale !== scale ? '#fbbf24' : '#3b82f6')"
                            :fill="comment.status === 'done' ? 'rgba(22, 163, 74, 0.2)' : (comment.created_at_scale && comment.created_at_scale !== scale ? 'rgba(251, 191, 36, 0.2)' : 'rgba(59, 130, 246, 0.2)')"
                            :stroke-width="comment.created_at_scale && comment.created_at_scale !== scale ? '3' : '2'"
                            :stroke-dasharray="comment.created_at_scale && comment.created_at_scale !== scale ? '5,3' : 'none'">
                        </rect>

                        <!-- Scale label untuk area annotation -->
                        <text v-if="comment.type === 'area' && comment.created_at_scale && comment.created_at_scale !== scale && getCommentPosition(comment)"
                              :x="(getCommentPosition(comment).x || 0) + (getCommentPosition(comment).width || 0) / 2"
                              :y="(getCommentPosition(comment).y || 0) - 5"
                              text-anchor="middle"
                              class="scale-label text-xs"
                              fill="#1f2937"
                              font-weight="bold">
                            {{ Math.round(comment.created_at_scale * 100) }}%
                        </text>
                    </g>

                    <!-- Temporary selection rectangle -->
                    <rect v-if="isDragging && tempSelectionRect"
                        :x="tempSelectionRect.x"
                        :y="tempSelectionRect.y"
                        :width="tempSelectionRect.width"
                        :height="tempSelectionRect.height"
                        fill="rgba(59, 130, 246, 0.2)"
                        stroke="#3b82f6"
                        stroke-width="2"
                        stroke-dasharray="5,5"
                        class="temp-selection">
                        <animate attributeName="stroke-dashoffset" values="0;10" dur="1s" repeatCount="indefinite"/>
                    </rect>
                </svg>
            </div>
        </div>

        <!-- Toast Notification -->
        <div v-if="showToast"
             class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform"
             :class="showToast ? 'translate-y-0 opacity-100' : 'translate-y-2 opacity-0'">
            {{ toastMessage }}
        </div>
    </main>
</template>

<style scoped>
    /* Layout responsif dengan proporsi 5/7 dari lebar layar */
    .pdf-viewer-container {
        width: calc(5/7 * 100%);
        min-width: 0;
        flex-shrink: 0;
    }

    /* Responsive untuk layar kecil */
    @media (max-width: 1200px) {
        .pdf-viewer-container {
            width: 60%;
        }
    }

    @media (max-width: 768px) {
        .pdf-viewer-container {
            width: 50%;
        }
    }

    /* PDF Container */
    #pdf-container {
        position: relative;
        transition: transform 0.1s ease-out;
    }

    /* Text Layer untuk PDF Search */
    .text-layer {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 5;
        pointer-events: none;
        opacity: 0;
    }

    /* Search highlight styling */
    .text-layer .search-highlight {
        background-color: #fbbf24 !important;
        color: transparent !important;
        border-radius: 2px;
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    .text-layer .search-highlight.current {
        background-color: #f97316 !important;
        opacity: 0.9;
    }

    .annotation-svg {
        position: absolute;
        top: 0;
        left: 0;
        cursor: crosshair;
        z-index: 10;
    }

    /* Annotation styling */
    .annotation-group:hover .annotation-background {
        fill-opacity: 0.5;
    }

    .annotation-point.done-point {
        fill: #16a34a;
    }

    .annotation-area.done-area {
        stroke: #16a34a;
        fill: rgba(22, 163, 74, 0.2);
    }

    /* ADDED: Scale-adjusted area styling */
    .annotation-area.scale-adjusted-area {
        stroke: #fbbf24;
        fill: rgba(251, 191, 36, 0.2);
        stroke-width: 3;
        stroke-dasharray: 5,3;
    }

    /* ADDED: Scale indicator styling */
    .scale-indicator {
        animation: scale-pulse 2s infinite;
    }

    @keyframes scale-pulse {
        0%, 100% { stroke-opacity: 0.5; }
        50% { stroke-opacity: 1; }
    }

    .scale-label {
        font-size: 10px;
        text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
    }

    .temp-selection {
        pointer-events: none;
    }

    /* Highlight animations */
    .highlighted {
        animation: highlight-anim 1.5s ease;
    }

    @keyframes highlight-anim {
        0%, 20% { fill: yellow; }
        100% { fill: #ef4444; }
    }

    .highlighted-done {
        animation: highlight-done-anim 1.5s ease;
    }

    @keyframes highlight-done-anim {
        0%, 20% { fill: yellow; }
        100% { fill: #16a34a; }
    }

    .highlighted-area {
        animation: highlight-area-anim 1.5s ease;
    }

    @keyframes highlight-area-anim {
        0%, 20% { fill: rgba(253, 224, 71, 0.7); stroke: #fcd34d; }
        100% { fill: rgba(59, 130, 246, 0.2); stroke: #3b82f6; }
    }

    .highlighted-area-done {
        animation: highlight-area-done-anim 1.5s ease;
    }

    @keyframes highlight-area-done-anim {
        0%, 20% { fill: rgba(253, 224, 71, 0.7); stroke: #fcd34d; }
        100% { fill: rgba(22, 163, 74, 0.2); stroke: #16a34a; }
    }

    /* Loading spinner */
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Improved button transitions */
    button {
        transition: all 0.2s ease-in-out;
    }

    button:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    button:active:not(:disabled) {
        transform: translateY(0);
    }

    /* Focus styles for accessibility */
    button:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Canvas shadow */
    #pdf-canvas {
        border-radius: 4px;
    }
</style>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';

// Define emits
const emit = defineEmits([
    'page-changed',
    'zoom-changed',
    'annotation-tool-changed',
    'annotation-mouse-down',
    'annotation-mouse-move',
    'annotation-mouse-up',
    'layer-click',
    'wheel',
    'go-to-comment',
    'fit-to-width',
    'fit-to-page',
    'zoom-in',
    'zoom-out'
]);

// Define props
const props = defineProps({
    document: Object,
    pdfDoc: Object,
    currentPageNum: Number,
    totalPages: Number,
    scale: Number,
    displayScale: Number,
    isLoading: Boolean,
    canvasSize: Object,
    annotationTool: String,
    tempSelectionRect: Object,
    isDragging: Boolean,
    commentsOnCurrentPage: Array,
    highlightedCommentId: [String, Number],
    zoomLevels: Array,
    minScale: Number,
    maxScale: Number
});

// Local state
const currentPageNumInput = ref(1);
const localScale = ref(1.5);
const isDownloading = ref(false);
const loadingError = ref(null);
const showToast = ref(false);
const toastMessage = ref('');

// PDF Search state
const pdfSearchQuery = ref('');
const searchMatches = ref([]);
const currentMatchIndex = ref(0);
const isSearching = ref(false);
const searchDebounceTimeout = ref(null);
const pageTextContents = ref(new Map()); // Cache text content per page

// Watchers to sync local state with props
watch(() => props.currentPageNum, (newVal) => {
    currentPageNumInput.value = newVal;
}, { immediate: true });

watch(() => props.scale, (newVal) => {
    localScale.value = newVal;
}, { immediate: true });

// Watch for page changes to update search highlights
watch(() => props.currentPageNum, async () => {
    if (pdfSearchQuery.value) {
        await updateSearchHighlights();
    }
});

// Watch for scale changes to update search highlights
watch(() => props.scale, async () => {
    if (pdfSearchQuery.value) {
        await updateSearchHighlights();
    }
});

// ADDED: Scale-aware position getter
const getCommentPosition = (comment) => {
    // Priority: adjusted_position > parsed position
    if (comment.adjusted_position) {
        return comment.adjusted_position;
    }

    return parsePosition(comment.position);
};

// Toast notification function
const showToastMessage = (message, duration = 3000) => {
    toastMessage.value = message;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, duration);
};

// PDF Search functions
const handleSearchInput = () => {
    if (searchDebounceTimeout.value) {
        clearTimeout(searchDebounceTimeout.value);
    }

    searchDebounceTimeout.value = setTimeout(() => {
        searchInPdf();
    }, 500);
};

const searchInPdf = async () => {
    if (!pdfSearchQuery.value.trim() || !props.pdfDoc) {
        clearSearchHighlights();
        return;
    }

    isSearching.value = true;
    searchMatches.value = [];
    currentMatchIndex.value = 0;

    try {
        await performPdfSearch();
        if (searchMatches.value.length > 0) {
            goToMatch(0);
        }
    } catch (error) {
        console.error('Search error:', error);
        showToastMessage('Error during search');
    } finally {
        isSearching.value = false;
    }
};

const performPdfSearch = async () => {
    const query = pdfSearchQuery.value.toLowerCase().trim();
    const matches = [];

    for (let pageNum = 1; pageNum <= props.totalPages; pageNum++) {
        try {
            const textContent = await getPageTextContent(pageNum);
            if (textContent) {
                const pageText = textContent.items.map(item => item.str).join(' ').toLowerCase();

                let searchIndex = 0;
                let matchIndex;

                while ((matchIndex = pageText.indexOf(query, searchIndex)) !== -1) {
                    matches.push({
                        pageNum,
                        textIndex: matchIndex,
                        text: query
                    });
                    searchIndex = matchIndex + 1;
                }
            }
        } catch (error) {
            console.warn(`Error searching page ${pageNum}:`, error);
        }
    }

    searchMatches.value = matches;
};

const getPageTextContent = async (pageNum) => {
    // Check cache first
    if (pageTextContents.value.has(pageNum)) {
        return pageTextContents.value.get(pageNum);
    }

    try {
        const page = await props.pdfDoc.getPage(pageNum);
        const textContent = await page.getTextContent();

        // Cache the result
        pageTextContents.value.set(pageNum, textContent);
        return textContent;
    } catch (error) {
        console.error(`Error getting text content for page ${pageNum}:`, error);
        return null;
    }
};

const updateSearchHighlights = async () => {
    if (!pdfSearchQuery.value.trim()) {
        clearSearchHighlights();
        return;
    }

    const textLayer = document.getElementById('text-layer');
    if (!textLayer) return;

    // Clear existing highlights
    textLayer.innerHTML = '';

    try {
        const textContent = await getPageTextContent(props.currentPageNum);
        if (!textContent) return;

        const viewport = await getPageViewport(props.currentPageNum);
        if (!viewport) return;

        renderTextLayer(textContent, viewport, textLayer);
        highlightSearchTerms(textLayer);
    } catch (error) {
        console.error('Error updating search highlights:', error);
    }
};

const getPageViewport = async (pageNum) => {
    try {
        const page = await props.pdfDoc.getPage(pageNum);
        return page.getViewport({ scale: props.scale });
    } catch (error) {
        console.error(`Error getting viewport for page ${pageNum}:`, error);
        return null;
    }
};

const renderTextLayer = (textContent, viewport, textLayer) => {
    textContent.items.forEach((textItem, index) => {
        const textDiv = document.createElement('div');
        const transform = textItem.transform;

        // Calculate position
        const x = transform[4];
        const y = transform[5];

        // Apply transformation
        textDiv.style.position = 'absolute';
        textDiv.style.left = x + 'px';
        textDiv.style.top = (viewport.height - y) + 'px';
        textDiv.style.fontSize = Math.abs(transform[0]) + 'px';
        textDiv.style.fontFamily = textItem.fontName || 'Arial';
        textDiv.style.color = 'transparent';
        textDiv.style.pointerEvents = 'none';
        textDiv.textContent = textItem.str;

        textLayer.appendChild(textDiv);
    });
};

const highlightSearchTerms = (textLayer) => {
    const query = pdfSearchQuery.value.toLowerCase().trim();
    const textNodes = textLayer.querySelectorAll('div');

    textNodes.forEach(node => {
        const text = node.textContent.toLowerCase();
        if (text.includes(query)) {
            node.classList.add('search-highlight');

            // Check if this is the current match
            const currentMatch = searchMatches.value[currentMatchIndex.value];
            if (currentMatch && currentMatch.pageNum === props.currentPageNum) {
                // This is a simplified check - in a full implementation,
                // you'd want to track exact text positions
                node.classList.add('current');
            }
        }
    });
};

const clearSearchHighlights = () => {
    const textLayer = document.getElementById('text-layer');
    if (textLayer) {
        textLayer.innerHTML = '';
    }
};

const clearPdfSearch = () => {
    pdfSearchQuery.value = '';
    searchMatches.value = [];
    currentMatchIndex.value = 0;
    clearSearchHighlights();
};

const goToNextMatch = () => {
    if (searchMatches.value.length > 1) {
        currentMatchIndex.value = (currentMatchIndex.value + 1) % searchMatches.value.length;
        goToMatch(currentMatchIndex.value);
    }
};

const goToPreviousMatch = () => {
    if (searchMatches.value.length > 1) {
        currentMatchIndex.value = currentMatchIndex.value === 0
            ? searchMatches.value.length - 1
            : currentMatchIndex.value - 1;
        goToMatch(currentMatchIndex.value);
    }
};

const goToMatch = async (matchIndex) => {
    const match = searchMatches.value[matchIndex];
    if (!match) return;

    // Navigate to the page if needed
    if (match.pageNum !== props.currentPageNum) {
        emit('page-changed', match.pageNum);

        // Wait for page to render
        await nextTick();
        setTimeout(async () => {
            await updateSearchHighlights();
        }, 100);
    } else {
        await updateSearchHighlights();
    }
};

// Page navigation functions
const changePage = (delta) => {
    const newPageNum = props.currentPageNum + delta;
    if (newPageNum > 0 && newPageNum <= props.totalPages) {
        emit('page-changed', newPageNum);
    }
};

const goToPdfPage = (num) => {
    const pageNum = parseInt(num, 10);
    if (pageNum > 0 && pageNum <= props.totalPages) {
        emit('page-changed', pageNum);
    } else {
        currentPageNumInput.value = props.currentPageNum;
        showToastMessage('Nomor halaman tidak valid');
    }
};

// Zoom functions
const onZoomChange = () => {
    emit('zoom-changed', localScale.value);
};

// Annotation tool functions
const setAnnotationTool = (tool) => {
    emit('annotation-tool-changed', tool);
};

// Mouse event handlers
const handleMouseDown = (event) => {
    emit('annotation-mouse-down', event);
};

const handleMouseMove = (event) => {
    emit('annotation-mouse-move', event);
};

const handleMouseUp = (event) => {
    emit('annotation-mouse-up', event);
};

const handleLayerClick = (event) => {
    emit('layer-click', event);
};

const handleWheel = (event) => {
    emit('wheel', event);
};

const handleCommentClick = (comment) => {
    // Show scale info in toast if different
    if (comment.created_at_scale && comment.created_at_scale !== props.scale) {
        const commentScale = Math.round(comment.created_at_scale * 100);
        const currentScale = Math.round(props.scale * 100);
        showToastMessage(`Komentar dibuat pada zoom ${commentScale}% (saat ini ${currentScale}%)`);
    }

    emit('go-to-comment', comment);
};

// Download PDF function - IMPROVED VERSION
const downloadPdf = async () => {
    if (isDownloading.value) return;

    isDownloading.value = true;

    try {
        // Method 1: Direct URL download (most common case)
        if (props.document?.pdf_url) {
            await downloadFromUrl();
            showToastMessage('PDF berhasil diunduh!');
        } else {
            throw new Error('URL PDF tidak tersedia');
        }
    } catch (error) {
        console.error('Error downloading PDF:', error);
        showToastMessage('Gagal mengunduh PDF: ' + error.message);

        // Fallback: try opening in new tab
        if (props.document?.pdf_url) {
            try {
                window.open(props.document.pdf_url, '_blank');
                showToastMessage('PDF dibuka di tab baru');
            } catch (fallbackError) {
                console.error('Fallback also failed:', fallbackError);
            }
        }
    } finally {
        isDownloading.value = false;
    }
};

// Download from URL with proper error handling
const downloadFromUrl = async () => {
    const url = props.document.pdf_url;
    const filename = generateFilename();

    try {
        // Try fetch method first (handles CORS and auth better)
        const response = await fetch(url, {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/pdf',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const blob = await response.blob();
        downloadBlob(blob, filename);

    } catch (fetchError) {
        console.warn('Fetch method failed, trying direct link:', fetchError);

        // Fallback to direct link method
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        link.target = '_blank';

        // For some browsers, we need to add the link to DOM temporarily
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};

// Generate appropriate filename
const generateFilename = () => {
    const title = props.document?.title || props.document?.name || 'document';
    const version = props.document?.version ? `_v${props.document.version}` : '';
    const timestamp = new Date().toISOString().slice(0, 10); // YYYY-MM-DD

    // Clean filename from invalid characters
    const cleanTitle = title.replace(/[^\w\s-]/g, '').replace(/\s+/g, '_');

    return `${cleanTitle}${version}_${timestamp}.pdf`;
};

// Download blob as file
const downloadBlob = (blob, filename) => {
    const url = URL.createObjectURL(blob);

    try {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;

        // Add to DOM temporarily for Firefox compatibility
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } finally {
        // Clean up the object URL
        setTimeout(() => URL.revokeObjectURL(url), 100);
    }
};

// Retry loading function
const retryLoading = () => {
    loadingError.value = null;
    // Emit event to parent to retry loading
    emit('retry-loading');
};

// UPDATED: Position parsing dengan enhanced error handling
const parsePosition = (positionData) => {
    if (!positionData) return null;

    try {
        if (typeof positionData === 'object') {
            // Validate object has required properties
            if (typeof positionData.x === 'number' && typeof positionData.y === 'number') {
                return positionData;
            }
            return null;
        }

        if (typeof positionData === 'string') {
            const parsed = JSON.parse(positionData);
            // Validate parsed object
            if (typeof parsed.x === 'number' && typeof parsed.y === 'number') {
                return parsed;
            }
            return null;
        }

        return null;
    } catch (error) {
        console.error('Error parsing position:', error, positionData);
        return null;
    }
};

// Keyboard event handlers
const handleKeyDown = (event) => {
    // Handle keyboard shortcuts
    if (event.target.tagName === 'INPUT' && event.target.placeholder === 'Cari teks dalam PDF...') {
        // Allow normal typing in search box, but handle special keys
        if (event.key === 'Enter') {
            event.preventDefault();
            if (event.shiftKey) {
                goToPreviousMatch();
            } else {
                goToNextMatch();
            }
        } else if (event.key === 'Escape') {
            clearPdfSearch();
            event.target.blur();
        }
        return;
    }

    if (event.target.tagName === 'INPUT') return; // Don't interfere with other input fields

    // Global shortcuts
    if (event.ctrlKey || event.metaKey) {
        if (event.key === 'f' || event.key === 'F') {
            event.preventDefault();
            const searchInput = document.querySelector('input[placeholder="Jangan teks dalam PDF..."]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
            return;
        }
    }

    switch (event.key) {
        case 'ArrowLeft':
            if (props.currentPageNum > 1) {
                event.preventDefault();
                changePage(-1);
            }
            break;
        case 'ArrowRight':
            if (props.currentPageNum < props.totalPages) {
                event.preventDefault();
                changePage(1);
            }
            break;
        case 'Home':
            if (props.currentPageNum !== 1) {
                event.preventDefault();
                emit('page-changed', 1);
            }
            break;
        case 'End':
            if (props.currentPageNum !== props.totalPages) {
                event.preventDefault();
                emit('page-changed', props.totalPages);
            }
            break;
        case 'Escape':
            // Clear any active annotation tools or search
            if (props.annotationTool) {
                event.preventDefault();
                emit('annotation-tool-changed', null);
            }
            if (pdfSearchQuery.value) {
                event.preventDefault();
                clearPdfSearch();
            }
            break;
        case 'F3':
            event.preventDefault();
            if (searchMatches.value.length > 0) {
                if (event.shiftKey) {
                    goToPreviousMatch();
                } else {
                    goToNextMatch();
                }
            }
            break;
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyDown);

    // Clear search timeout
    if (searchDebounceTimeout.value) {
        clearTimeout(searchDebounceTimeout.value);
    }

    // Clear text content cache
    pageTextContents.value.clear();
});

// Watch for loading errors
watch(() => props.isLoading, (isLoading, wasLoading) => {
    if (wasLoading && !isLoading) {
        // Loading finished, check if PDF loaded successfully
        if (!props.pdfDoc) {
            loadingError.value = 'PDF gagal dimuat. File mungkin rusak atau tidak dapat diakses.';
        } else {
            loadingError.value = null;
        }
    }
});

// Watch for scale changes to show relevant comments info
watch(() => props.scale, (newScale, oldScale) => {
    if (oldScale && newScale !== oldScale && props.commentsOnCurrentPage.length > 0) {
        const adjustedComments = props.commentsOnCurrentPage.filter(c =>
            c.created_at_scale && c.created_at_scale !== newScale
        );

        if (adjustedComments.length > 0) {
            console.log(`Scale changed to ${Math.round(newScale * 100)}%. ${adjustedComments.length} comments need position adjustment.`);
        }
    }
});

// Debug function for scale-aware comments
const debugScaleAwareComments = () => {
    console.log('=== SCALE-AWARE COMMENTS DEBUG ===');
    console.log('Current scale:', props.scale);
    console.log('Comments on page:', props.commentsOnCurrentPage.length);

    props.commentsOnCurrentPage.forEach(comment => {
        const position = getCommentPosition(comment);
        console.log(`Comment ${comment.id}:`, {
            created_at_scale: comment.created_at_scale,
            current_scale: props.scale,
            needs_adjustment: comment.created_at_scale !== props.scale,
            original_position: comment.position,
            adjusted_position: comment.adjusted_position,
            final_position: position,
            type: comment.type
        });
    });
    console.log('=================================');
};

// Expose debug function to window
if (typeof window !== 'undefined') {
    window.debugScaleAwareComments = debugScaleAwareComments;
    window.debugPdfSearch = () => {
        console.log('=== PDF SEARCH DEBUG ===');
        console.log('Search query:', pdfSearchQuery.value);
        console.log('Search matches:', searchMatches.value);
        console.log('Current match index:', currentMatchIndex.value);
        console.log('Page text cache size:', pageTextContents.value.size);
        console.log('========================');
    };
}

</script>
