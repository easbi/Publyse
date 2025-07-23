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
                <button @click="changePage(-1)" :disabled="currentPageNum <= 1" class="px-3 py-2 bg-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300">&lt;</button>
                <span>Halaman <input type="number" v-model.number="currentPageNumInput" @change="goToPdfPage(currentPageNumInput)" class="w-14 text-center border rounded-md p-1"> / {{ totalPages }}</span>
                <button @click="changePage(1)" :disabled="currentPageNum >= totalPages" class="px-3 py-2 bg-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300">&gt;</button>
            </div>

            <!-- Kontrol Zoom -->
            <div v-if="pdfDoc" class="flex items-center gap-2 border border-gray-300 rounded-lg p-1">
                <button @click="$emit('zoom-out')" :disabled="scale <= minScale" class="p-2 rounded-md hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed" title="Zoom Out">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        <path d="M4 6.5h6a.5.5 0 0 1 0 1H4a.5.5 0 0 1 0-1z"/>
                    </svg>
                </button>

                <select v-model="localScale" @change="onZoomChange" class="text-sm border-0 bg-transparent focus:ring-0 cursor-pointer">
                    <option v-for="zoomLevel in zoomLevels" :key="zoomLevel.value" :value="zoomLevel.value">
                        {{ zoomLevel.label }}
                    </option>
                </select>

                <button @click="$emit('zoom-in')" :disabled="scale >= maxScale" class="p-2 rounded-md hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed" title="Zoom In">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        <path d="M6.5 4a.5.5 0 0 1 .5.5v2h2a.5.5 0 0 1 0 1h-2v2a.5.5 0 0 1-1 0v-2h-2a.5.5 0 0 1 0-1h2v-2A.5.5 0 0 1 6.5 4z"/>
                    </svg>
                </button>

                <div class="border-l border-gray-300 h-6 mx-1"></div>

                <button @click="$emit('fit-to-width')" class="p-2 rounded-md hover:bg-gray-100" title="Fit to Width">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 0v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1z"/>
                        <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8zM2 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm10-3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </button>

                <button @click="$emit('fit-to-page')" class="p-2 rounded-md hover:bg-gray-100" title="Fit to Page">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 0v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1z"/>
                        <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6zm0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </button>
            </div>

            <!-- Kontrol Alat Anotasi -->
            <div v-if="pdfDoc" class="flex items-center gap-2 border border-gray-300 rounded-lg p-1">
                <button
                    @click="setAnnotationTool('point')"
                    :class="[
                        annotationTool === 'point' ? 'bg-blue-500 text-white' : 'bg-white',
                        'hover:bg-blue-100'
                    ]"
                    class="p-2 rounded-md"
                    title="Komentar Titik">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                </button>
                <button
                    @click="setAnnotationTool('area')"
                    :class="[
                        annotationTool === 'area' ? 'bg-blue-500 text-white' : 'bg-white',
                        'hover:bg-blue-100'
                    ]"
                    class="p-2 rounded-md"
                    title="Komentar Area">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm-5 0H6v3h4V4zM6 0v3H1v1h5V0zM1 4h4v3H1V4zm4 4H1v1h4V8zm-4 4h4v3H1v-3zm5 3v-3h4v3h-4zm5-3h4v3h-4v-3zM11 4h4V1h-4v3z"/>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Area Tampilan PDF -->
        <div class="flex-grow p-4 overflow-auto flex justify-center items-start bg-gray-100" ref="pdfContainer">
            <div v-if="isLoading" class="flex items-center justify-center h-full">
                <span class="text-gray-500">Memuat PDF...</span>
            </div>
            <div v-if="pdfDoc" id="pdf-container" class="relative" :style="{ transform: `scale(${displayScale})`, transformOrigin: 'top center' }">
                <canvas id="pdf-canvas"></canvas>
                <svg id="annotation-layer"
                    @mousedown="handleMouseDown"
                    @mousemove="handleMouseMove"
                    @mouseup="handleMouseUp"
                    @click.self="handleLayerClick"
                    @wheel="handleWheel"
                    :width="canvasSize.width"
                    :height="canvasSize.height">

                    <g v-for="comment in commentsOnCurrentPage" :key="comment.id" @click="handleCommentClick(comment)">
                        <!-- Render Point Annotation -->
                        <g v-if="comment.type === 'point' && parsePosition(comment.position)"
                        :transform="`translate(${parsePosition(comment.position).x || 0}, ${parsePosition(comment.position).y || 0})`"
                        class="cursor-pointer">

                            <circle r="12"
                                    :fill="comment.status === 'done' ? '#16a34a' : 'red'"
                                    fill-opacity="0.3">
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
                            fill="rgba(59, 130, 246, 0.2)">
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
                        stroke-width="1"
                        stroke-dasharray="4"/>
                </svg>
            </div>
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

    #annotation-layer {
        position: absolute;
        top: 0;
        left: 0;
        cursor: crosshair;
        z-index: 10;
    }

    /* Annotation styling */
    .annotation-point.done-point {
        fill: #16a34a;
    }

    .annotation-area.done-area {
        stroke: #16a34a;
        fill: #16a34a;
    }

    .highlighted {
        animation: highlight-anim 1.5s ease;
    }

    @keyframes highlight-anim {
        0% { fill: yellow; }
        100% { fill: #ef4444; }
    }

    .highlighted-done {
        animation: highlight-done-anim 1.5s ease;
    }

    @keyframes highlight-done-anim {
        0% { fill: yellow; }
        100% { fill: #16a34a; }
    }

    .highlighted-area {
        animation: highlight-area-anim 1.5s ease;
    }

    @keyframes highlight-area-anim {
        0% { fill: rgba(253, 224, 71, 0.7); }
        100% { fill: rgba(59, 130, 246, 0.2); }
    }

    .highlighted-area-done {
        animation: highlight-area-done-anim 1.5s ease;
    }

    @keyframes highlight-area-done-anim {
        0% { fill: rgba(253, 224, 71, 0.7); }
        100% { fill: rgba(22, 163, 74, 0.2); }
    }

    /* Zoom transition */
    .zoom-transition {
        transition: transform 0.2s ease-out;
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

// Watchers to sync local state with props
watch(() => props.currentPageNum, (newVal) => {
    currentPageNumInput.value = newVal;
}, { immediate: true });

watch(() => props.scale, (newVal) => {
    localScale.value = newVal;
}, { immediate: true });

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

// Watch untuk debugging perubahan props
watch(() => props.commentsOnCurrentPage, (newComments, oldComments) => {

}, { deep: true, immediate: true });
const parsePosition = (positionData) => {
    if (!positionData) return null;

    try {
        if (typeof positionData === 'object') {
            return positionData;
        }
        if (typeof positionData === 'string') {
            return JSON.parse(positionData);
        }
        return null;
    } catch (error) {
        console.error('Error parsing position:', error);
        return null;
    }
};
</script>
