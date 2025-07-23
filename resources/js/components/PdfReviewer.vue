<!-- resources/js/components/PdfReviewer.vue -->
<template>
    <div class="flex w-full h-screen" :class="{ 'no-select': isDragging }">
        <!-- Main Content: PDF Viewer - 5/7 dari lebar layar -->
        <PdfViewerPanel
            :document="document"
            :pdf-doc="pdfDoc"
            :current-page-num="currentPageNum"
            :total-pages="totalPages"
            :scale="scale"
            :display-scale="displayScale"
            :is-loading="isLoading"
            :canvas-size="canvasSize"
            :annotation-tool="annotationTool"
            :temp-selection-rect="tempSelectionRect"
            :is-dragging="isDragging"
            :comments-on-current-page="commentsOnCurrentPage"
            :highlighted-comment-id="highlightedCommentId"
            :zoom-levels="zoomLevels"
            :min-scale="minScale"
            :max-scale="maxScale"
            @page-changed="handlePageChanged"
            @zoom-changed="handleZoomChanged"
            @annotation-tool-changed="setAnnotationTool"
            @annotation-mouse-down="handleAnnotationMouseDown"
            @annotation-mouse-move="handleAnnotationMouseMove"
            @annotation-mouse-up="handleAnnotationMouseUp"
            @layer-click="handleLayerClick"
            @wheel="handleWheel"
            @go-to-comment="handleGoToComment"
            @fit-to-width="fitToWidth"
            @fit-to-page="fitToPage"
            @zoom-in="zoomIn"
            @zoom-out="zoomOut"
            ref="pdfContainer"
        />

        <!-- Sidebar: Daftar Komentar - 2/7 dari lebar layar -->
        <CommentManagement
            :comments="hierarchicalComments"
            :current-user="currentUser"
            :creator-id="actualCreatorId"
            :active-comment-id="activeCommentId"
            :comment-filter="commentFilter"
            :displayed-comments="displayedComments"
            :current-pagination-page="currentPaginationPage"
            :comments-per-page="commentsPerPage"
            :editing-comment="editingComment"
            :comment-edit-text="commentEditText"
            :replying-to-comment="replyingToComment"
            :reply-text="replyText"
            :editing-reply="editingReply"
            :reply-edit-text="replyEditText"
            @filter-changed="applyFilter"
            @page-changed="goToPage"
            @previous-page="goToPreviousPage"
            @next-page="goToNextPage"
            @comment-selected="setActiveComment"
            @go-to-comment="handleGoToComment"
            @toggle-comment-status="toggleCommentStatus"
            @start-edit="startEdit"
            @save-edit="saveEdit"
            @cancel-edit="cancelEdit"
            @start-reply="startReply"
            @submit-reply="submitReply"
            @cancel-reply="cancelReply"
            @start-edit-reply="startEditReply"
            @save-edit-reply="saveEditReply"
            @cancel-edit-reply="cancelEditReply"
            @delete-comment="deleteComment"
            @delete-reply="deleteReply"
        />

        <!-- Modal Komentar Baru -->
        <div v-if="showCommentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
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
    /* Layout responsif dengan proporsi 5/7 dan 2/7 */
    .no-select {
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Comment styling dengan text wrapping yang kuat */
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
</style>

<script setup>
import { ref, onMounted, computed, watch, nextTick, markRaw, onUnmounted } from 'vue';
import * as pdfjsLib from 'pdfjs-dist';
import axios from 'axios';
import PdfViewerPanel from './PdfViewerPanel.vue';
import CommentManagement from './CommentManagement.vue';

let keydownHandler = null;
const setupKeyboardShortcuts = () => {
    keydownHandler = (event) => {
        if (event.ctrlKey || event.metaKey) {
            switch (event.key) {
                case '=':
                case '+':
                    event.preventDefault();
                    zoomIn();
                    break;
                case '-':
                    event.preventDefault();
                    zoomOut();
                    break;
                case '0':
                    event.preventDefault();
                    scale.value = 1.0;
                    renderPage(currentPageNum.value);
                    break;
            }
        }
    };
    document.addEventListener('keydown', keydownHandler);
};

const cleanupKeyboardShortcuts = () => {
    if (keydownHandler) {
        document.removeEventListener('keydown', keydownHandler);
        keydownHandler = null;
    }
};

// axios configuration
axios.defaults.baseURL = '/app/publyse/public/';
axios.defaults.withCredentials = true;

const props = defineProps({
    document: Object,
    currentUser: Object,
    creatorId: [Number, String],
    apiStoreUrl: String,
    apiUpdateUrlTemplate: String,
    apiDeleteUrlTemplate: String,
    apiStatusUrlTemplate: String,
});

const comments = ref(props.document.comments || []);
const pdfUrl = ref(props.document.pdf_url);

// PDF related refs
const pdfDoc = ref(null);
const currentPageNum = ref(1);
const totalPages = ref(0);
const scale = ref(1.5);
const displayScale = ref(1.0);
const isLoading = ref(false);
const canvasSize = ref({ width: 0, height: 0 });
const pdfContainer = ref(null);

// Zoom configuration
const minScale = ref(0.25);
const maxScale = ref(5.0);
const zoomStep = ref(0.25);

const zoomLevels = ref([
    { value: 0.25, label: '25%' },
    { value: 0.5, label: '50%' },
    { value: 0.75, label: '75%' },
    { value: 1.0, label: '100%' },
    { value: 1.25, label: '125%' },
    { value: 1.5, label: '150%' },
    { value: 2.0, label: '200%' },
    { value: 3.0, label: '300%' },
    { value: 4.0, label: '400%' },
    { value: 5.0, label: '500%' }
]);

// Annotation related refs
const annotationTool = ref('point');
const showCommentModal = ref(false);
const commentTextarea = ref(null);
const newCommentData = ref({ content: '', position: null, type: '', page_number: 0 });
const highlightedCommentId = ref(null);
const commentFilter = ref('all');

// Selection related refs
const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });
const tempSelectionRect = ref(null);

// Comment editing refs
const editingComment = ref(null);
const commentEditText = ref('');
const replyingToComment = ref(null);
const replyText = ref('');

// Reply editing refs
const editingReply = ref(null);
const replyEditText = ref('');

const activeCommentId = ref(null);

// Pagination refs
const currentPaginationPage = ref(1);
const commentsPerPage = ref(5);

// Comments state
const displayedComments = ref([]);

// Computed property untuk mendapatkan creator ID dari berbagai sumber
const actualCreatorId = computed(() => {
    // Prioritas: props.creatorId -> document fields -> extract dari comments
    let creatorId = props.creatorId ||
                   props.document?.user_id ||
                   props.document?.created_by ||
                   props.document?.owner_id ||
                   props.document?.owner?.id ||
                   props.document?.creator_id ||
                   props.document?.publisher_id;

    // Jika tidak ada, coba cari dari field lain yang mungkin
    if (!creatorId && props.document) {
        // Cari field yang mengandung 'user' atau 'creator' atau 'owner'
        const documentKeys = Object.keys(props.document);
        const possibleKeys = documentKeys.filter(key =>
            key.toLowerCase().includes('user') ||
            key.toLowerCase().includes('creator') ||
            key.toLowerCase().includes('owner') ||
            key.toLowerCase().includes('publisher')
        );

        for (const key of possibleKeys) {
            const value = props.document[key];
            if (value && (typeof value === 'number' || typeof value === 'string')) {
                creatorId = value;
                break;
            }
        }
    }
    return creatorId;
});

// Timestamp refresh interval
let timestampInterval = null;

// Function untuk membangun struktur hierarkis dari flat array
const buildCommentTree = (flatComments) => {
    const commentMap = new Map();
    const topLevelComments = [];

    flatComments.forEach(comment => {
        comment.replies = [];
        commentMap.set(comment.id, comment);
    });

    flatComments.forEach(comment => {
        if (comment.parent_id && commentMap.has(comment.parent_id)) {
            const parent = commentMap.get(comment.parent_id);
            parent.replies.push(comment);
        } else {
            topLevelComments.push(comment);
        }
    });

    return topLevelComments;
};

// Computed properties untuk komentar hierarkis
const hierarchicalComments = computed(() => {
    return buildCommentTree([...comments.value]);
});

const sortedComments = computed(() => {
    return hierarchicalComments.value.sort((a, b) => a.page_number - b.page_number || a.id - b.id);
});

// Fungsi untuk update data tanpa mengubah pagination
const updateDisplayedComments = () => {
    const filterType = commentFilter.value;

    if (filterType === 'all') {
        displayedComments.value = sortedComments.value;
    } else {
        // Filter hanya berdasarkan status komentar parent/utama
        // Semua replies mengikuti parent mereka
        displayedComments.value = sortedComments.value
            .filter(comment => comment.status === filterType)
            .map(comment => ({
                ...comment,
                // Tampilkan SEMUA replies dari parent yang lolos filter
                replies: comment.replies || []
            }));
    }
};

const commentsOnCurrentPage = computed(() => {
    const currentPageComments = hierarchicalComments.value.filter(c => {
        if (c.parent_id !== null) return false;
        if (c.type === null || c.page_number !== currentPageNum.value) {
            return false;
        }
        const position = parsePosition(c.position);
        if (!position) return false;

        if (c.type === 'point') {
            return typeof position.x === 'number' && typeof position.y === 'number';
        }
        if (c.type === 'area') {
            return typeof position.x === 'number' &&
                   typeof position.y === 'number' &&
                   typeof position.width === 'number' &&
                   typeof position.height === 'number';
        }
        return false;
    });
    return currentPageComments;
});

// Event handlers
const handlePageChanged = (pageNum) => {
    renderPage(pageNum);
};

const handleZoomChanged = (newScale) => {
    scale.value = newScale;
    renderPage(currentPageNum.value);
};

const setAnnotationTool = (tool) => {
    annotationTool.value = tool;
};

const handleAnnotationMouseDown = (event) => {
    startSelection(event);
};

const handleAnnotationMouseMove = (event) => {
    updateSelection(event);
};

const handleAnnotationMouseUp = (event) => {
    endSelection(event);
};

const handleLayerClick = (event) => {
    if (annotationTool.value !== 'point' || isDragging.value) return;
    newCommentData.value = {
        content: '',
        position: { x: event.offsetX, y: event.offsetY },
        type: 'point',
        page_number: currentPageNum.value
    };
    showCommentModal.value = true;
};

const handleWheel = (event) => {
    if (event.ctrlKey || event.metaKey) {
        event.preventDefault();
        const zoomDirection = event.deltaY < 0 ? 1 : -1;
        const currentIndex = zoomLevels.value.findIndex(level => level.value === scale.value);

        if (zoomDirection > 0 && currentIndex < zoomLevels.value.length - 1) {
            scale.value = zoomLevels.value[currentIndex + 1].value;
            renderPage(currentPageNum.value);
        } else if (zoomDirection < 0 && currentIndex > 0) {
            scale.value = zoomLevels.value[currentIndex - 1].value;
            renderPage(currentPageNum.value);
        }
    }
};

const handleGoToComment = async (comment) => {
    if (comment.page_number && comment.page_number !== currentPageNum.value) {
        await renderPage(comment.page_number);
    }
    highlightedCommentId.value = comment.id;
    setTimeout(() => {
        highlightedCommentId.value = null;
    }, 1500);
};

// Zoom Functions
const zoomIn = () => {
    const currentIndex = zoomLevels.value.findIndex(level => level.value === scale.value);
    if (currentIndex < zoomLevels.value.length - 1) {
        scale.value = zoomLevels.value[currentIndex + 1].value;
        renderPage(currentPageNum.value);
    }
};

const zoomOut = () => {
    const currentIndex = zoomLevels.value.findIndex(level => level.value === scale.value);
    if (currentIndex > 0) {
        scale.value = zoomLevels.value[currentIndex - 1].value;
        renderPage(currentPageNum.value);
    }
};

const fitToWidth = async () => {
    if (!pdfDoc.value || !pdfContainer.value?.$el) return;
    try {
        const page = await pdfDoc.value.getPage(currentPageNum.value);
        const viewport = page.getViewport({ scale: 1.0 });
        const containerWidth = pdfContainer.value.$el.clientWidth - 40;
        const newScale = containerWidth / viewport.width;
        scale.value = Math.max(minScale.value, Math.min(maxScale.value, newScale));
        renderPage(currentPageNum.value);
    } catch (error) {
        console.error("Error fitting to width:", error);
    }
};

const fitToPage = async () => {
    if (!pdfDoc.value || !pdfContainer.value?.$el) return;
    try {
        const page = await pdfDoc.value.getPage(currentPageNum.value);
        const viewport = page.getViewport({ scale: 1.0 });
        const containerWidth = pdfContainer.value.$el.clientWidth - 40;
        const containerHeight = pdfContainer.value.$el.clientHeight - 40;
        const scaleWidth = containerWidth / viewport.width;
        const scaleHeight = containerHeight / viewport.height;
        const newScale = Math.min(scaleWidth, scaleHeight);
        scale.value = Math.max(minScale.value, Math.min(maxScale.value, newScale));
        renderPage(currentPageNum.value);
    } catch (error) {
        console.error("Error fitting to page:", error);
    }
};

// Selection functions
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
        tempSelectionRect.value = null;
        return;
    }
    newCommentData.value = {
        content: '',
        position: { ...tempSelectionRect.value },
        type: 'area',
        page_number: currentPageNum.value
    };
    showCommentModal.value = true;
    tempSelectionRect.value = null;
};

// Comment functions - FIXED TO PRESERVE PAGINATION
const applyFilter = (filterType) => {
    commentFilter.value = filterType;
    currentPaginationPage.value = 1; // Reset pagination hanya saat filter berubah
    updateDisplayedComments();
};

const goToPage = (page) => {
    if (page >= 1) {
        currentPaginationPage.value = page;
    }
};

const goToPreviousPage = () => {
    if (currentPaginationPage.value > 1) {
        currentPaginationPage.value--;
    }
};

const goToNextPage = () => {
    currentPaginationPage.value++;
};

const setActiveComment = (comment) => {
    activeCommentId.value = comment.id;
};

const cancelComment = () => {
    showCommentModal.value = false;
    newCommentData.value = { content: '', position: null, type: '', page_number: 0 };
};

const saveComment = async () => {
    if (!newCommentData.value.content.trim()) return;
    const payload = { ...newCommentData.value, document_id: props.document.id };
    if (payload.position) {
        payload.position = JSON.stringify(payload.position);
    }

    try {
        const response = await axios.post(props.apiStoreUrl, payload);
        let newCommentFromServer = response.data;

        // Pastikan komentar memiliki semua field yang diperlukan
        newCommentFromServer = {
            ...newCommentFromServer,
            replies: [],
            parent_id: null,
            status: newCommentFromServer.status || 'open'
        };

        // Parse position untuk memastikan format yang benar
        newCommentFromServer.position = parsePosition(newCommentFromServer.position);

        // Tambahkan ke array comments original
        comments.value.push(newCommentFromServer);

        // Force reactivity update
        comments.value = [...comments.value];

        // Update displayed comments tanpa reset pagination
        updateDisplayedComments();

        cancelComment();
    } catch (error) {
        console.error("Gagal menyimpan komentar:", error);
        alert('Gagal menyimpan komentar.');
    }
};

const toggleCommentStatus = async (comment) => {
    const newStatus = comment.status === 'open' ? 'done' : 'open';
    try {
        const url = props.apiStatusUrlTemplate.replace('COMMENT_ID', comment.id);
        await axios.patch(url, { status: newStatus });
        comment.status = newStatus;
        comments.value = [...comments.value];

        // Update displayed comments tanpa reset pagination
        updateDisplayedComments();
    } catch (error) {
        console.error("Gagal mengubah status:", error);
        alert('Terjadi kesalahan saat mengubah status.');
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

const saveEdit = async (comment, newContent) => {
    if (!newContent || !newContent.trim()) return;
    try {
        const url = props.apiUpdateUrlTemplate.replace('COMMENT_ID', comment.id);
        const response = await axios.put(url, { content: newContent });

        // Update di data original comments
        const originalComment = comments.value.find(c => c.id === comment.id);
        if (originalComment) {
            originalComment.content = response.data.content;
            originalComment.updated_at = response.data.updated_at;
        }

        // Force reactivity update untuk data original
        comments.value = [...comments.value];

        // Update displayed comments tanpa reset pagination
        updateDisplayedComments();

        cancelEdit();
    } catch (error) {
        console.error("Gagal mengupdate komentar:", error);
        alert('Gagal mengupdate komentar.');
    }
};

const startEditReply = (reply) => {
    editingReply.value = reply;
    replyEditText.value = reply.content;
};

const cancelEditReply = () => {
    editingReply.value = null;
    replyEditText.value = '';
};

const saveEditReply = async (parentComment, reply, newContent) => {
    if (!newContent || !newContent.trim()) return;
    try {
        const url = props.apiUpdateUrlTemplate.replace('COMMENT_ID', reply.id);
        const response = await axios.put(url, { content: newContent });

        // Update di data original comments - cari reply yang tepat
        const originalParent = comments.value.find(c => c.id === parentComment.id);
        if (originalParent && originalParent.replies) {
            const originalReply = originalParent.replies.find(r => r.id === reply.id);
            if (originalReply) {
                originalReply.content = response.data.content;
                originalReply.updated_at = response.data.updated_at;
            }
        }

        // Force reactivity update untuk data original
        comments.value = [...comments.value];

        // Update displayed comments tanpa reset pagination
        updateDisplayedComments();

        cancelEditReply();
    } catch (error) {
        console.error("Gagal mengupdate balasan:", error);
        alert('Gagal mengupdate balasan.');
    }
};

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

const removeCommentFromArray = (commentsArray, commentId) => {
    return commentsArray.filter(c => c.id !== commentId);
};

const deleteComment = async (commentToDelete) => {
    if (confirm('Anda yakin ingin menghapus komentar ini?')) {
        try {
            const url = props.apiDeleteUrlTemplate.replace('COMMENT_ID', commentToDelete.id);
            await axios.delete(url);
            comments.value = removeCommentFromArray(comments.value, commentToDelete.id);

            // Update displayed comments tanpa reset pagination
            updateDisplayedComments();
        } catch (error) {
            console.error("Gagal menghapus komentar:", error);
            alert('Gagal menghapus komentar.');
        }
    }
};

const deleteReply = async (parentComment, reply) => {
    if (confirm('Anda yakin ingin menghapus balasan ini?')) {
        try {
            const url = props.apiDeleteUrlTemplate.replace('COMMENT_ID', reply.id);
            await axios.delete(url);
            comments.value = removeCommentFromArray(comments.value, reply.id);

            // Update displayed comments tanpa reset pagination
            updateDisplayedComments();
        } catch (error) {
            console.error("Gagal menghapus balasan:", error);
            alert('Gagal menghapus balasan.');
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

const submitReply = async (parentComment, newReplyText) => {
    if (!newReplyText || !newReplyText.trim()) {
        console.warn('Reply text is empty');
        return;
    }

    const payload = {
        content: newReplyText,
        document_id: props.document.id,
        parent_id: parentComment.id
    };

    try {
        const response = await axios.post(props.apiStoreUrl, payload);

        // Tambahkan reply ke array utama comments
        comments.value.push(response.data);

        // Update displayed comments tanpa reset pagination
        updateDisplayedComments();

        // Reset form
        cancelReply();
    } catch (error) {
        console.error("Gagal mengirim balasan:", error);

        if (error.response?.data?.message) {
            alert(`Gagal mengirim balasan: ${error.response.data.message}`);
        } else {
            alert('Gagal mengirim balasan.');
        }
    }
};

// PDF functions
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
    } catch (error) {
        console.error("Gagal merender halaman:", error);
    } finally {
        isLoading.value = false;
    }
};

// Watch for comments changes - update display tanpa reset pagination
watch(sortedComments, () => {
    updateDisplayedComments();
}, { immediate: true });

watch(showCommentModal, (isShowing) => {
    if (isShowing) nextTick(() => {
        commentTextarea.value?.focus();
    });
});

// PDF.js worker configuration
pdfjsLib.GlobalWorkerOptions.workerSrc = `https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js`;

onUnmounted(() => {
    if (timestampInterval) {
        clearInterval(timestampInterval);
    }
    cleanupKeyboardShortcuts();
});

onMounted(async () => {
    console.log("Component mounted - Pagination preserved version");

    const csrfAxios = axios.create();
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

    timestampInterval = setInterval(() => {
        if (comments.value.length > 0) {
            comments.value = [...comments.value];
        }
    }, 60000);

    setupKeyboardShortcuts();
});
</script>
