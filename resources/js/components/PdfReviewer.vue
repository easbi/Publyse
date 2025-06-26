<!-- resources/js/components/PdfReviewer.vue -->
<template>
    <div class="flex h-[80vh] w-full gap-4" :class="{ 'no-select': isDragging }">

        <!-- Main Content: PDF Viewer -->
        <main class="flex-grow flex flex-col bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header Kontrol -->
            <header class="flex items-center justify-between p-3 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center gap-4">
                    <h1 class="text-xl font-bold text-gray-700">Versi: {{ document.version }}</h1>
                    <button @click="loginTest" class="bg-green-500 text-white px-4 py-2 rounded">Login Test</button>



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
                    <button @click="setAnnotationTool('point')" :class="annotationTool === 'point' ? 'bg-blue-500 text-white' : 'bg-white'" class="p-2 rounded-md hover:bg-blue-100" title="Komentar Titik"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg></button>
                    <button @click="setAnnotationTool('area')" :class="annotationTool === 'area' ? 'bg-blue-500 text-white' : 'bg-white'" class="p-2 rounded-md hover:bg-blue-100" title="Komentar Area"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm-5 0H6v3h4V4zM6 0v3H1v1h5V0zM1 4h4v3H1V4zm4 4H1v1h4V8zm-4 4h4v3H1v-3zm5 3v-3h4v3h-4zm5-3h4v3h-4v-3zM11 4h4V1h-4v3z"/></svg></button>
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
                <!-- Loop Komentar Utama -->
                <li v-for="comment in filteredComments" :key="comment.id" class="p-3 rounded-lg border border-gray-200" :class="comment.status === 'done' ? 'bg-green-50' : 'bg-white'">
                    <div class="flex justify-between items-start gap-3">
                        <div class="flex-grow">
                            <p class="text-xs font-semibold text-gray-600 cursor-pointer" @click="handleGoToComment(comment)">{{ comment.user.name }}</p>

                            <!-- Form Edit Tampil di sini jika editingComment aktif -->
                            <div v-if="editingComment && editingComment.id === comment.id">
                                <textarea v-model="commentEditText" class="w-full border rounded-md p-1 mt-1 text-sm"></textarea>
                                <div class="flex gap-2 mt-1">
                                    <button @click="saveEdit(comment)" class="text-xs text-white bg-blue-500 px-2 py-1 rounded-md">Simpan</button>
                                    <button @click="cancelEdit" class="text-xs text-gray-600">Batal</button>
                                </div>
                            </div>
                             <!-- Teks Komentar Tampil di sini -->
                            <p v-else class="text-sm text-gray-800 break-words cursor-pointer" :class="{'line-through text-gray-500': comment.status === 'done'}" @click="handleGoToComment(comment)">{{ comment.content }}</p>

                            <!-- Tombol Aksi -->
                            <div class="mt-2 flex items-center gap-3 text-xs text-gray-500">
                                <button @click="startReply(comment)" class="hover:underline">Balas</button>
                                <template v-if="currentUser.id === comment.user_id">
                                    <span>·</span>
                                    <button @click="startEdit(comment)" class="hover:underline">Edit</button>
                                    <span>·</span>
                                    <button @click="deleteComment(comment)" class="hover:underline text-red-500">Hapus</button>
                                </template>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span v-if="comment.page_number" class="text-xs font-semibold bg-gray-200 text-gray-700 px-2 py-1 rounded-full whitespace-nowrap cursor-pointer" @click="handleGoToComment(comment)">Hal. {{ comment.page_number }}</span>
                            <input type="checkbox" :checked="comment.status === 'done'" @change="toggleCommentStatus(comment)" @click.stop class="form-checkbox h-5 w-5 rounded text-green-600 transition duration-150 ease-in-out cursor-pointer">
                        </div>
                    </div>

                    <!-- Form untuk Balasan Baru -->
                    <div v-if="replyingToComment && replyingToComment.id === comment.id" class="mt-3 ml-6">
                        <textarea v-model="replyText" class="w-full border rounded-md p-1 text-sm" placeholder="Tulis balasan..."></textarea>
                        <div class="flex gap-2 mt-1">
                            <button @click="submitReply(comment)" class="text-xs text-white bg-blue-500 px-2 py-1 rounded-md">Kirim</button>
                            <button @click="cancelReply" class="text-xs text-gray-600">Batal</button>
                        </div>
                    </div>

                    <!-- Daftar Balasan (Replies) -->
                    <ul v-if="comment.replies && comment.replies.length > 0" class="mt-3 ml-6 pl-4 border-l-2 border-gray-200 space-y-3">
                        <li v-for="reply in comment.replies" :key="reply.id">
                            <p class="text-xs font-semibold text-gray-600">{{ reply.user.name }}</p>
                            <p class="text-sm text-gray-700">{{ reply.content }}</p>
                        </li>
                    </ul>

                </li>
            </ul>
        </aside>

        <!-- Modal Komentar Baru -->
        <div v-if="showCommentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
            <!-- Perbaikan: Ukuran modal diatur di sini -->
            <div class="bg-white rounded-lg shadow-xl p-4 w-full max-w-sm sm:max-w-xs">
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
    try {
        const res = await axios.post('/api/comments', {
            content: "Komentar dari frontend",
            position: JSON.stringify({ x: 10, y: 20 }),
            page_number: 1,
            document_id: props.document.id
        });
        console.log('✅ Komentar berhasil disimpan:', res.data);
    } catch (error) {
        console.error('❌ Gagal menyimpan komentar:', error.response?.status, error.message);
        alert('Gagal menyimpan komentar: ' + (error.response?.data?.message || error.message));
    }
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

const deleteComment = async (commentToDelete) => {
    if (confirm('Anda yakin ingin menghapus komentar ini dan semua balasannya?')) {
        try {
            const url = props.apiDeleteUrlTemplate.replace('COMMENT_ID', commentToDelete.id);
            await axios.delete(url);
            comments.value = comments.value.filter(c => c.id !== commentToDelete.id);
        } catch (error) {
            console.error("Gagal menghapus komentar:", error);
            alert('Gagal menghapus komentar.');
        }
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
    return comments.value.filter(c => c.type !== null && c.page_number === currentPageNum.value);
});

const filteredComments = computed(() => {
    const sorted = [...comments.value].sort((a, b) => a.page_number - b.page_number || a.id - b.id);
    if (commentFilter.value === 'all') return sorted;
    return sorted.filter(c => c.status === commentFilter.value);
});
</script>
