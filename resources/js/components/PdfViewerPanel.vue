<!-- resources/js/components/PdfViewerPanel.vue -->
<template>
    <main class="pdf-viewer-container flex flex-col bg-gray-100 overflow-hidden">
        <!-- Header Kontrol -->
        <header class="flex items-center justify-between p-4 bg-white border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center gap-4">
                <h1 class="text-xl font-bold text-gray-700">Versi: {{ document.version }}</h1>
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
                        <!-- Render Point Annotation -->
                        <g v-if="comment.type === 'point' && parsePosition(comment.position)"
                        :transform="`translate(${parsePosition(comment.position).x || 0}, ${parsePosition(comment.position).y || 0})`"
                        class="cursor-pointer annotation-group">

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
                                fill="#ef4444"
                                transform="translate(-8, -16) scale(1)">
                            </path>
                        </g>

                        <!-- Render Area Annotation -->
                        <rect v-if="comment.type === 'area' && parsePosition(comment.position)"
                            :class="{
                                'highlighted-area': comment.id === highlightedCommentId && comment.status === 'open',
                                'highlighted-area-done': comment.id === highlightedCommentId && comment.status === 'done',
                                'done-area': comment.status === 'done'
                            }"
                            class="annotation-area cursor-pointer"
                            :x="parsePosition(comment.position).x || 0"
                            :y="parsePosition(comment.position).y || 0"
                            :width="parsePosition(comment.position).width || 0"
                            :height="parsePosition(comment.position).height || 0"
                            stroke="#3b82f6"
                            fill="rgba(59, 130, 246, 0.2)"
                            stroke-width="2">
                        </rect>
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
import { ref, computed, watch, nextTick } from 'vue';

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

// Watchers to sync local state with props
watch(() => props.currentPageNum, (newVal) => {
    currentPageNumInput.value = newVal;
}, { immediate: true });

watch(() => props.scale, (newVal) => {
    localScale.value = newVal;
}, { immediate: true });

// Toast notification function
const showToastMessage = (message, duration = 3000) => {
    toastMessage.value = message;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, duration);
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

// Position parsing with better error handling
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

// Watch untuk debugging perubahan props dengan improved logging
watch(() => props.commentsOnCurrentPage, (newComments, oldComments) => {
    if (process.env.NODE_ENV === 'development') {
        console.log('PdfViewerPanel: commentsOnCurrentPage changed');
        console.log('Old comments count:', oldComments?.length || 0);
        console.log('New comments count:', newComments?.length || 0);
        console.log('New comments:', newComments?.map(c => ({
            id: c.id,
            type: c.type,
            page_number: c.page_number,
            hasPosition: !!c.position,
            positionValid: !!parsePosition(c.position)
        })));
    }
}, { deep: true, immediate: true });

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

// Keyboard event handlers
const handleKeyDown = (event) => {
    // Handle keyboard shortcuts
    if (event.target.tagName === 'INPUT') return; // Don't interfere with input fields

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
            // Clear any active annotation tools
            if (props.annotationTool) {
                event.preventDefault();
                emit('annotation-tool-changed', null);
            }
            break;
    }
};

// Add keyboard event listener
if (typeof window !== 'undefined') {
    document.addEventListener('keydown', handleKeyDown);
}

// Cleanup on unmount
import { onUnmounted } from 'vue';

onUnmounted(() => {
    if (typeof window !== 'undefined') {
        document.removeEventListener('keydown', handleKeyDown);
    }
});
</script>
                '
