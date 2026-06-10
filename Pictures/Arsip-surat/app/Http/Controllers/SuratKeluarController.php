<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Http;
use App\Models\PenanggungJawab; 
use App\Models\NomorSurat;


class SuratKeluarController extends Controller
{
    private function kodeSurat()
{
    return [
        'surat undangan' => '01',
        'surat pemberitahuan' => '02',
        'surat permohonan' => '03',
        'surat edaran' => '04',
        'surat keputusan' => '05',
        'surat keterangan' => '06',
        'surat tugas' => '07',
        'surat perjalanan dinas' => '08',
        'surat peraturan' => '09',
        'surat pengantar' => '10',
        'surat pernyataan' => '11',
        'surat kuasa' => '12',
        'surat peringatan' => '13',
        'surat memo' => '14',
        'surat instruksi/perintah' => '15',
        'surat perjanjian' => '16',
        'mou' => '17',
        'surat rekomendasi' => '18',
        'surat balasan' => '19',
        'surat pengumuman' => '20',
        'nota dinas' => '21',
        'berita acara' => '22',
        'Piagam-sertifikat' => '23',
        'surat persetujuan' => '24',
        'surat kontrak' => '25',
    ];
}
    // ================== INDEX + SEARCH ==================
   public function index(Request $request)
{
    $keyword = $request->keyword;
    $tanggal = $request->tanggal;

    $data = SuratKeluar::where('pengirim_id', auth()->id())

        ->when($keyword, function ($query) use ($keyword) {
            $query->where(function ($q) use ($keyword) {

                $q->where('perihal', 'like', "%{$keyword}%")
                  ->orWhere('tujuan_surat', 'like', "%{$keyword}%")
                  ->orWhere('nomor_surat', 'like', "%{$keyword}%");

            });
        })

        ->when($tanggal, function ($query) use ($tanggal) {
            $query->whereDate('tanggal_surat', $tanggal);
        })

        ->orderBy('tanggal_surat', 'desc')
        ->get();

    return view('surat_keluar.index', compact(
        'data',
        'keyword',
        'tanggal'
    ));
}

 //====filter jenis surat======
   public function filter(Request $request)
{
    $jenis = $request->jenis;

    $data = SuratKeluar::where('pengirim_id', auth()->id())
        ->when($jenis, function ($query) use ($jenis) {
            $query->where('perihal', $jenis);
        })
        ->orderBy('tanggal_surat', 'desc')
        ->get();

    return view('surat_keluar.index', compact('data'));
}

    // ================== CREATE ==================


public function create()
{
    $users = User::where('id', '!=', auth()->id())
        ->whereIn('role', ['user', 'admin'])
        ->get();

    // ambil data penanggung jawab
    $penanggungJawab = PenanggungJawab::where(
    'user_id',
    auth()->id()
    )->get();
    // nomor surat user login
    $nomorSurat = \App\Models\NomorSurat::where('user_id', auth()->id())
        ->pluck('nomor', 'jenis_surat');

    return view('surat_keluar.create', [
        'users' => $users,
        'penanggungJawab' => $penanggungJawab,
        'nomorSurat' => $nomorSurat,
        'prefill' => session('prefill', []),
        'uploaded_file_name' => session('uploaded_file_name'),
    ]);
}


    public function store(Request $request)
{
    $request->validate([
        
        'tujuan_id' => 'required|array',
        'tujuan_id.*' => 'exists:users,id',
        'tanggal_surat' => 'required|date',
        'perihal' => 'required',
        'penanggung_jawab_id' => 'nullable|exists:penanggung_jawabs,id',
        'file' => 'nullable|mimes:pdf,jpg,jpeg,png,xlsx,xls,csv|max:4096',
        'uploaded_file_name' => 'nullable|string',
    ]);

    $sender = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | FORMAT DASAR SURAT
    |--------------------------------------------------------------------------
    */

    $jenis = strtolower($request->perihal);

    $bulanRomawi = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
        5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
        9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    $bulan = $bulanRomawi[now()->month];
    $tahun = now()->year;

    $kodeOrganisasi = $sender->kode_organisasi;

    $kodeJenis = $this->kodeSurat()[$jenis] ?? '00';

    /*
    |--------------------------------------------------------------------------
    | AMBIL NOMOR SURAT DARI DATABASE
    |--------------------------------------------------------------------------
    */

    $nomorConfig = \App\Models\NomorSurat::where('user_id', $sender->id)
        ->where('jenis_surat', str_replace(' ', '_', $jenis))
        ->first();

    if (!$nomorConfig) {
        return back()->with('error', 'Konfigurasi nomor surat belum tersedia');
    }

    $nomorUrut = $nomorConfig->nomor ?? 1;

    // increment nomor untuk berikutnya
    $nomorConfig->update([
        'nomor' => $nomorUrut + 1
    ]);

    $nomorUrut = str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

    $nomorSurat =
        $nomorUrut . '.' .
        $kodeJenis . '/' .
        $kodeOrganisasi . '/' .
        $bulan . '/' .
        $tahun;

    /*
    |--------------------------------------------------------------------------
    | FILE HANDLING
    |--------------------------------------------------------------------------
    */

    $fileName = null;

    if ($request->filled('uploaded_file_name')) {
        $fileName = $request->uploaded_file_name;
    }

    if ($request->hasFile('file')) {
        $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
        $request->file('file')->move(public_path('uploads/surat_keluar'), $fileName);
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN SURAT
    |--------------------------------------------------------------------------
    */

    $tujuanIds = $request->tujuan_id;

    // kalau pilih semua user
    if (in_array('all', $tujuanIds)) {

        $users = \App\Models\User::where('id', '!=', $sender->id)->get();

    } else {

        $users = \App\Models\User::whereIn('id', $tujuanIds)->get();
    }

    foreach ($users as $userTujuan) {

        $this->saveSurat(
            $request,
            $sender,
            $userTujuan,
            $nomorSurat,
            
            $fileName
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LOG
    |--------------------------------------------------------------------------
    */

    \App\Models\ActivityLog::create([
        'user_id' => $sender->id,
        'activity' => 'Mengupload Surat Keluar: ' . $request->perihal
    ]);

    /*
    |--------------------------------------------------------------------------
    | WHATSAPP NOTIFIKASI (CLEAN)
    |--------------------------------------------------------------------------
    */

    $tujuanIds = $request->tujuan_id;

    if (in_array('all', $tujuanIds)) {

        $usersToNotify = \App\Models\User::where('id', '!=', $sender->id)->get();

    } else {

        $usersToNotify = \App\Models\User::whereIn('id', $tujuanIds)->get();
    }

    foreach ($usersToNotify as $user) {

        if (!$user->phone) continue;

        $phone = preg_replace('/^0/', '62', $user->phone);

        $message =
            "📨 *NOTIFIKASI SURAT*\n\n" .
            "Yth. {$user->name}\n\n" .
            "Anda menerima surat baru dengan detail:\n\n" .
            "Nomor Surat : {$nomorSurat}\n" .
            "Perihal : {$request->perihal}\n" .
            "Tanggal : {$request->tanggal_surat}\n" .
            ($request->jam ? "Jam : {$request->jam}\n" : "") .
            "Pengirim : {$sender->name}\n\n" .
            "Silakan login ke sistem persuratan.\n\n" .
            "Terima kasih.";

        \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => env('FONTE_TOKEN'),
        ])->post('https://api.fonnte.com/send', [
            'target' => $phone,
            'message' => $message,
        ]);
    }

    return redirect()
        ->route('surat-keluar.index')
        ->with('success', 'Surat keluar berhasil disimpan');
}

/*
|--------------------------------------------------------------------------
| HELPER SIMPAN SURAT
|--------------------------------------------------------------------------
*/

private function saveSurat($request, $sender, $userTujuan, $nomorSurat, $fileName)
{
    \App\Models\SuratKeluar::create([
        'nomor_surat' => $nomorSurat,
        'tujuan_surat' => $userTujuan->name,
        'pengirim_id' => $sender->id,
        'tujuan_id' => $userTujuan->id,
        'tanggal_surat' => $request->tanggal_surat,
        'jam' => $request->jam,
        'perihal' => $request->perihal,
        'penanggung_jawab_id' => $request->penanggung_jawab_id,
        'file' => $fileName,
    ]);

    \App\Models\SuratMasuk::create([
        'nomor_surat' => $nomorSurat,
        'asal_surat' => $sender->name,
        'pengirim_id' => $sender->id,
        'penerima_id' => $userTujuan->id,
        'tanggal_surat' => $request->tanggal_surat,
        'jam' => $request->jam,
        'perihal' => $request->perihal,
        'penanggung_jawab_id' => $request->penanggung_jawab_id,
        'file' => $fileName,
    ]);
}

    // ================== DELETE ==================
   public function destroy($id)
{
    $suratKeluar = SuratKeluar::findOrFail($id);

    if ((int)$suratKeluar->pengirim_id !== (int)auth()->id()) {
        return back()->with('error', 'Akses ditolak');
    }

    $nomorSurat = $suratKeluar->nomor_surat;

    if (
        $suratKeluar->file &&
        file_exists(public_path('uploads/surat_keluar/' . $suratKeluar->file))
    ) {
        unlink(public_path('uploads/surat_keluar/' . $suratKeluar->file));
    }

    SuratKeluar::where('nomor_surat', $nomorSurat)->delete();

    SuratMasuk::where('nomor_surat', $nomorSurat)->delete();

    return back()->with('success', 'Surat berhasil dihapus');
}
    // ================== VIEW FILE ==================
    public function viewFile($id)
{
    $surat = SuratKeluar::findOrFail($id);

    // tandai sudah dibaca
    if (auth()->id() == $surat->tujuan_id) {

        $surat->update([
            'is_read' => true
        ]);
    }

    return response()->file(
        public_path('uploads/surat_keluar/'.$surat->file)
    );
}

    // ================== DOWNLOAD FILE ==================
    public function download($id)
    {
        $surat = SuratKeluar::findOrFail($id);

        return response()->download(
            public_path('uploads/surat_keluar/'.$surat->file)
        );
    }

    public function edit($id)
{
    $surat = SuratKeluar::findOrFail($id);

    if ((int)$surat->pengirim_id !== (int)auth()->id()) {
        abort(403);
    }

    $users = User::where('id', '!=', auth()->id())
        ->whereIn('role', ['user', 'admin'])
        ->get();

    $penanggungJawab = PenanggungJawab::latest()->get();

    $nomorSurat = \App\Models\NomorSurat::where('user_id', auth()->id())
        ->pluck('nomor', 'jenis_surat');
        

    return view('surat_keluar.edit', compact(
        'surat',
        'users',
        'penanggungJawab',
        'nomorSurat'
    ));
}

   public function update(Request $request, $id)
{
    $surat = SuratKeluar::findOrFail($id);

    if ((int)$surat->pengirim_id !== (int)auth()->id()) {
        abort(403);
    }

    // update surat keluar yang dipilih
    $surat->update([
        'tanggal_surat' => $request->tanggal_surat,
        'jam' => $request->jam,
        'perihal' => $request->perihal,
        'penanggung_jawab_id' => $request->penanggung_jawab_id,
    ]);

    // update semua surat masuk dengan nomor surat yang sama
    SuratMasuk::where('nomor_surat', $surat->nomor_surat)
        ->update([
            'tanggal_surat' => $request->tanggal_surat,
            'jam' => $request->jam,
            'perihal' => $request->perihal,
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
        ]);

    return redirect()
        ->route('surat-keluar.index')
        ->with('success', 'Surat berhasil diupdate');
}

    // ================== OCR UPLOAD (SURAT KELUAR) ==================
public function ocr(Request $request)
{
    @set_time_limit(300);
    ini_set('max_execution_time', '300');

    $request->validate([
        'ocr_file' => 'required|mimes:pdf,jpg,jpeg,png|max:4096'
    ]);

    // Simpan file OCR sebagai file utama yang akan dipakai juga saat simpan
    $fileName = time().'_'.$request->file('ocr_file')->getClientOriginalName();
    $request->file('ocr_file')->move(public_path('uploads/surat_keluar'), $fileName);

    $path = public_path('uploads/surat_keluar/'.$fileName);

    // panggil python dari venv-ocr
    $python = base_path('venv-ocr\\Scripts\\python.exe');
    $script = base_path('storage/app/ocr/paddleocr_extract.py');

    if (!file_exists($python)) {
        return back()->with('error', 'Python OCR tidak ditemukan: '.$python);
    }
    if (!file_exists($script)) {
        return back()->with('error', 'Script OCR tidak ditemukan: '.$script);
    }

    $cmd = [$python, $script, $path];

    $result = Process::env([
        'PYTHONUTF8' => '1',
        'PYTHONIOENCODING' => 'utf-8',
        'PYTHONHASHSEED' => '1',
        'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\\Windows',
        'WINDIR'     => getenv('WINDIR')     ?: 'C:\\Windows',
        'PATH'       => getenv('PATH')       ?: '',
    ])
    ->timeout(300)
    ->run($cmd);

    if (!$result->successful()) {
        return back()->with('error', 'OCR gagal: '.$result->errorOutput());
    }

    // Ambil JSON dari baris terakhir yang valid
    $output = trim($result->output());
    $linesOut = preg_split("/\R/", $output);
    $jsonLine = null;
    for ($i = count($linesOut) - 1; $i >= 0; $i--) {
        $try = trim($linesOut[$i]);
        if ($try === '') continue;
        if (str_starts_with($try, '{') && str_ends_with($try, '}')) {
            $jsonLine = $try;
            break;
        }
    }
    if (!$jsonLine) {
        return back()->with('error', 'OCR output tidak valid JSON: '.$output);
    }

    $json = json_decode($jsonLine, true);
    if (!$json || empty($json['ok'])) {
        return back()->with('error', 'OCR gagal: '.$jsonLine);
    }

    $text = $json['text'] ?? '';
    $parsed = $this->parseOcrTextKeluar($text);

   return redirect()
    ->route('surat-keluar.create')
    ->with([
        'prefill' => $parsed,
        'uploaded_file_name' => $fileName,
    ]);

}

// ================== PARSER OCR (SURAT KELUAR) ==================
private function parseOcrTextKeluar(string $text): array
{
    $raw = str_replace(["\r"], "", $text);
    $rawNorm = preg_replace("/[ \t]+/", " ", $raw);

    $lines = array_values(array_filter(array_map(function ($l) {
        $l = trim(preg_replace("/[ \t]+/", " ", $l));
        return $l === '' ? null : $l;
    }, explode("\n", $rawNorm))));

    // helper normalisasi nomor surat
    $normCode = function (string $s): string {
        $s = strtoupper($s);
        $s = str_replace([" ", "\t"], "", $s);
        $s = str_replace(['|'], ['I'], $s);
        $s = preg_replace('/\/+/', '/', $s);
        $s = preg_replace('/[^A-Z0-9\/\-\.\(\)]/', '', $s);
        return $s;
    };

    // 1) NOMOR SURAT (ambil kandidat terbaik setelah label "Nomor")
    $nomor = null;
    $idxNomorLabel = null;
    foreach ($lines as $i => $line) {
        if (preg_match('/\bnomor\b|\bnomer\b|\bno\b/i', $line)) {
            $idxNomorLabel = $i;
            break;
        }
    }
    if ($idxNomorLabel !== null) {
        $best = null; $bestScore = -999;
        for ($k = $idxNomorLabel + 1; $k <= min($idxNomorLabel + 8, count($lines) - 1); $k++) {
            $lRaw = trim($lines[$k]);
            if ($lRaw === '') continue;
            $lUp = strtoupper($lRaw);

            // stop kalau sudah masuk tanggal
            if (preg_match('/\b\d{1,2}\s+(JANUARI|FEBRUARI|MARET|APRIL|MEI|JUNI|JULI|AGUSTUS|SEPTEMBER|OKTOBER|NOVEMBER|DESEMBER)\s+\d{4}\b/i', $lUp)) {
                break;
            }

            if (strpos($lUp, '/') === false) continue;

            // buang kalimat yang mengandung kata umum
            if (preg_match('/\b(TIDAK|RAPAT|DISIPLIN|SEHUBUNGAN|DENGAN|YANG|MAKA)\b/i', $lUp)) continue;

            $slashCount = substr_count($lUp, '/');
            $len = mb_strlen($lUp);
            $score = $slashCount * 3;
            if ($len >= 10 && $len <= 70) $score += 4;
            if (preg_match('/^\d{1,6}\//', $lRaw)) $score += 5;

            $candidate = $normCode($lRaw);
            if ($score > $bestScore) { $bestScore = $score; $best = $candidate; }
        }
        if ($best) $nomor = $best;
    }

    // 2) TANGGAL
    $tanggal = null;
    $bulanMap = [
        'januari'=>1,'februari'=>2,'maret'=>3,'april'=>4,'mei'=>5,'juni'=>6,
        'juli'=>7,'agustus'=>8,'september'=>9,'oktober'=>10,'november'=>11,'desember'=>12
    ];

    $fixDateGlue = preg_replace(
        '/(\d{1,2})(januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember)\b/i',
        '$1 $2',
        $rawNorm
    );

    if (preg_match('/\b(\d{1,2})\s+(januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember)\s+(\d{4})\b/i', $fixDateGlue, $m)) {
        $day = (int)$m[1];
        $month = $bulanMap[strtolower($m[2])] ?? null;
        $year = (int)$m[3];
        if ($month) $tanggal = sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    // 3) PERIHAL
    $perihal = null;
    for ($i=0; $i<count($lines); $i++) {
        $line = $lines[$i];
        if (preg_match('/\b(perihal|hal)\b/i', $line)) {
            if (preg_match('/\b(perihal|hal)\b\s*[:\-]?\s*(.+)$/i', $line, $m)) {
                $cand = trim($m[2]);
                if (strlen($cand) >= 4) { $perihal = $cand; break; }
            }
            $next = $lines[$i+1] ?? null;
            if ($next && strlen(trim($next)) >= 4) { $perihal = trim($next); break; }
        }
    }

    // 4) TUJUAN SURAT (Yth/Kepada)
    $tujuan = null;
    $idx = null;

    foreach ($lines as $i => $line) {
        if (preg_match('/\b(yth\.?|kepada)\b/i', $line)) { $idx = $i; break; }
    }

    if ($idx !== null) {
        $buf = [];

        // ambil 1-4 baris setelah Yth/Kepada sampai ketemu "di -" atau "Nomor/Perihal"
        for ($k = $idx; $k <= min($idx + 5, count($lines)-1); $k++) {
            $l = trim($lines[$k]);
            if ($l === '') continue;

            // stop conditions
            if ($k !== $idx && preg_match('/\b(nomor|perihal|hal|lamp|tanggal)\b/i', $l)) break;

            // hapus "Yth." / "Kepada"
            $l = preg_replace('/\b(yth\.?|kepada)\b\s*/i', '', $l);
            $l = trim($l);
            if ($l === '') continue;

            $buf[] = $l;

            // kalau baris "di Tempat" atau "di-" biasanya akhir tujuan
            if (preg_match('/\bdi\b/i', $l) && mb_strlen($l) <= 20) break;
            if (count($buf) >= 3) break;
        }

            if (!empty($buf)) {
        $tujuan = trim(implode(' ', $buf));

        // 🔥 BUANG KATA SOPANAN UMUM
        $tujuan = preg_replace('/\b(dengan\s+hormat[,:\.]*)\b/i', '', $tujuan);

        // buang koma / titik sisa di awal / akhir
        $tujuan = trim($tujuan, " \t\n\r\0\x0B,.-");

        // rapikan spasi
        $tujuan = preg_replace('/\s+/', ' ', $tujuan);
    }

    }

    return [
        'nomor_surat'   => $nomor,
        'tujuan_surat'  => $tujuan,
        'tanggal_surat' => $tanggal,
        'perihal'       => $perihal,
    ];
}



}
