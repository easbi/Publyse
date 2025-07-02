<!-- resources/js/components/PdfReviewer.vue -->
<template>
    <div class="flex flex-col lg:grid lg:grid-cols-7 lg:gap-6 w-full p-4" :class="{ 'no-select': isDragging }">

        <!-- Main Content: PDF Viewer -->
        <main class="lg:col-span-5 flex flex-col bg-white rounded-lg shadow-lg overflow-hidden h-screen lg:h-auto" style="min-width: 0; flex-shrink: 0;">
            <!-- Header Kontrol -->
            <header class="flex items-center justify-between p-4 bg-white border-b border-gray-200">
                <div class="flex items-center gap-4">
                    <h1 class="text-xl font-bold text-gray-700">Versi: {{ document.version }}</h1>
                    <!-- <a :href="pdfUrl" target="_blank" class="text-sm text-blue-600 hover:underline">Buka PDF di Tab Baru</a> -->
                </div>

                <!-- Kontrol Navigasi PDF -->
                <div v-if="pdfDoc" class="flex items-center gap-3">
                    <button @click="changePage(-1)" :disabled="currentPageNum <= 1" class="px-3 py-2 bg-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300">&lt;</button>
                    <span>Halaman <input type="number" v-model.number="currentPageNumInput" @change="goToPage(currentPageNumInput)" class="w-14 text-center border rounded-md p-1"> / {{ totalPages }}</span>
                    <button @click="changePage(1)" :disabled="currentPageNum >= totalPages" class="px-3 py-2 bg-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300">&gt;</button>
                </div>

                <!-- Kontrol Alat Anotasi -->
                <div v-if="pdfDoc" class="flex items-center gap-2 border border-gray-300 rounded-lg p-1">
                    <button @click="setAnnotationTool('point')" :class="annotationTool === 'point' ? 'bg-blue-500 text-white' : 'bg-white'" class="p-2 rounded-md hover:bg-blue-100" title="Komentar Titik"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg></button>
                    <button @click="setAnnotationTool('area')" :class="annotationTool === 'area' ? 'bg-blue-500 text-white' : 'bg-white'" class="p-2 rounded-md hover:bg-blue-100" title="Komentar Area"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm-5 0H6v3h4V4zM6 0v3H1v1h5V0zM1 4h4v3H1V4zm4 4H1v1h4V8zm-4 4h4v3H1v-3zm5 3v-3h4v3h-4zm5-3h4v3h-4v-3zM11 4h4V1h-4v3z"/></svg></button>
                </div>
            </header>

            <!-- Area Tampilan PDF -->
            <div class="flex-grow p-4 overflow-auto flex justify-center items-start bg-gray-100" style="min-height: 500px;">
                <div v-if="isLoading" class="flex items-center justify-center h-full">
                    <span class="text-gray-500">Memuat PDF...</span>
                </div>
                <div v-if="pdfDoc" id="pdf-container" class="relative">
                    <canvas id="pdf-canvas"></canvas>
                    <svg id="annotation-layer"
                        @mousedown="startSelection"
                        @mousemove="updateSelection"
                        @mouseup="endSelection"
                        @click.self="handleLayerClick"
                        :width="canvasSize.width"
                        :height="canvasSize.height">

                        <g v-for="comment in commentsOnCurrentPage" :key="comment.id" @click="handleGoToComment(comment)">
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

        <!-- Sidebar: Daftar Komentar - UKURAN DIPERLEBAR 1,5x -->
        <aside class="lg:col-span-2 bg-white rounded-lg shadow-lg flex flex-col mt-4 lg:mt-0 h-[80vh] lg:h-screen">
        <!-- <aside class="w-120 flex-shrink-0 bg-white rounded-lg shadow-lg flex flex-col" style="height: 95vh;"> -->
            <div class="p-4 border-b flex-shrink-0">
                <h2 class="text-xl font-bold text-gray-700">Daftar Komentar</h2>
                <div class="mt-3 flex gap-2">
                    <button @click="applyFilter('all')" :class="{'bg-blue-500 text-white': commentFilter === 'all'}" class="flex-1 text-sm px-3 py-1 border rounded-md">Semua</button>
                    <button @click="applyFilter('open')" :class="{'bg-blue-500 text-white': commentFilter === 'open'}" class="flex-1 text-sm px-3 py-1 border rounded-md">Terbuka</button>
                    <button @click="applyFilter('done')" :class="{'bg-blue-500 text-white': commentFilter === 'done'}" class="flex-1 text-sm px-3 py-1 border rounded-md">Selesai</button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto">
                <div v-if="displayedComments.length === 0" class="flex-grow flex items-center justify-center p-4">
                    <p class="text-gray-500 text-center">Tidak ada komentar yang cocok dengan filter ini.</p>
                </div>
                <div v-else class="flex-grow min-h-0 overflow-hidden">
                    <ul class="h-full overflow-y-auto p-3 space-y-3" ref="commentListEl">
                        <!-- Loop Komentar Utama -->
                        <li v-for="comment in displayedComments"
                            :key="comment.id"
                            :ref="el => { if (el) commentElements[comment.id] = el }"
                            @click="setActiveComment(comment)"
                            class="comment-item border transition-colors rounded-lg overflow-hidden"
                            :class="[
                                comment.status === 'done' ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200',
                                comment.id === activeCommentId ? 'border-yellow-400 bg-yellow-50' : 'cursor-pointer hover:bg-blue-50'
                            ]">

                            <div class="p-4">
                                <!-- Header dengan nama user dan halaman -->
                                <div class="flex justify-between items-start gap-3 mb-3">
                                    <div class="comment-header">
                                        <p class="text-sm font-semibold text-gray-600 cursor-pointer" @click="handleGoToComment(comment)">
                                            {{ comment.user.name }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span v-if="comment.page_number"
                                              class="text-xs font-semibold bg-gray-200 text-gray-700 px-2 py-1 rounded-full whitespace-nowrap cursor-pointer"
                                              @click="handleGoToComment(comment)">
                                            Hal. {{ comment.page_number }}
                                        </span>
                                        <input type="checkbox"
                                               :checked="comment.status === 'done'"
                                               @change="toggleCommentStatus(comment)"
                                               @click.stop
                                               class="form-checkbox h-4 w-4 rounded text-green-600 transition duration-150 ease-in-out cursor-pointer">
                                    </div>
                                </div>

                                <!-- Konten Komentar -->
                                <div class="comment-content-wrapper">
                                    <!-- Form Edit -->
                                    <div v-if="editingComment && editingComment.id === comment.id" class="mb-3">
                                        <textarea
                                            v-model="commentEditText"
                                            class="comment-textarea w-full border rounded-md p-3 text-sm resize-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                                            rows="3">
                                        </textarea>
                                        <div class="flex gap-2 mt-2">
                                            <button @click="saveEdit(comment)" class="text-xs text-white bg-blue-500 px-3 py-1 rounded-md hover:bg-blue-600">Simpan</button>
                                            <button @click="cancelEdit" class="text-xs text-gray-600 hover:text-gray-800">Batal</button>
                                        </div>
                                    </div>

                                    <!-- Teks Komentar -->
                                    <div v-else class="comment-content cursor-pointer mb-3"
                                         :class="{'line-through text-gray-500': comment.status === 'done'}"
                                         @click="handleGoToComment(comment)">
                                        {{ comment.content }}
                                    </div>
                                </div>

                                <!-- Tombol Aksi -->
                                <div class="flex items-center gap-3 text-xs text-gray-500 border-t pt-2">
                                    <button @click="startReply(comment)" class="hover:underline hover:text-blue-600">Balas</button>
                                    <template v-if="currentUser.id === comment.user_id">
                                        <span>·</span>
                                        <button @click="startEdit(comment)" class="hover:underline hover:text-blue-600">Edit</button>
                                        <span>·</span>
                                        <button @click="deleteComment(comment)" class="hover:underline text-red-500 hover:text-red-700">Hapus</button>
                                    </template>
                                </div>
                            </div>

                            <!-- Form untuk Balasan Baru -->
                            <div v-if="replyingToComment && replyingToComment.id === comment.id" class="px-4 pb-4">
                                <div class="ml-4 pl-4 border-l-2 border-gray-200">
                                    <textarea
                                        v-model="replyText"
                                        class="comment-textarea w-full border rounded-md p-3 text-sm focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                                        placeholder="Tulis balasan..."
                                        rows="2">
                                    </textarea>
                                    <div class="flex gap-2 mt-2">
                                        <button @click="submitReply(comment)" class="text-xs text-white bg-blue-500 px-3 py-1 rounded-md hover:bg-blue-600">Kirim</button>
                                        <button @click="cancelReply" class="text-xs text-gray-600 hover:text-gray-800">Batal</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Balasan (Replies) -->
                            <div v-if="comment.replies && comment.replies.length > 0" class="px-4 pb-4">
                                <div class="ml-4 pl-4 border-l-2 border-gray-200 space-y-2">
                                    <div v-for="reply in comment.replies" :key="reply.id" class="bg-gray-50 p-3 rounded-md">
                                        <p class="text-xs font-semibold text-gray-600 mb-1">{{ reply.user.name }}</p>
                                        <div class="comment-content text-sm text-gray-700">{{ reply.content }}</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Modal Komentar Baru -->
        <div v-if="showCommentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <!-- Modal yang diperpanjang 2x -->
            <div class="bg-white rounded-lg shadow-xl p-6 w-[60rem] max-w-[90vw]">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Komentar Baru</h3>
                <textarea
                    v-model="newCommentData.content"
                    ref="commentTextarea"
                    class="comment-textarea w-full border border-gray-300 rounded-md p-4 text-sm resize-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    placeholder="Tulis komentar..."
                    rows="4">
                </textarea>
                <div class="mt-6 flex justify-end gap-4">
                    <button @click="cancelComment" class="px-6 py-3 bg-gray-300 rounded-md hover:bg-gray-400 text-sm">Batal</button>
                    <button @click="saveComment" class="px-6 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    /* === AREA PERBAIKAN DIMULAI DI SINI === */
    #pdf-container {
        position: relative;
    }
    #annotation-layer {
        position: absolute;
        top: 0;
        left: 0;
        cursor: crosshair;
        z-index: 10;
    }

    /* CSS untuk text wrapping yang SANGAT KUAT */
    .comment-content {
        word-wrap: break-word !important;
        word-break: break-all !important;
        overflow-wrap: anywhere !important;
        hyphens: auto;
        line-height: 1.5;
        white-space: pre-wrap;
        max-width: 100% !important;
        overflow: hidden !important;
        font-size: 0.875rem; /* 14px */
    }

    .comment-item {
        min-width: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
        box-sizing: border-box;
    }

    .comment-content-wrapper {
        min-width: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
        overflow: hidden;
    }

    .comment-header {
        min-width: 0;
        flex: 1;
    }

    /* Textarea dengan wrapping yang baik */
    .comment-textarea {
        word-wrap: break-word !important;
        word-break: break-all !important;
        overflow-wrap: anywhere !important;
        white-space: pre-wrap;
        resize: vertical;
        min-height: 60px;
        max-width: 100% !important;
        box-sizing: border-box;
    }

    /* Memastikan sidebar LEBIH LEBAR 1.5x - dari 320px jadi 480px */
    aside {
        min-width: 480px !important;
        max-width: 480px !important;
        width: 480px !important;
    }

    /* Utility class untuk width 480px (w-120 dalam Tailwind = 480px) */
    .w-120 {
        width: 30rem; /* 480px */
    }

    /* === AREA PERBAIKAN SELESAI === */

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

    /* Pastikan scrollbar terlihat */
    .overflow-y-auto {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }

    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f7fafc;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }
</style>

<script setup>
import { ref, onMounted, computed, watch, nextTick, markRaw } from 'vue';
import * as pdfjsLib from 'pdfjs-dist';
import axios from 'axios';

// resources/js/bootstrap.js atau sebelum axios dipakai
axios.defaults.baseURL = '/app/publyse/public/';
axios.defaults.withCredentials = true;

const loginTest = async () => {
    try {
        const res = await axios.post('/login', {
            email: 'user@example.com',
            password: 'password'
        });
        console.log('✅ Login berhasil:', res.data);
    } catch (error) {
        console.error('❌ Gagal login:', error.response?.status, error.message);
    }
};

const props = defineProps({
    document: Object,
    currentUser: Object,
    apiStoreUrl: String,
    apiUpdateUrlTemplate: String,
    apiDeleteUrlTemplate: String,
    apiStatusUrlTemplate: String,
});

const comments = ref(props.document.comments || []);
// Perbaikan: Gunakan URL dari props yang sudah diperbaiki di backend
const pdfUrl = ref(props.document.pdf_url);
const BASE_API = 'http://localhost/app/publyse/public';

const pdfDoc = ref(null);
const currentPageNum = ref(1);
const currentPageNumInput = ref(1);
const totalPages = ref(0);
const scale = ref(1.5);
const isLoading = ref(false);
const canvasSize = ref({ width: 0, height: 0 });

const annotationTool = ref('point');
const showCommentModal = ref(false);
const commentTextarea = ref(null);
const newCommentData = ref({ content: '', position: null, type: '', page_number: 0 });
const highlightedCommentId = ref(null);
const commentFilter = ref('all');

const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });
const tempSelectionRect = ref(null);

const editingComment = ref(null);
const commentEditText = ref('');
const replyingToComment = ref(null);
const replyText = ref('');

const activeCommentId = ref(null);
const commentListEl = ref(null);
const commentElements = ref({});

// 1. State baru untuk menampung komentar yang akan ditampilkan
const displayedComments = ref([]);
// 2. Computed property ini sekarang hanya untuk mengurutkan, bukan memfilter
const sortedComments = computed(() => {
    return [...comments.value].sort((a, b) => a.page_number - b.page_number || a.id - b.id);
});

// 3. Fungsi baru untuk menerapkan filter secara manual
const applyFilter = (filterType) => {
    commentFilter.value = filterType; // Update status filter aktif

    if (filterType === 'all') {
        displayedComments.value = sortedComments.value;
    } else {
        displayedComments.value = sortedComments.value.filter(c => c.status === filterType);
    }
};
// 4. Gunakan 'watch' untuk menerapkan filter secara otomatis saat data siap
watch(sortedComments, (newSortedList) => {
    // Saat daftar komentar utama (sortedComments) siap atau berubah,
    // terapkan kembali filter yang sedang aktif.
    applyFilter(commentFilter.value);
}, { immediate: true }); // 'immediate: true' akan menjalankan watcher ini sekali saat komponen dimuat

pdfjsLib.GlobalWorkerOptions.workerSrc = `https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js`;

onMounted(async () => {
    console.log("Component mounted");

    const csrfAxios = axios.create(); // tidak ada baseURL
    try {
        const res = await csrfAxios.get('http://localhost/app/publyse/public/sanctum/csrf-cookie', {
            withCredentials: true
        });
        console.log('✅ CSRF OK:', res.status);
    } catch (err) {
        console.error('❌ CSRF error:', err.response?.status, err.message);
    }

    nextTick(() => {
        loadPdfFromUrl();
    });
});

watch(showCommentModal, (isShowing) => { if (isShowing) nextTick(() => { commentTextarea.value?.focus(); }); });

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

const setAnnotationTool = (tool) => {
    annotationTool.value = tool;
};

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

const cancelComment = () => {
    showCommentModal.value = false;
    newCommentData.value = { content: '', position: null, type: '', page_number: 0 };
};

const toggleCommentStatus = async (comment) => {
    const newStatus = comment.status === 'open' ? 'done' : 'open';
    try {
        const url = props.apiStatusUrlTemplate.replace('COMMENT_ID', comment.id);
        await axios.patch(url, { status: newStatus });
        comment.status = newStatus; // Cukup update statusnya, biarkan tetap di list
    } catch (error) {
        console.error("Gagal mengubah status:", error);
        alert('Terjadi kesalahan saat mengubah status.');
    }
};

const handleGoToComment = async (comment) => {
    if (comment.page_number && comment.page_number !== currentPageNum.value) {
        await renderPage(comment.page_number);
    }
    highlightedCommentId.value = comment.id;
    setTimeout(() => { highlightedCommentId.value = null; }, 1500);
};

// --- FUNGSI DIPERBAIKI ---

const saveComment = async () => {
    if (!newCommentData.value.content.trim()) return;
    const payload = { ...newCommentData.value, document_id: props.document.id };
    if (payload.position) {
        payload.position = JSON.stringify(payload.position);
    }
    try {
        const response = await axios.post(props.apiStoreUrl, payload);
        let newCommentFromServer = response.data;
        newCommentFromServer.position = parsePosition(newCommentFromServer.position);
        comments.value.push(newCommentFromServer);
        // applyFilter(commentFilter.value); // Tidak perlu lagi, 'watch' akan menanganinya
        cancelComment();
    } catch (error) { /* ... */ }
};

const setActiveComment = (comment) => {
    activeCommentId.value = comment.id;
};

const startEdit = (comment) => {
    editingComment.value = comment;
    commentEditText.value = comment.content;
};

const cancelEdit = () => {
    editingComment.value = null;
    commentEditText.value = '';
};

const saveEdit = async (comment) => {
    if (!commentEditText.value.trim()) return;
    try {
        const url = props.apiUpdateUrlTemplate.replace('COMMENT_ID', comment.id);
        const response = await axios.put(url, { content: commentEditText.value });
        comment.content = response.data.content;
        cancelEdit();
    } catch (error) {
        console.error("Gagal mengupdate komentar:", error);
        alert('Gagal mengupdate komentar.');
    }
};

const parsePosition = (positionData) => {
    if (!positionData) return null;

    try {
        // Jika sudah berupa object
        if (typeof positionData === 'object') {
            return positionData;
        }

        // Jika berupa string JSON
        if (typeof positionData === 'string') {
            return JSON.parse(positionData);
        }

        return null;
    } catch (error) {
        console.error('Error parsing position:', error);
        return null;
    }
};

const debugComments = () => {
    console.log('All comments:', comments.value);
    comments.value.forEach((comment, index) => {
        console.log(`Comment ${index}:`, {
            id: comment.id,
            type: comment.type,
            position_raw: comment.position,
            position_parsed: parsePosition(comment.position),
            page_number: comment.page_number
        });
    });
};

// Fungsi baru yang bisa mencari dan menghapus komentar dari mana saja (induk atau balasan)
const removeCommentFromTree = (commentsArray, commentId) => {
    // Coba hapus dari level saat ini
    const filtered = commentsArray.filter(c => c.id !== commentId);

    // Jika tidak ada yang terhapus, berarti komentar ada di dalam balasan.
    // Kita perlu mencari di dalam setiap balasan.
    if (filtered.length === commentsArray.length) {
        return commentsArray.map(parentComment => {
            if (parentComment.replies && parentComment.replies.length > 0) {
                // Panggil fungsi ini lagi untuk array balasan (rekursif)
                parentComment.replies = removeCommentFromTree(parentComment.replies, commentId);
            }
            return parentComment;
        });
    }

    // Jika komentar ditemukan di level ini, kembalikan array yang sudah difilter.
    return filtered;
};

const deleteComment = async (commentToDelete) => {
    if (confirm('Anda yakin ingin menghapus komentar ini?')) {
        try {
            const url = props.apiDeleteUrlTemplate.replace('COMMENT_ID', commentToDelete.id);
            await axios.delete(url);
            comments.value = removeCommentFromTree(comments.value, commentToDelete.id);
            // applyFilter(commentFilter.value); // Tidak perlu lagi, 'watch' akan menanganinya
        } catch (error) { /* ... */ }
    }
};

const startReply = (comment) => {
    replyingToComment.value = comment;
    replyText.value = '';
};

const cancelReply = () => {
    replyingToComment.value = null;
    replyText.value = '';
};

const submitReply = async (parentComment) => {
    if (!replyText.value.trim()) return;
    const payload = {
        content: replyText.value,
        document_id: props.document.id,
        parent_id: parentComment.id
    };
    try {
        const response = await axios.post(props.apiStoreUrl, payload);
        if (!parentComment.replies) {
            parentComment.replies = [];
        }
        parentComment.replies.push(response.data);
        cancelReply();
    } catch (error) {
        console.error("Gagal mengirim balasan:", error);
        alert('Gagal mengirim balasan.');
    }
};

const commentsOnCurrentPage = computed(() => {
    return comments.value.filter(c => {
        if (c.type === null || c.page_number !== currentPageNum.value) {
            return false;
        }

        const position = parsePosition(c.position);
        if (!position) return false;

        // Validasi koordinat untuk point
        if (c.type === 'point') {
            return typeof position.x === 'number' && typeof position.y === 'number';
        }

        // Validasi koordinat untuk area
        if (c.type === 'area') {
            return typeof position.x === 'number' &&
                   typeof position.y === 'number' &&
                   typeof position.width === 'number' &&
                   typeof position.height === 'number';
        }

        return false;
    });
});

const filteredComments = computed(() => {
    const sorted = [...comments.value].sort((a, b) => a.page_number - b.page_number || a.id - b.id);
    if (commentFilter.value === 'all') return sorted;
    return sorted.filter(c => c.status === commentFilter.value);
});
</script>
