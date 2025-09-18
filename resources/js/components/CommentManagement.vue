<!-- resources/js/components/CommentManagement.vue -->
<template>
    <aside class="comments-sidebar flex flex-col bg-gray-50 border-l border-gray-200">
        <!-- Header Sidebar -->
        <div class="p-4 border-b flex-shrink-0">
            <h2 class="text-xl font-bold text-gray-700">Daftar Komentar</h2>

            <!-- Search Box -->
            <div class="mt-3 mb-3">
                <div class="relative">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari komentar..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    >
                    <button
                        v-if="searchQuery"
                        @click="clearSearch"
                        class="absolute right-2 top-2 p-1 hover:bg-gray-200 rounded-full"
                    >
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Search Results Info -->
                <div v-if="searchQuery && searchResults.length !== displayedComments.length" class="mt-2 text-xs text-gray-500">
                    Ditemukan {{ searchResults.length }} dari {{ displayedComments.length }} komentar
                </div>
            </div>

            <div class="flex gap-2">
                <button @click="applyFilter('all')" :class="{'bg-blue-500 text-white': commentFilter === 'all'}" class="flex-1 text-sm px-3 py-1 border rounded-md">Semua</button>
                <button @click="applyFilter('open')" :class="{'bg-blue-500 text-white': commentFilter === 'open'}" class="flex-1 text-sm px-3 py-1 border rounded-md">Terbuka</button>
                <button @click="applyFilter('done')" :class="{'bg-blue-500 text-white': commentFilter === 'done'}" class="flex-1 text-sm px-3 py-1 border rounded-md">Selesai</button>
            </div>

            <!-- Pagination Info -->
            <div v-if="finalDisplayComments.length > 0" class="mt-2 text-sm text-gray-600 text-center">
                Menampilkan {{ startIndex + 1 }}-{{ Math.min(endIndex, finalDisplayComments.length) }} dari {{ finalDisplayComments.length }} komentar
            </div>
        </div>

        <!-- Comments List -->
        <div class="flex-1 overflow-hidden flex flex-col">
            <div v-if="finalDisplayComments.length === 0" class="flex-grow flex items-center justify-center p-4">
                <div class="text-center">
                    <p class="text-gray-500 text-center">
                        {{ searchQuery ? 'Tidak ada komentar yang cocok dengan pencarian.' : 'Tidak ada komentar yang cocok dengan filter ini.' }}
                    </p>
                    <button v-if="searchQuery" @click="clearSearch" class="mt-2 text-sm text-blue-500 hover:text-blue-700">
                        Hapus pencarian
                    </button>
                </div>
            </div>
            <div v-else class="flex-grow min-h-0 overflow-hidden flex flex-col">
                <!-- List Komentar dengan Pagination -->
                <div class="flex-grow overflow-y-auto p-3">
                    <ul class="space-y-3" ref="commentListEl">
                        <!-- Loop Komentar yang Dipaginasi -->
                        <li v-for="comment in paginatedComments"
                            :key="comment.id"
                            :ref="el => { if (el) commentElements[comment.id] = el }"
                            @click="handleCommentSelect(comment)"
                            class="comment-item border transition-colors rounded-lg overflow-hidden"
                            :class="[
                                comment.status === 'done' ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200',
                                comment.id === activeCommentId ? 'border-yellow-400 bg-yellow-50' : 'cursor-pointer hover:bg-blue-50'
                            ]">

                            <div class="p-4">
                                <!-- Header dengan nama user, timestamp, dan halaman -->
                                <div class="flex justify-between items-start gap-3 mb-3">
                                    <div class="comment-header flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-sm font-semibold text-gray-600 cursor-pointer" @click="handleGoToComment(comment)">
                                                {{ comment.user.fullname }}
                                            </p>
                                            <!-- Timestamp untuk komentar -->
                                            <span class="text-xs text-gray-400">•</span>
                                            <span class="text-xs text-gray-400 cursor-help" :title="formatFullDateTime(comment.created_at)">
                                                {{ formatRelativeTime(comment.created_at) }}
                                            </span>
                                        </div>

                                        <!-- Tampilkan jika komentar sudah diedit -->
                                        <div v-if="comment.updated_at && comment.updated_at !== comment.created_at" class="text-xs text-gray-400">
                                            <span class="cursor-help" :title="formatFullDateTime(comment.updated_at)">
                                                Diedit {{ formatRelativeTime(comment.updated_at) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span v-if="comment.page_number"
                                              class="text-xs font-semibold bg-gray-200 text-gray-700 px-2 py-1 rounded-full whitespace-nowrap cursor-pointer"
                                              @click="handleGoToComment(comment)">
                                            Hal. {{ comment.page_number }}
                                        </span>
                                        <input type="checkbox"
                                            :checked="comment.status === 'done'"
                                            @change="handleToggleStatus(comment)"
                                            @click.stop
                                            class="form-checkbox h-4 w-4 rounded text-green-600 transition duration-150 ease-in-out"
                                            :disabled="!canToggleStatus(comment)"
                                            :title="canToggleStatus(comment) ? 'Tandai sebagai selesai' : 'Hanya pemilik publikasi/komentar yang bisa mengubah status'"
                                            :class="{ 'cursor-not-allowed': !canToggleStatus(comment) }"
                                        >
                                    </div>
                                </div>

                                <!-- Konten Komentar -->
                                <div class="comment-content-wrapper">
                                    <!-- Form Edit -->
                                    <div v-if="editingComment && editingComment.id === comment.id" class="mb-3">
                                        <textarea
                                            v-model="localCommentEditText"
                                            class="comment-textarea w-full border rounded-md p-3 text-sm resize-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                                            rows="3">
                                        </textarea>
                                        <div class="flex gap-2 mt-2">
                                            <button
                                                @click="handleSaveEdit(comment)"
                                                class="text-xs text-white bg-blue-500 px-3 py-1 rounded-md hover:bg-blue-600">
                                                Simpan
                                            </button>
                                            <button @click="handleCancelEdit" class="text-xs text-gray-600 hover:text-gray-800">Batal</button>
                                        </div>
                                    </div>

                                    <!-- Teks Komentar dengan Highlight -->
                                    <div v-else class="comment-content cursor-pointer mb-3"
                                         :class="{'line-through text-gray-500': comment.status === 'done'}"
                                         @click="handleGoToComment(comment)"
                                         v-html="highlightSearchText(comment.content)">
                                    </div>
                                </div>

                                <!-- Tombol Aksi -->
                                <div class="flex items-center gap-3 text-xs text-gray-500 border-t pt-2">
                                    <button
                                        @click="handleStartReply(comment)"
                                        class="hover:underline hover:text-blue-600">
                                        Balas
                                    </button>
                                    <template v-if="currentUser.id === comment.user_id">
                                        <span>·</span>
                                        <button
                                            @click="handleStartEdit(comment)"
                                            class="hover:underline hover:text-blue-600">
                                            Edit
                                        </button>
                                        <span>·</span>
                                        <button
                                            @click="handleDeleteComment(comment)"
                                            class="hover:underline text-red-500 hover:text-red-700">
                                            Hapus
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Form untuk Balasan Baru -->
                            <div v-if="replyingToComment && replyingToComment.id === comment.id" class="px-4 pb-4">
                                <div class="ml-4 pl-4 border-l-2 border-gray-200">
                                    <textarea
                                        v-model="localReplyText"
                                        class="comment-textarea w-full border rounded-md p-3 text-sm focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                                        placeholder="Tulis balasan..."
                                        rows="2">
                                    </textarea>
                                    <div class="flex gap-2 mt-2">
                                        <button @click="handleSubmitReply(comment)" class="text-xs text-white bg-blue-500 px-3 py-1 rounded-md hover:bg-blue-600">Kirim</button>
                                        <button @click="handleCancelReply" class="text-xs text-gray-600 hover:text-gray-800">Batal</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Balasan (Replies) -->
                            <div v-if="comment.replies && comment.replies.length > 0" class="px-4 pb-4">
                                <div class="ml-4 pl-4 border-l-2 border-gray-200 space-y-2">
                                    <div v-for="reply in comment.replies" :key="reply.id" class="bg-gray-50 p-3 rounded-md">
                                        <div class="flex justify-between items-start gap-3 mb-2">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-xs font-semibold text-gray-600">{{ reply.user.fullname }}</p>
                                                <span class="text-xs text-gray-400">•</span>
                                                <span class="text-xs text-gray-400 cursor-help" :title="formatFullDateTime(reply.created_at)">
                                                    {{ formatRelativeTime(reply.created_at) }}
                                                </span>
                                            </div>

                                            <!-- Actions untuk reply -->
                                            <div v-if="currentUser.id === reply.user_id" class="flex items-center gap-2 text-xs text-gray-500">
                                                <button
                                                    @click="handleStartEditReply(reply)"
                                                    class="hover:underline hover:text-blue-600">
                                                    Edit
                                                </button>
                                                <span>·</span>
                                                <button
                                                    @click="handleDeleteReply(comment, reply)"
                                                    class="hover:underline text-red-500 hover:text-red-700">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Tampilkan jika balasan sudah diedit -->
                                        <div v-if="reply.updated_at && reply.updated_at !== reply.created_at" class="text-xs text-gray-400 mb-1">
                                            <span class="cursor-help" :title="formatFullDateTime(reply.updated_at)">
                                                Diedit {{ formatRelativeTime(reply.updated_at) }}
                                            </span>
                                        </div>

                                        <!-- Reply content atau form edit -->
                                        <div v-if="editingReply && editingReply.id === reply.id" class="mb-2">
                                            <textarea
                                                v-model="localReplyEditText"
                                                class="comment-textarea w-full border rounded-md p-2 text-sm resize-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                                                rows="2">
                                            </textarea>
                                            <div class="flex gap-2 mt-2">
                                                <button
                                                    @click="handleSaveEditReply(comment, reply)"
                                                    class="text-xs text-white bg-blue-500 px-3 py-1 rounded-md hover:bg-blue-600">
                                                    Simpan
                                                </button>
                                                <button @click="handleCancelEditReply" class="text-xs text-gray-600 hover:text-gray-800">Batal</button>
                                            </div>
                                        </div>
                                        <div v-else class="comment-content text-sm text-gray-700" v-html="highlightSearchText(reply.content)"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Pagination Controls -->
                <div v-if="totalPaginationPages > 1" class="p-3 border-t bg-white flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <button
                            @click="handlePreviousPage"
                            :disabled="currentPaginationPage <= 1"
                            class="px-3 py-1 text-sm bg-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300">
                            &lt; Prev
                        </button>

                        <div class="flex items-center gap-1">
                            <span v-for="page in visiblePageNumbers" :key="page" class="pagination-item">
                                <button
                                    v-if="page !== '...'"
                                    @click="handleGoToPage(page)"
                                    :class="page === currentPaginationPage ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300'"
                                    class="px-2 py-1 text-xs rounded">
                                    {{ page }}
                                </button>
                                <span v-else class="px-1 text-xs text-gray-500">...</span>
                            </span>
                        </div>

                        <button
                            @click="handleNextPage"
                            :disabled="currentPaginationPage >= totalPaginationPages"
                            class="px-3 py-1 text-sm bg-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300">
                            Next &gt;
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</template>

<style scoped>
    /* Layout responsif dengan proporsi 2/7 dari lebar layar */
    .comments-sidebar {
        width: calc(2/7 * 100%);
        min-width: 300px;
        max-width: none;
        height: 100vh;
        flex-shrink: 0;
    }

    /* Responsive untuk layar kecil */
    @media (max-width: 1200px) {
        .comments-sidebar {
            width: 40%;
            min-width: 280px;
        }
    }

    @media (max-width: 768px) {
        .comments-sidebar {
            width: 50%;
            min-width: 250px;
        }
    }

    /* Comment styling dengan text wrapping yang kuat */
    .comment-content {
        word-wrap: break-word !important;
        word-break: break-all !important;
        overflow-wrap: anywhere !important;
        hyphens: auto;
        line-height: 1.5;
        white-space: pre-wrap;
        max-width: 100% !important;
        overflow: hidden !important;
        font-size: 0.875rem;
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

    /* Search highlight styling */
    .search-highlight {
        background-color: #fef08a;
        color: #854d0e;
        font-weight: 500;
        padding: 1px 2px;
        border-radius: 2px;
    }

    /* Timestamp styling */
    .text-xs.text-gray-400 {
        font-size: 0.75rem;
        line-height: 1;
    }

    /* Hover effect untuk timestamp */
    .text-xs.text-gray-400.cursor-help:hover {
        color: #6b7280;
    }

    /* Pagination styling */
    .pagination-item {
        display: inline-block;
    }

    /* Scrollbar styling */
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
import { ref, computed, watch } from 'vue';

// Define emits
const emit = defineEmits([
    'filter-changed',
    'page-changed',
    'previous-page',
    'next-page',
    'comment-selected',
    'go-to-comment',
    'toggle-comment-status',
    'start-edit',
    'save-edit',
    'cancel-edit',
    'start-reply',
    'submit-reply',
    'cancel-reply',
    'start-edit-reply',
    'save-edit-reply',
    'cancel-edit-reply',
    'delete-comment',
    'delete-reply'
]);

// Define props
const props = defineProps({
    comments: Array,
    currentUser: Object,
    creatorId: [Number, String],
    activeCommentId: [String, Number],
    commentFilter: String,
    displayedComments: Array,
    currentPaginationPage: Number,
    commentsPerPage: Number,
    editingComment: Object,
    commentEditText: String,
    replyingToComment: Object,
    replyText: String,
    editingReply: Object,
    replyEditText: String
});

// Local state
const commentListEl = ref(null);
const commentElements = ref({});
const localCommentEditText = ref('');
const localReplyText = ref('');
const localReplyEditText = ref('');

// Search state
const searchQuery = ref('');

// Search functionality
const searchResults = computed(() => {
    if (!searchQuery.value.trim()) {
        return props.displayedComments;
    }

    const query = searchQuery.value.toLowerCase().trim();

    return props.displayedComments.filter(comment => {
        // Search in comment content
        const contentMatch = comment.content.toLowerCase().includes(query);

        // Search in user name
        const userMatch = comment.user?.fullname?.toLowerCase().includes(query);

        // Search in replies
        const replyMatch = comment.replies?.some(reply =>
            reply.content.toLowerCase().includes(query) ||
            reply.user?.fullname?.toLowerCase().includes(query)
        );

        return contentMatch || userMatch || replyMatch;
    });
});

// Final displayed comments (after search)
const finalDisplayComments = computed(() => {
    return searchResults.value;
});

// Computed properties
const startIndex = computed(() => {
    return (props.currentPaginationPage - 1) * props.commentsPerPage;
});

const endIndex = computed(() => {
    return startIndex.value + props.commentsPerPage;
});

const paginatedComments = computed(() => {
    return finalDisplayComments.value.slice(startIndex.value, endIndex.value);
});

const totalPaginationPages = computed(() => {
    return Math.ceil(finalDisplayComments.value.length / props.commentsPerPage);
});

const visiblePageNumbers = computed(() => {
    const total = totalPaginationPages.value;
    const current = props.currentPaginationPage;
    const pages = [];

    if (total <= 7) {
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
    } else {
        if (current <= 4) {
            for (let i = 1; i <= 5; i++) pages.push(i);
            pages.push('...');
            pages.push(total);
        } else if (current >= total - 3) {
            pages.push(1);
            pages.push('...');
            for (let i = total - 4; i <= total; i++) pages.push(i);
        } else {
            pages.push(1);
            pages.push('...');
            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
            pages.push('...');
            pages.push(total);
        }
    }

    return pages;
});

// Search functions
const clearSearch = () => {
    searchQuery.value = '';
};

const highlightSearchText = (text) => {
    if (!searchQuery.value.trim() || !text) {
        return text;
    }

    const query = searchQuery.value.trim();
    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');

    return text.replace(regex, '<span class="search-highlight">$1</span>');
};

// Watch for prop changes to sync local state
watch(() => props.commentEditText, (newVal) => {
    localCommentEditText.value = newVal;
}, { immediate: true });

watch(() => props.replyText, (newVal) => {
    localReplyText.value = newVal;
}, { immediate: true });

watch(() => props.replyEditText, (newVal) => {
    localReplyEditText.value = newVal;
}, { immediate: true });

// Reset pagination when search changes
watch(searchQuery, () => {
    // Reset to first page when searching
    if (props.currentPaginationPage !== 1) {
        handleGoToPage(1);
    }
});

// Timestamp formatting functions
const formatRelativeTime = (timestamp) => {
    if (!timestamp) return '';

    const now = new Date();
    const commentTime = new Date(timestamp);
    const diffInSeconds = Math.floor((now - commentTime) / 1000);

    if (diffInSeconds < 60) {
        return 'Baru saja';
    }

    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) {
        return `${diffInMinutes} menit yang lalu`;
    }

    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) {
        return `${diffInHours} jam yang lalu`;
    }

    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) {
        return `${diffInDays} hari yang lalu`;
    }

    const diffInWeeks = Math.floor(diffInDays / 7);
    if (diffInWeeks < 4) {
        return `${diffInWeeks} minggu yang lalu`;
    }

    const diffInMonths = Math.floor(diffInDays / 30);
    if (diffInMonths < 12) {
        return `${diffInMonths} bulan yang lalu`;
    }

    const diffInYears = Math.floor(diffInDays / 365);
    return `${diffInYears} tahun yang lalu`;
};

const formatFullDateTime = (timestamp) => {
    if (!timestamp) return '';

    const date = new Date(timestamp);
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZoneName: 'short'
    };

    return date.toLocaleDateString('id-ID', options);
};

// Computed property untuk check permission
const canToggleStatus = computed(() => {
    return (comment) => {
        const currentUserId = String(props.currentUser?.id || '');
        const creatorIdStr = String(props.creatorId || '');
        const commentUserId = String(comment?.user_id || '');

        // Fallback: jika creatorId tidak ada, anggap currentUser adalah owner
        const isCreator = creatorIdStr !== '' ?
            (currentUserId === creatorIdStr) :
            true; // Fallback logic

        const isCommentOwner = currentUserId === commentUserId;
        const canToggle = isCreator || isCommentOwner;

        return canToggle;
    };
});

const applyFilter = (filterType) => {
    emit('filter-changed', filterType);
};

const handleGoToPage = (page) => {
    emit('page-changed', page);
};

const handlePreviousPage = () => {
    emit('previous-page');
};

const handleNextPage = () => {
    emit('next-page');
};

const handleCommentSelect = (comment) => {
    emit('comment-selected', comment);
};

const handleGoToComment = (comment) => {
    emit('go-to-comment', comment);
};

const handleToggleStatus = (comment) => {
    if (!canToggleStatus.value(comment)) {
        return;
    }

    emit('toggle-comment-status', comment);
};

const handleStartEdit = (comment) => {
    localCommentEditText.value = comment.content;
    emit('start-edit', comment);
};

const handleSaveEdit = (comment) => {
    emit('save-edit', comment, localCommentEditText.value);
};

const handleCancelEdit = () => {
    localCommentEditText.value = '';
    emit('cancel-edit');
};

const handleStartReply = (comment) => {
    localReplyText.value = '';
    emit('start-reply', comment);
};

const handleSubmitReply = (comment) => {
    if (!localReplyText.value.trim()) {
        console.warn('Reply text is empty in child component');
        return;
    }
    emit('submit-reply', comment, localReplyText.value);
    localReplyText.value = '';
};

const handleCancelReply = () => {
    localReplyText.value = '';
    emit('cancel-reply');
};

const handleStartEditReply = (reply) => {
    localReplyEditText.value = reply.content;
    emit('start-edit-reply', reply);
};

const handleSaveEditReply = (comment, reply) => {
    emit('save-edit-reply', comment, reply, localReplyEditText.value);
    localReplyEditText.value = '';
};

const handleCancelEditReply = () => {
    localReplyEditText.value = '';
    emit('cancel-edit-reply');
};

const handleDeleteComment = (comment) => {
    emit('delete-comment', comment);
};

const handleDeleteReply = (comment, reply) => {
    emit('delete-reply', comment, reply);
};
</script>
