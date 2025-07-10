<!-- resources/js/components/PdfReviewer.vue -->
<template>
    <div class="flex w-full h-screen" :class="{ 'no-select': isDragging }">
        <!-- Main Content: PDF Viewer - 5/7 dari lebar layar -->
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
                    <button @click="zoomOut" :disabled="scale <= minScale" class="p-2 rounded-md hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed" title="Zoom Out">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            <path d="M4 6.5h6a.5.5 0 0 1 0 1H4a.5.5 0 0 1 0-1z"/>
                        </svg>
                    </button>

                    <select v-model="scale" @change="onZoomChange" class="text-sm border-0 bg-transparent focus:ring-0 cursor-pointer">
                        <option v-for="zoomLevel in zoomLevels" :key="zoomLevel.value" :value="zoomLevel.value">
                            {{ zoomLevel.label }}
                        </option>
                    </select>

                    <button @click="zoomIn" :disabled="scale >= maxScale" class="p-2 rounded-md hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed" title="Zoom In">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            <path d="M6.5 4a.5.5 0 0 1 .5.5v2h2a.5.5 0 0 1 0 1h-2v2a.5.5 0 0 1-1 0v-2h-2a.5.5 0 0 1 0-1h2v-2A.5.5 0 0 1 6.5 4z"/>
                        </svg>
                    </button>

                    <div class="border-l border-gray-300 h-6 mx-1"></div>

                    <button @click="fitToWidth" class="p-2 rounded-md hover:bg-gray-100" title="Fit to Width">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 0v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1z"/>
                            <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8zM2 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm10-3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                    </button>

                    <button @click="fitToPage" class="p-2 rounded-md hover:bg-gray-100" title="Fit to Page">
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
                        @mousedown="handleAnnotationMouseDown"
                        @mousemove="handleAnnotationMouseMove"
                        @mouseup="handleAnnotationMouseUp"
                        @click.self="handleLayerClick"
                        @wheel="handleWheel"
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

        <!-- Sidebar: Daftar Komentar - 2/7 dari lebar layar -->
        <aside class="comments-sidebar flex flex-col bg-gray-50 border-l border-gray-200">
            <!-- Header Sidebar -->
            <div class="p-4 border-b flex-shrink-0">
                <h2 class="text-xl font-bold text-gray-700">Daftar Komentar</h2>

                <div class="mt-3 flex gap-2">
                    <button @click="applyFilter('all')" :class="{'bg-blue-500 text-white': commentFilter === 'all'}" class="flex-1 text-sm px-3 py-1 border rounded-md">Semua</button>
                    <button @click="applyFilter('open')" :class="{'bg-blue-500 text-white': commentFilter === 'open'}" class="flex-1 text-sm px-3 py-1 border rounded-md">Terbuka</button>
                    <button @click="applyFilter('done')" :class="{'bg-blue-500 text-white': commentFilter === 'done'}" class="flex-1 text-sm px-3 py-1 border rounded-md">Selesai</button>
                </div>

                <!-- Pagination Info -->
                <div v-if="displayedComments.length > 0" class="mt-2 text-sm text-gray-600 text-center">
                    Menampilkan {{ startIndex + 1 }}-{{ Math.min(endIndex, displayedComments.length) }} dari {{ displayedComments.length }} komentar
                </div>
            </div>

            <!-- Comments List -->
            <div class="flex-1 overflow-hidden flex flex-col">
                <div v-if="displayedComments.length === 0" class="flex-grow flex items-center justify-center p-4">
                    <p class="text-gray-500 text-center">Tidak ada komentar yang cocok dengan filter ini.</p>
                </div>
                <div v-else class="flex-grow min-h-0 overflow-hidden flex flex-col">
                    <!-- List Komentar dengan Pagination -->
                    <div class="flex-grow overflow-y-auto p-3">
                        <ul class="space-y-3" ref="commentListEl">
                            <!-- Loop Komentar yang Dipaginasi -->
                            <li v-for="comment in paginatedComments"
                                :key="comment.id"
                                :ref="el => { if (el) commentElements[comment.id] = el }"
                                @click="setActiveComment(comment)"
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
                                                @change="toggleCommentStatus(comment)"
                                                @click.stop
                                                class="form-checkbox h-4 w-4 rounded text-green-600 transition duration-150 ease-in-out"
                                                :disabled="!(currentUser.id === creatorId || currentUser.id === comment.user_id)"
                                                :title="(currentUser.id === creatorId || currentUser.id === comment.user_id) ? 'Tandai sebagai selesai' : 'Hanya pemilik publikasi/komentar yang bisa mengubah status'"
                                                :class="{ 'cursor-not-allowed': !(currentUser.id === creatorId || currentUser.id === comment.user_id) }"
                                            >
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
                                                <button
                                                    @click="saveEdit(comment)"
                                                    class="text-xs text-white bg-blue-500 px-3 py-1 rounded-md hover:bg-blue-600">
                                                    Simpan
                                                </button>
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
                                        <button
                                            @click="startReply(comment)"
                                            class="hover:underline hover:text-blue-600">
                                            Balas
                                        </button>
                                        <template v-if="currentUser.id === comment.user_id">
                                            <span>·</span>
                                            <button
                                                @click="startEdit(comment)"
                                                class="hover:underline hover:text-blue-600">
                                                Edit
                                            </button>
                                            <span>·</span>
                                            <button
                                                @click="deleteComment(comment)"
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
                                                        @click="startEditReply(reply)"
                                                        class="hover:underline hover:text-blue-600">
                                                        Edit
                                                    </button>
                                                    <span>·</span>
                                                    <button
                                                        @click="deleteReply(comment, reply)"
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
                                                    v-model="replyEditText"
                                                    class="comment-textarea w-full border rounded-md p-2 text-sm resize-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                                                    rows="2">
                                                </textarea>
                                                <div class="flex gap-2 mt-2">
                                                    <button
                                                        @click="saveEditReply(comment, reply)"
                                                        class="text-xs text-white bg-blue-500 px-3 py-1 rounded-md hover:bg-blue-600">
                                                        Simpan
                                                    </button>
                                                    <button @click="cancelEditReply" class="text-xs text-gray-600 hover:text-gray-800">Batal</button>
                                                </div>
                                            </div>
                                            <div v-else class="comment-content text-sm text-gray-700">{{ reply.content }}</div>
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
                                @click="goToPreviousPage"
                                :disabled="currentPaginationPage <= 1"
                                class="px-3 py-1 text-sm bg-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300">
                                &lt; Prev
                            </button>

                            <div class="flex items-center gap-1">
                                <span v-for="page in visiblePageNumbers" :key="page" class="pagination-item">
                                    <button
                                        v-if="page !== '...'"
                                        @click="goToPage(page)"
                                        :class="page === currentPaginationPage ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300'"
                                        class="px-2 py-1 text-xs rounded">
                                        {{ page }}
                                    </button>
                                    <span v-else class="px-1 text-xs text-gray-500">...</span>
                                </span>
                            </div>

                            <button
                                @click="goToNextPage"
                                :disabled="currentPaginationPage >= totalPaginationPages"
                                class="px-3 py-1 text-sm bg-gray-200 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300">
                                Next &gt;
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

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
    .pdf-viewer-container {
        width: calc(5/7 * 100%); /* 5/7 dari lebar layar */
        min-width: 0;
        flex-shrink: 0;
    }

    .comments-sidebar {
        width: calc(2/7 * 100%); /* 2/7 dari lebar layar */
        min-width: 300px; /* Minimum width untuk keterbacaan */
        max-width: none;
        height: 100vh;
        flex-shrink: 0;
    }

    /* Responsive untuk layar kecil */
    @media (max-width: 1200px) {
        .pdf-viewer-container {
            width: 60%;
        }

        .comments-sidebar {
            width: 40%;
            min-width: 280px;
        }
    }

    @media (max-width: 768px) {
        .pdf-viewer-container {
            width: 50%;
        }

        .comments-sidebar {
            width: 50%;
            min-width: 250px;
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

    /* Annotation styling */
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

    /* Zoom transition */
    .zoom-transition {
        transition: transform 0.2s ease-out;
    }
</style>

<script setup>
import { ref, onMounted, computed, watch, nextTick, markRaw, onUnmounted } from 'vue';
import * as pdfjsLib from 'pdfjs-dist';
import axios from 'axios';


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

// Cleanup keyboard shortcuts
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
const currentPageNumInput = ref(1);
const totalPages = ref(0);
const scale = ref(1.5);
const displayScale = ref(1.0); // Scale untuk display/transform CSS
const isLoading = ref(false);
const canvasSize = ref({ width: 0, height: 0 });
const pdfContainer = ref(null);

// Zoom configuration
const minScale = ref(0.25);
const maxScale = ref(5.0);
const zoomStep = ref(0.25);

// Predefined zoom levels
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
const commentListEl = ref(null);
const commentElements = ref({});

// Pagination refs
const currentPaginationPage = ref(1);
const commentsPerPage = ref(5);

// Comments state
const displayedComments = ref([]);

// Timestamp refresh interval
let timestampInterval = null;

// Function untuk membangun struktur hierarkis dari flat array
const buildCommentTree = (flatComments) => {
    const commentMap = new Map();
    const topLevelComments = [];

    // Pertama, buat map dari semua komentar
    flatComments.forEach(comment => {
        comment.replies = [];
        commentMap.set(comment.id, comment);
    });

    // Kemudian, bangun struktur hierarkis
    flatComments.forEach(comment => {
        if (comment.parent_id && commentMap.has(comment.parent_id)) {
            // Ini adalah reply, tambahkan ke parent
            const parent = commentMap.get(comment.parent_id);
            parent.replies.push(comment);
        } else {
            // Ini adalah komentar utama
            topLevelComments.push(comment);
        }
    });

    return topLevelComments;
};

// Computed properties untuk komentar hierarkis
const hierarchicalComments = computed(() => {
    return buildCommentTree([...comments.value]);
});

// Computed properties for comments (updated to use hierarchical structure)
const sortedComments = computed(() => {
    return hierarchicalComments.value.sort((a, b) => a.page_number - b.page_number || a.id - b.id);
});

// Pagination computed properties
const totalPaginationPages = computed(() => {
    return Math.ceil(displayedComments.value.length / commentsPerPage.value);
});

const startIndex = computed(() => {
    return (currentPaginationPage.value - 1) * commentsPerPage.value;
});

const endIndex = computed(() => {
    return startIndex.value + commentsPerPage.value;
});

const paginatedComments = computed(() => {
    return displayedComments.value.slice(startIndex.value, endIndex.value);
});

const visiblePageNumbers = computed(() => {
    const total = totalPaginationPages.value;
    const current = currentPaginationPage.value;
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

const onZoomChange = () => {
    renderPage(currentPageNum.value);
};

const fitToWidth = async () => {
    if (!pdfDoc.value || !pdfContainer.value) return;

    try {
        const page = await pdfDoc.value.getPage(currentPageNum.value);
        const viewport = page.getViewport({ scale: 1.0 });
        const containerWidth = pdfContainer.value.clientWidth - 40; // padding
        const newScale = containerWidth / viewport.width;

        // Clamp scale between min and max
        scale.value = Math.max(minScale.value, Math.min(maxScale.value, newScale));
        renderPage(currentPageNum.value);
    } catch (error) {
        console.error("Error fitting to width:", error);
    }
};

const fitToPage = async () => {
    if (!pdfDoc.value || !pdfContainer.value) return;

    try {
        const page = await pdfDoc.value.getPage(currentPageNum.value);
        const viewport = page.getViewport({ scale: 1.0 });
        const containerWidth = pdfContainer.value.clientWidth - 40;
        const containerHeight = pdfContainer.value.clientHeight - 40;

        const scaleWidth = containerWidth / viewport.width;
        const scaleHeight = containerHeight / viewport.height;
        const newScale = Math.min(scaleWidth, scaleHeight);

        scale.value = Math.max(minScale.value, Math.min(maxScale.value, newScale));
        renderPage(currentPageNum.value);
    } catch (error) {
        console.error("Error fitting to page:", error);
    }
};

// Mouse wheel zoom
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

// Annotation event handlers
const handleAnnotationMouseDown = (event) => {
    startSelection(event);
};

const handleAnnotationMouseMove = (event) => {
    updateSelection(event);
};

const handleAnnotationMouseUp = (event) => {
    endSelection(event);
};

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

// Function untuk menghitung total komentar termasuk replies
const countAllComments = (commentsArray, status = null) => {
    let count = 0;

    commentsArray.forEach(comment => {
        // Hitung komentar utama
        if (!status || comment.status === status) {
            count++;
        }

        // Hitung replies
        if (comment.replies && comment.replies.length > 0) {
            comment.replies.forEach(reply => {
                if (!status || reply.status === status) {
                    count++;
                }
            });
        }
    });

    return count;
};

// Filter function - Updated untuk menyertakan replies dalam filtering
const applyFilter = (filterType) => {
    commentFilter.value = filterType;
    currentPaginationPage.value = 1;

    if (filterType === 'all') {
        displayedComments.value = sortedComments.value;
    } else {
        // Filter komentar utama dan replies berdasarkan status
        displayedComments.value = sortedComments.value.filter(comment => {
            // Cek apakah komentar utama sesuai filter
            const commentMatches = comment.status === filterType;

            // Cek apakah ada replies yang sesuai filter
            const hasMatchingReplies = comment.replies && comment.replies.some(reply => reply.status === filterType);

            // Tampilkan jika komentar utama sesuai atau ada replies yang sesuai
            return commentMatches || hasMatchingReplies;
        }).map(comment => {
            // Jika filtering bukan 'all', filter replies juga
            if (filterType !== 'all') {
                return {
                    ...comment,
                    replies: comment.replies ? comment.replies.filter(reply => reply.status === filterType) : []
                };
            }
            return comment;
        });
    }
};

// Pagination functions
const goToPage = (page) => {
    if (page >= 1 && page <= totalPaginationPages.value) {
        currentPaginationPage.value = page;
    }
};

const goToPreviousPage = () => {
    if (currentPaginationPage.value > 1) {
        currentPaginationPage.value--;
    }
};

const goToNextPage = () => {
    if (currentPaginationPage.value < totalPaginationPages.value) {
        currentPaginationPage.value++;
    }
};

// Watch for comments changes
watch(sortedComments, (newSortedList) => {
    applyFilter(commentFilter.value);
}, { immediate: true });

// PDF.js worker configuration
pdfjsLib.GlobalWorkerOptions.workerSrc = `https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js`;

onUnmounted(() => {
    // Cleanup timestamp interval
    if (timestampInterval) {
        clearInterval(timestampInterval);
    }

    // Cleanup keyboard event listener
    cleanupKeyboardShortcuts();
});

onMounted(async () => {
    console.log("Component mounted");

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

    // Auto-refresh timestamps setiap menit
    timestampInterval = setInterval(() => {
        if (comments.value.length > 0) {
            comments.value = [...comments.value];
        }
    }, 60000);

    // Setup keyboard shortcuts
    setupKeyboardShortcuts();
});

watch(showCommentModal, (isShowing) => {
    if (isShowing) nextTick(() => {
        commentTextarea.value?.focus();
    });
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

const goToPdfPage = (num) => {
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
    newCommentData.value = {
        content: '',
        position: { x: event.offsetX, y: event.offsetY },
        type: 'point',
        page_number: currentPageNum.value
    };
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
        newCommentFromServer.position = parsePosition(newCommentFromServer.position);

        // Tambahkan komentar baru ke array comments (bukan ke hierarchical structure)
        comments.value.push(newCommentFromServer);

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

        // Force reactivity update
        comments.value = [...comments.value];
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
    setTimeout(() => {
        highlightedCommentId.value = null;
    }, 1500);
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
        comment.updated_at = response.data.updated_at;

        // Force reactivity update
        comments.value = [...comments.value];

        cancelEdit();
    } catch (error) {
        console.error("Gagal mengupdate komentar:", error);
        alert('Gagal mengupdate komentar.');
    }
};

// Reply editing functions
const startEditReply = (reply) => {
    editingReply.value = reply;
    replyEditText.value = reply.content;
};

const cancelEditReply = () => {
    editingReply.value = null;
    replyEditText.value = '';
};

const saveEditReply = async (parentComment, reply) => {
    if (!replyEditText.value.trim()) return;
    try {
        const url = props.apiUpdateUrlTemplate.replace('COMMENT_ID', reply.id);
        const response = await axios.put(url, { content: replyEditText.value });

        reply.content = response.data.content;
        reply.updated_at = response.data.updated_at;

        // Force reactivity update
        comments.value = [...comments.value];

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

            // Hapus dari array utama comments
            comments.value = removeCommentFromArray(comments.value, commentToDelete.id);
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

            // Hapus reply dari array utama comments
            comments.value = removeCommentFromArray(comments.value, reply.id);
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

const submitReply = async (parentComment) => {
    if (!replyText.value.trim()) {
        console.warn('Reply text is empty');
        return;
    }

    const payload = {
        content: replyText.value,
        document_id: props.document.id,
        parent_id: parentComment.id
    };

    console.log('Sending reply payload:', payload);

    try {
        const response = await axios.post(props.apiStoreUrl, payload);
        console.log('Reply response:', response.data);

        // Tambahkan reply ke array utama comments (akan diatur ulang oleh buildCommentTree)
        comments.value.push(response.data);

        // Reset form
        cancelReply();

        console.log('Reply berhasil ditambahkan');
    } catch (error) {
        console.error("Gagal mengirim balasan:", error);
        console.error("Error response:", error.response?.data);
        console.error("Error status:", error.response?.status);

        if (error.response?.data?.message) {
            alert(`Gagal mengirim balasan: ${error.response.data.message}`);
        } else {
            alert('Gagal mengirim balasan.');
        }
    }
};

//  commentsOnCurrentPage untuk hanya menampilkan komentar utama di PDF annotation layer
const commentsOnCurrentPage = computed(() => {
    return hierarchicalComments.value.filter(c => {
        // Hanya komentar utama
        if (c.parent_id !== null) return false;

        // Filter halaman aktif dan tipe
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
});

</script>
