<?php
namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Superilis;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class SuperilisController extends Controller
{
    /**
     * Menghasilkan surat persetujuan dalam format PDF.
     */
    public function generate(Request $request, Publication $publication)
    {
        // 1. Validasi dan simpan data kepala (tidak berubah)
        Carbon::setLocale('id');
        $validated = $request->validate([
            'template_choice' => 'required|in:A,B',
            'nomor_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'nama_kepala' => 'required|string',
            'nip_kepala' => 'required|string',
            'tipe_buku' => 'required|in:ARC,Non-ARC',
        ]);
        Superilis::updateOrCreate(['key' => 'last_head_name'], ['value' => $validated['nama_kepala']]);
        Superilis::updateOrCreate(['key' => 'last_head_nip'], ['value' => $validated['nip_kepala']]);

        // 2. Path ke template Word
        $templateName = $validated['template_choice'] === 'A'
            ? 'template_superilis.docx'
            : 'template_superilis2.docx';
        $templatePath = storage_path('app/public/templates/' . $templateName);
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'File template yang dipilih tidak ditemukan!');
        }

        // 3. Buat instance TemplateProcessor
        $templateProcessor = new TemplateProcessor($templatePath);

        // 4. Siapkan semua variabel yang dibutuhkan oleh template
        $tanggal = Carbon::parse($validated['tanggal_surat']);

        // 5. Ganti setiap placeholder di template dengan nilai yang sebenarnya
        $templateProcessor->setValue('nomor_surat', $validated['nomor_surat']);
        $templateProcessor->setValue('hari', $tanggal->isoFormat('dddd'));
        $templateProcessor->setValue('tanggal_terbilang', $this->terbilang($tanggal->day));
        $templateProcessor->setValue('bulan', $tanggal->isoFormat('MMMM'));
        $templateProcessor->setValue('bulan_terbilang', $this->bulanTerbilang($tanggal->month));
        $templateProcessor->setValue('tahun_terbilang', $this->terbilang($tanggal->year));
        $templateProcessor->setValue('nama_kepala', $validated['nama_kepala']);
        $templateProcessor->setValue('nip_kepala', $validated['nip_kepala']);
        $templateProcessor->setValue('judul_buku', $publication->name);
        $templateProcessor->setValue('tipe_buku', $validated['tipe_buku']);
        $templateProcessor->setValue('tanggal_surat_formatted', $tanggal->isoFormat('D MMMM YYYY'));

        // 6. Siapkan nama file untuk di-download
        $fileName = 'Surat Persetujuan - ' . $publication->name . '.docx';

        // 7. Simpan hasilnya dan kirim ke browser untuk di-download
        return response()->streamDownload(function () use ($templateProcessor) {
            $templateProcessor->saveAs('php://output');
        }, $fileName);
    }

    private function terbilang($angka)
    {
        $f = new \NumberFormatter('id', \NumberFormatter::SPELLOUT);
        return $f->format($angka);
    }

    private function bulanTerbilang($bulan)
    {
        $daftarBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        return $daftarBulan[(int) $bulan] ?? $bulan;
    }
}
