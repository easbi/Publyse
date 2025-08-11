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

                <!-- Info Scale saat membuat komentar -->
                <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-blue-700">
                            Komentar ini dibuat pada zoom <strong>{{ Math.round(scale * 100) }}%</strong> halaman {{ currentPageNum }}
                        </span>
                    </div>
                    <p class="text-xs text-blue-600 mt-1">
                        Posisi akan tersimpan akurat untuk zoom level ini
                    </p>
                </div>

                <textarea
                    v-model="newCommentData.content"
                    ref="commentTextarea"
                    class="comment-textarea w-full border border-gray-300 rounded-md p-4 text-sm resize-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    placeholder="Tulis komentar..."
                    rows="4">
                </textarea>
                <div class="mt-6 flex justify-end gap-4">
                    <button @click="cancelComment" class="px-6 py-3 bg-gray-300 rounded-md hover:bg-gray-400 text-sm">Batal</button>
                    <button @click="saveComment" :disabled="isSubmittingComment" class="px-6 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm disabled:opacity-50">
                        {{ isSubmittingComment ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast Notification untuk Scale Changes -->
        <div v-if="showScaleToast"
             class="fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform z-50"
             :class="showScaleToast ? 'translate-y-0 opacity-100' : 'translate-y-2 opacity-0'">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ scaleToastMessage }}</span>
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

// Scale toast notification
const showScaleToast = ref(false);
const scaleToastMessage = ref('');

// Comment submission state
const isSubmittingComment = ref(false);

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

// ============= SCALE-AWARE FUNCTIONS =============

// Fungsi untuk normalisasi posisi ke scale 1.0
const normalizePosition = (position, currentScale) => {
    if (!position || !currentScale || currentScale === 0) return position;

    return {
        x: position.x / currentScale,
        y: position.y / currentScale,
        width: position.width ? position.width / currentScale : undefined,
        height: position.height ? position.height / currentScale : undefined
    };
};

// Fungsi untuk denormalisasi posisi ke scale tertentu
const denormalizePosition = (normalizedPosition, targetScale) => {
    if (!normalizedPosition || !targetScale) return normalizedPosition;

    return {
        x: normalizedPosition.x * targetScale,
        y: normalizedPosition.y * targetScale,
        width: normalizedPosition.width ? normalizedPosition.width * targetScale : undefined,
        height: normalizedPosition.height ? normalizedPosition.height * targetScale : undefined
    };
};

// Fungsi untuk menampilkan toast notification scale
const showScaleToastMessage = (message, duration = 3000) => {
    scaleToastMessage.value = message;
    showScaleToast.value = true;
    setTimeout(() => {
        showScaleToast.value = false;
    }, duration);
};

// Fungsi untuk scroll ke posisi komentar
const scrollToComment = (comment) => {
    if (!comment.position) return;

    const position = parsePosition(comment.position);
    if (!position) return;

    // Hitung posisi scroll berdasarkan posisi komentar
    const pdfContainer = document.getElementById('pdf-container');
    const pdfContainerParent = pdfContainer?.parentElement;

    if (pdfContainer && pdfContainerParent) {
        // Hitung offset scroll untuk center komentar di viewport
        const commentCenterX = position.x + (position.width || 0) / 2;
        const commentCenterY = position.y + (position.height || 0) / 2;

        const containerRect = pdfContainerParent.getBoundingClientRect();
        const targetScrollLeft = commentCenterX - containerRect.width / 2;
        const targetScrollTop = commentCenterY - containerRect.height / 2;

        pdfContainerParent.scrollTo({
            left: Math.max(0, targetScrollLeft),
            top: Math.max(0, targetScrollTop),
            behavior: 'smooth'
        });
    }
};

// ============= COMPUTED PROPERTIES =============

// Computed property untuk mendapatkan creator ID dari berbagai sumber
const actualCreatorId = computed(() => {
    let creatorId = props.creatorId ||
                   props.document?.user_id ||
                   props.document?.created_by ||
                   props.document?.owner_id ||
                   props.document?.owner?.id ||
                   props.document?.creator_id ||
                   props.document?.publisher_id;

    if (!creatorId && props.document) {
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

// UPDATED: Computed property untuk komentar di halaman saat ini dengan scale adjustment
const commentsOnCurrentPage = computed(() => {
    const currentPageComments = hierarchicalComments.value.filter(c => {
        if (c.parent_id !== null) return false;
        if (c.type === null || c.page_number !== currentPageNum.value) {
            return false;
        }

        // Parse posisi dengan mempertimbangkan scale
        let position = parsePosition(c.position);
        if (!position) return false;

        // SCALE AWARENESS: Adjust posisi berdasarkan scale
        if (c.created_at_scale && c.created_at_scale !== scale.value) {
            // Normalisasi dulu ke scale 1.0, lalu denormalisasi ke scale saat ini
            const normalizedPos = normalizePosition(position, c.created_at_scale);
            position = denormalizePosition(normalizedPos, scale.value);

            // Update posisi di objek komentar untuk rendering
            c.adjusted_position = position;
        } else {
            c.adjusted_position = position;
        }

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

// Timestamp refresh interval
let timestampInterval = null;

// Fungsi untuk update data tanpa mengubah pagination
const updateDisplayedComments = () => {
    const filterType = commentFilter.value;

    if (filterType === 'all') {
        displayedComments.value = sortedComments.value;
    } else {
        displayedComments.value = sortedComments.value
            .filter(comment => comment.status === filterType)
            .map(comment => ({
                ...comment,
                replies: comment.replies || []
            }));
    }
};

// ============= EVENT HANDLERS =============

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

// UPDATED: handleGoToComment dengan auto-adjust scale
const handleGoToComment = async (comment) => {
    try {
        console.log('Navigating to comment:', {
            id: comment.id,
            page: comment.page_number,
            current_scale: scale.value,
            comment_scale: comment.created_at_scale
        });

        // Pindah ke halaman yang tepat
        if (comment.page_number && comment.page_number !== currentPageNum.value) {
            await renderPage(comment.page_number);
        }

        // FITUR BARU: Auto-adjust ke scale yang sama dengan saat komentar dibuat
        if (comment.created_at_scale && comment.created_at_scale !== scale.value) {
            const oldScale = Math.round(scale.value * 100);
            const newScale = Math.round(comment.created_at_scale * 100);

            console.log(`Auto-adjusting scale from ${oldScale}% to ${newScale}% for accurate positioning`);

            showScaleToastMessage(
                `Zoom disesuaikan ke ${newScale}% (saat komentar dibuat)`
            );

            scale.value = comment.created_at_scale;
            await renderPage(currentPageNum.value);

            // Tunggu sebentar untuk render selesai
            await nextTick();
            setTimeout(() => {
                highlightedCommentId.value = comment.id;
                scrollToComment(comment);

                setTimeout(() => {
                    highlightedCommentId.value = null;
                }, 2500); // Highlight lebih lama
            }, 200);
        } else {
            // Jika scale sudah sama, langsung highlight
            highlightedCommentId.value = comment.id;
            scrollToComment(comment);

            setTimeout(() => {
                highlightedCommentId.value = null;
            }, 1500);
        }

    } catch (error) {
        console.error("Error navigating to comment:", error);
        showScaleToastMessage("Error navigating to comment", 3000);
    }
};

// ============= ZOOM FUNCTIONS =============

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

// ============= SELECTION FUNCTIONS =============

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

// ============= COMMENT FUNCTIONS =============

const applyFilter = (filterType) => {
    commentFilter.value = filterType;
    currentPaginationPage.value = 1;
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
    isSubmittingComment.value = false;
};

// UPDATED: saveComment dengan scale info dan robust error handling

// DEBUGGING FRONTEND REQUEST - CEK NETWORK TAB

// Mari kita debug step by step mengapa scale tidak terkirim

// 1. TAMBAHKAN DEBUGGING EKSTENSIF DI FRONTEND
// Update function saveComment di PdfReviewer.vue:

const saveComment = async () => {
    console.log('=== SAVE COMMENT START DEBUG ===');

    if (!newCommentData.value.content.trim()) {
        alert('Silakan isi konten komentar terlebih dahulu.');
        return;
    }

    if (isSubmittingComment.value) return;
    isSubmittingComment.value = true;

    try {
        // DEBUGGING SCALE VALUE
        console.log('1. Raw scale ref:', scale);
        console.log('2. scale.value:', scale.value);
        console.log('3. typeof scale.value:', typeof scale.value);

        // Ensure scale is a valid number
        let scaleValue = scale.value;
        console.log('4. Initial scaleValue:', scaleValue);

        if (typeof scaleValue === 'string') {
            scaleValue = parseFloat(scaleValue);
            console.log('5. Converted from string:', scaleValue);
        }

        if (!scaleValue || scaleValue <= 0 || scaleValue > 10) {
            console.warn('6. Invalid scale, using default 1.0. Original:', scaleValue);
            scaleValue = 1.0;
        }

        console.log('7. Final scaleValue:', scaleValue);
        console.log('8. typeof final scaleValue:', typeof scaleValue);

        // DEBUGGING CANVAS SIZE
        console.log('9. canvasSize.value:', canvasSize.value);
        let canvasWidth = canvasSize.value.width || 800;
        let canvasHeight = canvasSize.value.height || 600;
        console.log('10. Canvas dimensions:', { width: canvasWidth, height: canvasHeight });

        // DEBUGGING POSITION
        console.log('11. newCommentData.value:', newCommentData.value);
        console.log('12. Position:', newCommentData.value.position);

        // PREPARE PAYLOAD WITH EXPLICIT VALUES
        const payload = {
            document_id: props.document.id,
            content: newCommentData.value.content,
            page_number: newCommentData.value.page_number,
            type: newCommentData.value.type,
            position: JSON.stringify(newCommentData.value.position),
            // EXPLICIT SCALE VALUE
            created_at_scale: scaleValue,
            page_dimensions: {
                width: canvasWidth,
                height: canvasHeight
            },
            original_position: JSON.stringify(newCommentData.value.position)
        };

        console.log('13. PAYLOAD SEBELUM DIKIRIM:');
        console.log(JSON.stringify(payload, null, 2));

        // CEK SETIAP FIELD PAYLOAD
        console.log('14. Payload field check:');
        Object.keys(payload).forEach(key => {
            console.log(`   ${key}:`, payload[key], `(${typeof payload[key]})`);
        });

        console.log('15. API URL:', props.apiStoreUrl);
        console.log('16. Axios defaults:', axios.defaults);

        // SEND REQUEST WITH DETAILED LOGGING
        console.log('17. Sending request...');
        const response = await axios.post(props.apiStoreUrl, payload);

        console.log('18. Response status:', response.status);
        console.log('19. Response headers:', response.headers);
        console.log('20. Response data:', response.data);
        console.log('21. Response scale field:', response.data.created_at_scale);
        console.log('22. typeof response scale:', typeof response.data.created_at_scale);

        // Process response...
        let newCommentFromServer = response.data;

        newCommentFromServer = {
            ...newCommentFromServer,
            replies: [],
            parent_id: null,
            status: newCommentFromServer.status || 'open',
            created_at_scale: newCommentFromServer.created_at_scale || scaleValue
        };

        newCommentFromServer.position = parsePosition(newCommentFromServer.position);
        comments.value.push(newCommentFromServer);
        comments.value = [...comments.value];

        updateDisplayedComments();
        cancelComment();

        showScaleToastMessage(`Komentar tersimpan dengan zoom ${Math.round(scaleValue * 100)}%`);

        console.log('23. Final comment added:', newCommentFromServer);
        console.log('=== SAVE COMMENT END DEBUG ===');

    } catch (error) {
        console.error("=== ERROR DETAILS ===");
        console.error("Error object:", error);
        console.error("Response data:", error.response?.data);
        console.error("Response status:", error.response?.status);
        console.error("Response headers:", error.response?.headers);
        console.error("Request config:", error.config);
        console.error("Request data sent:", error.config?.data);
        console.error("=== END ERROR ===");

        let errorMessage = 'Gagal menyimpan komentar.';
        if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        } else if (error.response?.status === 403) {
            errorMessage = 'Batas waktu pemeriksaan telah berakhir.';
        } else if (error.response?.status === 422) {
            errorMessage = 'Data tidak valid. Periksa kembali input Anda.';
        }

        alert(errorMessage);

    } finally {
        isSubmittingComment.value = false;
    }
};

const toggleCommentStatus = async (comment) => {
    const newStatus = comment.status === 'open' ? 'done' : 'open';
    try {
        const url = props.apiStatusUrlTemplate.replace('COMMENT_ID', comment.id);
        await axios.patch(url, { status: newStatus });
        comment.status = newStatus;
        comments.value = [...comments.value];
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

        comments.value = [...comments.value];
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

        comments.value = [...comments.value];
        updateDisplayedComments();
        cancelEditReply();
    } catch (error) {
        console.error("Gagal mengupdate balasan:", error);
        alert('Gagal mengupdate balasan.');
    }
};

// UPDATED: parsePosition dengan support untuk adjusted position
const parsePosition = (positionData, comment = null) => {
    // Prioritaskan adjusted_position jika ada
    if (comment?.adjusted_position) {
        return comment.adjusted_position;
    }

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
        comments.value.push(response.data);
        updateDisplayedComments();
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

// ============= PDF FUNCTIONS =============

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

// ============= WATCHERS =============

// Watch for comments changes - update display tanpa reset pagination
watch(sortedComments, () => {
    updateDisplayedComments();
}, { immediate: true });

watch(showCommentModal, (isShowing) => {
    if (isShowing) nextTick(() => {
        commentTextarea.value?.focus();
    });
});

// Watch scale changes dengan validation
watch(scale, (newScale, oldScale) => {
    if (oldScale && newScale !== oldScale) {
        console.log(`Scale changed from ${oldScale} to ${newScale}`);

        // Ensure scale is always a number
        if (typeof newScale === 'string') {
            console.warn('Scale is string, converting to number');
            const numScale = parseFloat(newScale);
            if (!isNaN(numScale) && numScale > 0 && numScale <= 10) {
                scale.value = numScale;
            }
        }
    }
}, { immediate: false });

// ============= LIFECYCLE HOOKS =============

// PDF.js worker configuration
pdfjsLib.GlobalWorkerOptions.workerSrc = `https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js`;

onUnmounted(() => {
    if (timestampInterval) {
        clearInterval(timestampInterval);
    }
    cleanupKeyboardShortcuts();
});

onMounted(async () => {
    console.log("PdfReviewer mounted - Scale-Aware Comments enabled");
    console.log("Initial scale:", scale.value);
    console.log("Document:", props.document);
    console.log("API Store URL:", props.apiStoreUrl);

    // CSRF configuration
    const csrfAxios = axios.create();
    try {
        const res = await csrfAxios.get('http://localhost/app/publyse/public/sanctum/csrf-cookie', {
            withCredentials: true
        });
        console.log('✅ CSRF OK:', res.status);
    } catch (err) {
        console.error('❌ CSRF error:', err.response?.status, err.message);
    }

    // Load PDF
    nextTick(() => {
        loadPdfFromUrl();
    });

    // Setup timestamp refresh
    timestampInterval = setInterval(() => {
        if (comments.value.length > 0) {
            comments.value = [...comments.value];
        }
    }, 60000);

    // Setup keyboard shortcuts
    setupKeyboardShortcuts();

    // Log existing comments with scale info
    console.log('=== EXISTING COMMENTS DEBUG ===');
    comments.value.forEach(comment => {
        console.log(`Comment ${comment.id}:`, {
            page: comment.page_number,
            scale: comment.created_at_scale || 'not set',
            scale_type: typeof comment.created_at_scale,
            hasPosition: !!comment.position,
            content: comment.content?.substring(0, 50) + '...'
        });
    });
    console.log('==============================');
    setTimeout(() => {
        console.log('=== MANUAL SCALE CHECK ===');
        console.log('Scale after mount:', scale.value);
        console.log('Scale type:', typeof scale.value);

        // Test scale change
        const originalScale = scale.value;
        scale.value = 1.75; // Test value
        console.log('After manual change:', scale.value);
        console.log('Type after change:', typeof scale.value);

        // Reset
        scale.value = originalScale;
        console.log('After reset:', scale.value);
        console.log('=== END MANUAL CHECK ===');
    }, 2000);

    // Expose debug functions
    if (typeof window !== 'undefined') {
        window.forceTestComment = async () => {
            console.log('=== FORCE TEST COMMENT ===');

            const testPayload = {
                document_id: props.document.id,
                content: "FORCE TEST SCALE 1.75",
                page_number: 1,
                type: "point",
                position: JSON.stringify({x: 100, y: 100}),
                created_at_scale: 1.75, // HARDCODED untuk test
                page_dimensions: {
                    width: 800,
                    height: 600
                }
            };

            console.log('Force test payload:', testPayload);

            try {
                const response = await axios.post(props.apiStoreUrl, testPayload);
                console.log('Force test response:', response.data);
                console.log('Force test scale result:', response.data.created_at_scale);
                return response.data;
            } catch (error) {
                console.error('Force test error:', error);
                return error;
            }
        };

        window.getCurrentScale = () => {
            console.log('Current scale:', scale.value);
            console.log('Current scale type:', typeof scale.value);
            console.log('Canvas size:', canvasSize.value);
            return scale.value;
        };
    }
});
</script>
