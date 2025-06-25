<!-- resources/js/components/PdfReviewer.vue -->
<template>
    <div class="flex h-[80vh] w-full gap-4" :class="{ 'no-select': isDragging }">

        <!-- Main Content: PDF Viewer -->
        <main class="flex-grow flex flex-col bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header Kontrol -->
            <header class="flex items-center justify-between p-3 bg-gray-50 border-b border-gray-200">
                <!-- Informasi Dokumen -->
                <div class="flex items-center gap-4">
                    <h1 class="text-xl font-bold text-gray-700">Versi: {{ document.version }}</h1>
                    <a :href="pdfUrl" target="_blank" class="text-sm text-blue-600 hover:underline">Buka PDF di Tab Baru</a>
                </div>

                <!-- Kontrol Navigasi PDF -->
                <div v-if="pdfDoc" class="flex items-center gap-3">
                    <button @click="changePage(-1)" :disabled="currentPageNum <= 1" class="px-3 py-2 bg-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300">&lt;</button>
                    <span>Halaman <input type="number" v-model.number="currentPageNumInput" @change="goToPage(currentPageNumInput)" class="w-14 text-center border rounded-md p-1"> / {{ totalPages }}</span>
                    <button @click="changePage(1)" :disabled="currentPageNum >= totalPages" class="px-3 py-2 bg-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300">&gt;</button>
                </div>

                <!-- Kontrol Alat Anotasi -->
                <div v-if="pdfDoc" class="flex items-center gap-2 border border-gray-300 rounded-lg p-1">
                    <button @click="setAnnotationTool('point')" :class="{'bg-blue-500 text-white': annotationTool === 'point'}" class="p-2 rounded-md hover:bg-blue-100" title="Komentar Titik"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg></button>
                    <button @click="setAnnotationTool('area')" :class="{'bg-blue-500 text-white': annotationTool === 'area'}" class="p-2 rounded-md hover:bg-blue-100" title="Komentar Area"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm-5 0H6v3h4V4zM6 0v3H1v1h5V0zM1 4h4v3H1V4zm4 4H1v1h4V8zm-4 4h4v3H1v-3zm5 3v-3h4v3h-4zm5-3h4v3h-4v-3zM11 4h4V1h-4v3z"/></svg></button>
                </div>
            </header>
            
            <!-- Area Tampilan PDF -->
            <div class="flex-grow p-4 overflow-auto flex justify-center items-start bg-gray-200">
                <div v-if="isLoading" class="flex items-center justify-center h-full"><span class="text-gray-500">Memuat PDF...</span></div>
                <div v-if="pdfDoc" id="pdf-container">
                    <canvas id="pdf-canvas"></canvas>
                    <svg id="annotation-layer" @mousedown="startSelection" @mousemove="updateSelection" @mouseup="endSelection" @click.self="handleLayerClick" :width="canvasSize.width" :height="canvasSize.height">
                        <g v-for="comment in commentsOnCurrentPage" :key="comment.id" @click="handleGoToComment(comment)">
                           <!-- Render Anotasi di sini -->
                            <g v-if="comment.type === 'point'" :transform="`translate(${comment.position.x}, ${comment.position.y})`" class="cursor-pointer">
                                <circle r="12" :fill="comment.status === 'done' ? '#16a34a' : 'red'" fill-opacity="0.3"></circle>
                                <path :class="{ 'highlighted': comment.id === highlightedCommentId && comment.status === 'open', 'highlighted-done': comment.id === highlightedCommentId && comment.status === 'done', 'done-point': comment.status === 'done' }" class="annotation-point" d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" fill="#ef4444" transform="translate(-8, -16) scale(1)"></path>
                            </g>
                            <rect v-if="comment.type === 'area'" :class="{ 'highlighted-area': comment.id === highlightedCommentId && comment.status === 'open', 'highlighted-area-done': comment.id === highlightedCommentId && comment.status === 'done', 'done-area': comment.status === 'done' }" class="annotation-area cursor-pointer" :x="comment.position.x" :y="comment.position.y" :width="comment.position.width" :height="comment.position.height" stroke="#3b82f6" fill="#3b82f6"></rect>
                        </g>
                        <rect v-if="isDragging && tempSelectionRect" :x="tempSelectionRect.x" :y="tempSelectionRect.y" :width="tempSelectionRect.width" :height="tempSelectionRect.height" fill="rgba(59, 130, 246, 0.2)" stroke="#3b82f6" stroke-width="1" stroke-dasharray="4"/>
                    </svg>
                </div>
            </div>
        </main>

        <!-- Sidebar: Daftar Komentar -->
        <aside class="w-1/3 max-w-md flex flex-col bg-white rounded-lg shadow-lg">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold text-gray-700">Daftar Komentar</h2>
                <div class="mt-3 flex gap-2">
                    <button @click="commentFilter = 'all'" :class="{'bg-blue-500 text-white': commentFilter === 'all'}" class="flex-1 text-sm px-3 py-1 border rounded-md">Semua</button>
                    <button @click="commentFilter = 'open'" :class="{'bg-blue-500 text-white': commentFilter === 'open'}" class="flex-1 text-sm px-3 py-1 border rounded-md">Terbuka</button>
                    <button @click="commentFilter = 'done'" :class="{'bg-blue-500 text-white': commentFilter === 'done'}" class="flex-1 text-sm px-3 py-1 border rounded-md">Selesai</button>
                </div>
            </div>
            <div v-if="filteredComments.length === 0" class="flex-grow flex items-center justify-center"><p class="text-gray-500">Tidak ada komentar.</p></div>
            <ul v-else class="flex-grow overflow-y-auto p-2 space-y-2">
                <li v-for="comment in filteredComments" :key="comment.id" @click="handleGoToComment(comment)" class="p-3 rounded-lg border border-gray-200 cursor-pointer transition-colors" :class="comment.status === 'done' ? 'bg-green-50' : 'hover:bg-blue-50'">
                    <div class="flex justify-between items-start gap-3">
                        <div class="flex-grow">
                             <p class="text-xs font-semibold text-gray-600">{{ comment.user.name }}</p>
                             <p class="text-sm text-gray-800 break-words" :class="{'line-through text-gray-500': comment.status === 'done'}">{{ comment.content }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="text-xs font-semibold bg-gray-200 text-gray-700 px-2 py-1 rounded-full whitespace-nowrap">Hal. {{ comment.page_number }}</span>
                            <input type="checkbox" :checked="comment.status === 'done'" @change="toggleCommentStatus(comment)" @click.stop class="form-checkbox h-5 w-5 rounded text-green-600 transition duration-150 ease-in-out cursor-pointer">
                        </div>
                    </div>
                </li>
            </ul>
        </aside>

        <!-- Modal -->
        <div v-if="showCommentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Tambahkan Komentar</h3>
                <textarea v-model="newCommentData.content" ref="commentTextarea" class="w-full border rounded-md p-2 h-28" placeholder="Tulis komentar Anda di sini..."></textarea>
                <div class="mt-4 flex justify-end gap-3">
                    <button @click="cancelComment" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Batal</button>
                    <button @click="saveComment" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    /* Tambahkan style dari prototipe sebelumnya ke sini */
    .annotation-point.done-point { fill: #16a34a; }
    .annotation-area.done-area { stroke: #16a34a; fill: #16a34a; }
    .highlighted { animation: highlight-anim 1.5s ease; }
    @keyframes highlight-anim { 0% { fill: yellow; } 100% { fill: #ef4444; } }
    .highlighted-done { animation: highlight-done-anim 1.5s ease; }
    @keyframes highlight-done-anim { 0% { fill: yellow; } 100% { fill: #16a34a; } }
    .highlighted-area { animation: highlight-area-anim 1.5s ease; }
    @keyframes highlight-area-anim { 0% { fill: rgba(253, 224, 71, 0.7); } 100% { fill: rgba(59, 130, 246, 0.2); } }
    .highlighted-area-done { animation: highlight-area-done-anim 1.5s ease; }
    @keyframes highlight-area-done-anim { 0% { fill: rgba(253, 224, 71, 0.7); } 100% { fill: rgba(22, 163, 74, 0.2); } }
    .no-select { -webkit-user-select: none; -ms-user-select: none; user-select: none; }
</style>

<script setup>
import { ref, onMounted, computed, watch, nextTick, markRaw } from 'vue';
import * as pdfjsLib from 'pdfjs-dist';
import axios from 'axios';

// BARU: Menerima data dari Blade sebagai props
const props = defineProps({
    document: Object,
    currentUser: Object,
});

// BARU: Set state awal dari props
const comments = ref(props.document.comments || []);
const pdfUrl = ref(`/storage/${props.document.stored_path}`);

// State untuk PDF
const pdfDoc = ref(null);
const currentPageNum = ref(1);
const currentPageNumInput = ref(1);
const totalPages = ref(0);
const scale = ref(1.5);
const isLoading = ref(false);
const canvasSize = ref({ width: 0, height: 0 });

// State untuk Anotasi
const annotationTool = ref('point');
const showCommentModal = ref(false);
const commentTextarea = ref(null);
const newCommentData = ref({ content: '', position: null, type: '', page_number: 0 });
const highlightedCommentId = ref(null);
const commentFilter = ref('all');

// State untuk menggambar area
const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });
const tempSelectionRect = ref(null);

// Mengatur worker untuk PDF.js
pdfjsLib.GlobalWorkerOptions.workerSrc = `https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js`;

onMounted(() => {
    loadPdfFromUrl();
});

watch(showCommentModal, (isShowing) => {
    if (isShowing) nextTick(() => { commentTextarea.value?.focus(); });
});

const loadPdfFromUrl = async () => {
    isLoading.value = true;
    try {
        const loadingTask = pdfjsLib.getDocument(pdfUrl.value);
        const pdf = await loadingTask.promise;
        pdfDoc.value = markRaw(pdf);
        totalPages.value = pdf.numPages;
        await renderPage(1);
    } catch (error) {
        console.error("Gagal memuat PDF:", error);
        alert("Gagal memuat file PDF dari server. Pastikan path benar dan file dapat diakses.");
    } finally {
        isLoading.value = false;
    }
};

const renderPage = async (num) => {
    if (!pdfDoc.value) return;
    isLoading.value = true;
    try {
        const page = await pdfDoc.value.getPage(num);
        const viewport = page.getViewport({ scale: scale.value });
        const canvas = document.getElementById('pdf-canvas');
        const context = canvas.getContext('2d');
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        canvasSize.value = { width: viewport.width, height: viewport.height };
        const renderContext = { canvasContext: context, viewport: viewport };
        await page.render(renderContext).promise;
        currentPageNum.value = num;
        currentPageNumInput.value = num;
    } catch (error) {
        console.error("Gagal merender halaman:", error);
    } finally {
        isLoading.value = false;
    }
};

const changePage = (delta) => {
    const newPageNum = currentPageNum.value + delta;
    if (newPageNum > 0 && newPageNum <= totalPages.value) {
        renderPage(newPageNum);
    }
};

const goToPage = (num) => {
    const pageNum = parseInt(num, 10);
    if (pageNum > 0 && pageNum <= totalPages.value) {
        renderPage(pageNum);
    } else {
        currentPageNumInput.value = currentPageNum.value;
    }
};

const setAnnotationTool = (tool) => { annotationTool.value = tool; };

const handleLayerClick = (event) => {
    if (annotationTool.value !== 'point' || isDragging.value) return;
    newCommentData.value = { content: '', position: { x: event.offsetX, y: event.offsetY }, type: 'point', page_number: currentPageNum.value };
    showCommentModal.value = true;
};

const startSelection = (event) => {
    if (annotationTool.value !== 'area' || event.target.closest('g, rect')) return;
    isDragging.value = true;
    dragStart.value = { x: event.offsetX, y: event.offsetY };
    tempSelectionRect.value = { x: event.offsetX, y: event.offsetY, width: 0, height: 0 };
};

const updateSelection = (event) => {
    if (!isDragging.value) return;
    const currentX = event.offsetX;
    const currentY = event.offsetY;
    const x = Math.min(currentX, dragStart.value.x);
    const y = Math.min(currentY, dragStart.value.y);
    const width = Math.abs(currentX - dragStart.value.x);
    const height = Math.abs(currentY - dragStart.value.y);
    tempSelectionRect.value = { x, y, width, height };
};

const endSelection = () => {
    if (!isDragging.value) return;
    isDragging.value = false;
    if (tempSelectionRect.value.width < 5 || tempSelectionRect.value.height < 5) {
        tempSelectionRect.value = null; return;
    }
    newCommentData.value = { content: '', position: { ...tempSelectionRect.value }, type: 'area', page_number: currentPageNum.value };
    showCommentModal.value = true;
    tempSelectionRect.value = null;
};

// BARU: Menyimpan komentar via API Laravel
const saveComment = async () => {
    if (!newCommentData.value.content.trim()) return;
    
    const payload = {
        ...newCommentData.value,
        document_id: props.document.id,
    };

    try {
        const response = await axios.post('/api/comments', payload);
        // Tambahkan komentar baru ke state dengan data dari server
        comments.value.push(response.data);
        cancelComment();
    } catch (error) {
        console.error("Gagal menyimpan komentar:", error);
        alert('Terjadi kesalahan saat menyimpan komentar.');
    }
};

const cancelComment = () => {
    showCommentModal.value = false;
    newCommentData.value = { content: '', position: null, type: '', page_number: 0 };
};

// BARU: Mengubah status komentar via API
const toggleCommentStatus = async (comment) => {
    const newStatus = comment.status === 'open' ? 'done' : 'open';
    try {
        await axios.patch(`/api/comments/${comment.id}/status`, { status: newStatus });
        comment.status = newStatus; // Update state lokal jika berhasil
    } catch (error) {
        console.error("Gagal mengubah status:", error);
        alert('Terjadi kesalahan saat mengubah status.');
    }
};

const handleGoToComment = async (comment) => {
    if (comment.page_number !== currentPageNum.value) {
        await renderPage(comment.page_number);
    }
    highlightedCommentId.value = comment.id;
    setTimeout(() => { highlightedCommentId.value = null; }, 1500);
};

const commentsOnCurrentPage = computed(() => {
    return comments.value.filter(c => c.page_number === currentPageNum.value);
});

const filteredComments = computed(() => {
    const sorted = [...comments.value].sort((a, b) => a.page_number - b.page_number || a.id - b.id);
    if (commentFilter.value === 'all') return sorted;
    return sorted.filter(c => c.status === commentFilter.value);
});
</script>