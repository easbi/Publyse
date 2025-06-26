// resources/js/app.js

// 1. Import file-file penting
import './bootstrap';
import '../css/app.css';

// 2. Import Vue dan komponen kita
import { createApp } from 'vue';
import PdfReviewer from './components/PdfReviewer.vue';

console.log("File app.js baru berhasil dijalankan!"); // Pesan debug #1

// 3. Cari elemen div tempat kita akan me-mount aplikasi Vue
const reviewerAppEl = document.getElementById('pdf-reviewer-app');

// 4. Hanya jalankan jika elemen tersebut ada di halaman
if (reviewerAppEl) {
    console.log("Elemen #pdf-reviewer-app DITEMUKAN! Memulai mount Vue..."); // Pesan debug #2

    // Ambil data dari atribut data-* dan parse sebagai JSON
    const documentData = JSON.parse(reviewerAppEl.dataset.document);
    const currentUserData = JSON.parse(reviewerAppEl.dataset.user);

    // Buat aplikasi Vue dengan props
    const app = createApp(PdfReviewer, {
        // document: documentData,
        // currentUser: currentUserData,
        document: JSON.parse(reviewerAppEl.dataset.document),
        currentUser: JSON.parse(reviewerAppEl.dataset.user),
        apiStoreUrl: reviewerAppEl.dataset.apiStoreUrl,
        apiUpdateUrlTemplate: reviewerAppEl.dataset.apiUpdateUrlTemplate,
        apiDeleteUrlTemplate: reviewerAppEl.dataset.apiDeleteUrlTemplate,
        apiStatusUrlTemplate: reviewerAppEl.dataset.apiStatusUrlTemplate,
    });

    // Pasang aplikasi Vue ke elemen div
    app.mount(reviewerAppEl);

    console.log("Aplikasi Vue berhasil di-mount."); // Pesan debug #3
} else {
    console.log("Elemen #pdf-reviewer-app TIDAK ditemukan di halaman ini."); // Pesan debug #4
}