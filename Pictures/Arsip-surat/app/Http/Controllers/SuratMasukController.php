<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process  ;

class SuratMasukController extends Controller
{
    // ================== INDEX + SEARCH ==================
   public function index(Request $request)
{
    $keyword = $request->keyword;
    $tanggal = $request->tanggal;

    $data = SuratMasuk::where('penerima_id', auth()->id())

        ->when($keyword, function ($query) use ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('perihal', 'like', "%{$keyword}%")
                  ->orWhere('asal_surat', 'like', "%{$keyword}%")
                  ->orWhere('nomor_surat', 'like', "%{$keyword}%");
            });
        })

        ->when($tanggal, function ($query) use ($tanggal) {
            $query->whereDate('tanggal_surat', $tanggal);
        })

        ->orderBy('tanggal_surat', 'desc')
        ->get();

    return view('surat_masuk.index', compact(
        'data',
        'keyword',
        'tanggal'
    ));
}

    //====filter jenis surat======
    public function filter(Request $request)
{
    $jenis = $request->jenis;

    $data = SuratMasuk::where('penerima_id', auth()->id())
        ->when($jenis, function ($query) use ($jenis) {
            $query->where('perihal', $jenis);
        })
        ->orderBy('tanggal_surat', 'desc')
        ->get();

    return view('surat_masuk.index', compact('data'));
}

    // ================== CREATE ==================
    public function create()
    {
        return view('surat_masuk.create', [
            'prefill' => session('prefill', []),
            'uploaded_file_name' => session('uploaded_file_name'),
        ]);
    }

    // ================== STORE ==================
    public function store(Request $request)
    {
        $request->validate([
            'nomor_surat'   => 'required',
            'asal_surat'    => 'required',
            'tanggal_surat' => 'required|date',
            'perihal'       => 'required',
            'penanggung_jawab_id' => 'nullable|exists:penanggung_jawabs,id',
            'file'          => 'nullable|mimes:pdf,jpg,jpeg,png,xlsx,xls,csv|max:4096',
            'uploaded_file_name' => 'nullable|string',
        ]);

        // FILE SURAT (dari OCR atau upload manual)
        $fileName = null;

        // 1) dari OCR (file sudah disimpan sebelumnya)
        if ($request->filled('uploaded_file_name')) {
            $fileName = $request->uploaded_file_name;
        }

        // 2) upload manual dari form create (override jika ada)
        if ($request->hasFile('file')) {
            $fileName = time().'_'.$request->file('file')->getClientOriginalName();
            $request->file('file')->move(public_path('uploads/surat_masuk'), $fileName);
        }

        SuratMasuk::create([
    'nomor_surat'   => $request->nomor_surat,
    'asal_surat'    => $request->asal_surat,
    'pengirim_id'   => auth()->id(),
    'penerima_id'   => auth()->id(), // sementara
    'tanggal_surat' => $request->tanggal_surat,
    'jam'           => $request->jam,
    'perihal'       => $request->perihal,
    'file'          => $fileName,
]);

        ActivityLog::create([
            'user_id'  => auth()->id(),
            'activity' => 'Mengupload Surat Masuk: '.$request->perihal
        ]);

        // NOTE: sebelumnya kamu return dengan $parsed yang belum didefinisikan -> error.
        // Setelah store, biasanya cukup redirect dengan flash success.
        return redirect()
            ->route('surat-masuk.index')
            ->with('success', 'Surat masuk berhasil disimpan.')
            ->with([
                'prefill' => [],                 // kosongkan prefill setelah sukses simpan
                'uploaded_file_name' => $fileName, // kalau mau tetap ada, boleh
            ]);
    }

    // ================== DELETE ==================
    public function destroy($id)
{
    $suratMasuk = SuratMasuk::findOrFail($id);

    // hanya penerima yang boleh menghapus
    if ((int)$suratMasuk->penerima_id !== (int)auth()->id()) {
        return back()->with('error', 'Akses ditolak');
    }

    // hapus file jika ada
    if (
        $suratMasuk->file &&
        file_exists(public_path('uploads/surat_masuk/' . $suratMasuk->file))
    ) {
        unlink(public_path('uploads/surat_masuk/' . $suratMasuk->file));
    }

    // hapus record database
    $suratMasuk->delete();

    return redirect()
        ->route('surat-masuk.index')
        ->with('success', 'Surat berhasil dihapus');
}

    // ================== DOWNLOAD FILE ==================
    public function download($id)
{
    $surat = SuratMasuk::findOrFail($id);

    return response()->download(
        public_path('uploads/surat_keluar/'.$surat->file)
    );
}

    // ================== OCR UPLOAD ==================
        public function ocr(Request $request)
    {
        // biar request OCR tidak mati cepat
        @set_time_limit(300);
        ini_set('max_execution_time', '300');

        $request->validate([
            'ocr_file' => 'required|mimes:pdf,jpg,jpeg,png|max:4096'
        ]);

        // simpan file OCR sebagai FILE UTAMA (biar bisa download)
        $fileName = time().'_'.$request->file('ocr_file')->getClientOriginalName();
        $request->file('ocr_file')->move(public_path('uploads/surat_masuk'), $fileName);

        $path = public_path('uploads/surat_masuk/'.$fileName);

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

        // Kadang python library nulis log dulu sebelum JSON.
        // Ambil JSON dari baris terakhir yang valid.
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
            return back()->with('error', 'OCR gagal membaca output JSON: '.$jsonLine);
        }

        $text = $json['text'] ?? '';

        // parse field dari text
        $parsed = $this->parseOcrText($text);

        $allEmpty = empty($parsed['nomor_surat']) && empty($parsed['tanggal_surat']) && empty($parsed['perihal']);
        if ($allEmpty) {
            return view('surat_masuk.create', [
                'prefill' => $parsed,
                'uploaded_file_name' => $fileName,
            ])->with('error', 'OCR berhasil, tapi data tidak terbaca. Isi manual ya.');
        }

        return view('surat_masuk.create', [
            'prefill' => $parsed,
            'uploaded_file_name' => $fileName,
        ]);
    }


    // ================== PARSER OCR ==================
            private function parseOcrText(string $text): array
    {
        $raw = str_replace(["\r"], "", $text);
        $rawNorm = preg_replace("/[ \t]+/", " ", $raw);

        $lines = array_values(array_filter(array_map(function ($l) {
            $l = trim(preg_replace("/[ \t]+/", " ", $l));
            return $l === '' ? null : $l;
        }, explode("\n", $rawNorm))));

        $joinedLower = strtolower(implode("\n", $lines));

        // normalisasi untuk nomor surat
        $normCode = function (string $s): string {
            $s = strtoupper($s);
            $s = str_replace([" ", "\t"], "", $s);
            $s = str_replace(['|'], ['I'], $s);
            $s = preg_replace('/\/+/', '/', $s);
            $s = preg_replace('/[^A-Z0-9\/\-\.\(\)]/', '', $s);
            return $s;
        };

       // =========================
        // 1) NOMOR SURAT (FIX)
        // =========================
        $nomor = null;

        $idxNomorLabel = null;
        foreach ($lines as $i => $line) {
            if (preg_match('/\bnomor\b|\bnomer\b|\bno\b/i', $line)) {
                $idxNomorLabel = $i;
                break;
            }
        }

        if ($idxNomorLabel !== null) {
            $best = null;
            $bestScore = -999;

            // cek max 8 baris setelah label
            for ($k = $idxNomorLabel + 1; $k <= min($idxNomorLabel + 8, count($lines) - 1); $k++) {
                $lRaw = trim($lines[$k]);
                if ($lRaw === '') continue;

                $lUp = strtoupper($lRaw);

                // stop kalau sudah ketemu baris tanggal/kota (biasanya setelah nomor)
                if (preg_match('/\b\d{1,2}\s+(JANUARI|FEBRUARI|MARET|APRIL|MEI|JUNI|JULI|AGUSTUS|SEPTEMBER|OKTOBER|NOVEMBER|DESEMBER)\s+\d{4}\b/i', $lUp)
                    || preg_match('/\b\d{4}\b/', $lUp) && preg_match('/\b(LHOKSEUMAWE|BANDA|MEDAN|JAKARTA|BANDA\s*ACEH)\b/i', $lUp)
                ) {
                    break;
                }

                // kandidat wajib punya "/" dan tidak boleh kalimat (mengandung kata-kata)
                if (strpos($lUp, '/') === false) continue;

                // buang kandidat yang jelas kalimat
                if (preg_match('/\b(TIDAK|RAPAT|DISIPLIN|SEHUBUNGAN|DENGAN|YANG|MAKA)\b/i', $lUp)) continue;

                // skor: banyak segmen "/" biasanya nomor surat
                $slashCount = substr_count($lUp, '/');
                $len = mb_strlen($lUp);

                $score = 0;
                $score += $slashCount * 3;
                if ($len >= 10 && $len <= 60) $score += 4;

                // pola umum nomor surat: diawali angka
                if (preg_match('/^\s*\d{1,6}\s*\/.+/i', $lRaw)) $score += 5;

                // roman month (IV, V, VI, dst) sering ada
                if (preg_match('/\/(I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII)\b/i', $lUp)) $score += 2;

                // normalisasi ringkas
                $candidate = $normCode($lRaw);

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = $candidate;
                }
            }

            if ($best) $nomor = $best;
        }

        // =========================
        // 2) TANGGAL
        // =========================
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

        if (preg_match('/\b([A-Za-z\.\s]{2,30})\,?\s*(\d{1,2})\s+(januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember)\s+(\d{4})\b/i', $fixDateGlue, $m)) {
            $day = (int)$m[2];
            $month = $bulanMap[strtolower($m[3])] ?? null;
            $year = (int)$m[4];
            if ($month) $tanggal = sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        if (!$tanggal && preg_match('/\b(\d{1,2})\s+(januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember)\s+(\d{4})\b/i', $fixDateGlue, $m)) {
            $day = (int)$m[1];
            $month = $bulanMap[strtolower($m[2])] ?? null;
            $year = (int)$m[3];
            if ($month) $tanggal = sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        // ✅ Pindahkan penambahan tahun nomor ke sini (setelah $tanggal sudah ada)
        if ($nomor && !preg_match('/\/20\d{2}$/', $nomor) && !empty($tanggal)) {
            $year = substr($tanggal, 0, 4);
            $nomor .= '/'.$year;
        }

        // =========================
        // 2.5) JAM
        // =========================
        $jam = null;

        if (preg_match('/\b(pukul|jam|waktu)\s*[:\-]?\s*(\d{1,2})[\.:](\d{2})/i', $rawNorm, $m)) {

            $jam = sprintf('%02d:%02d:00', $m[2], $m[3]);

        } elseif (preg_match('/\b(\d{1,2})[\.:](\d{2})\s*(WIB|WITA|WIT)?\b/i', $rawNorm, $m)) {

            $jam = sprintf('%02d:%02d:00', $m[1], $m[2]);
        }

        // =========================
        // 3) PERIHAL
        // =========================
        $perihal = null;
        for ($i=0; $i<count($lines); $i++) {
            $line = $lines[$i];

            if (preg_match('/\b(perihal|hal)\b/i', $line)) {
                if (preg_match('/\b(perihal|hal)\b\s*[:\-]?\s*(.+)$/i', $line, $m)) {
                    $candidate = trim($m[2]);
                    if (strlen($candidate) >= 4) { $perihal = $candidate; break; }
                }
                $next = $lines[$i+1] ?? null;
                if ($next && strlen(trim($next)) >= 4) { $perihal = trim($next); break; }
            }
        }
        if (!$perihal && preg_match('/surat\s+peringatan/i', $joinedLower)) {
            $perihal = 'Surat Peringatan';
        }

        // =========================
        // 4) ASAL SURAT (SUPER RINGKAS)
        // =========================
        $asal = null;

        // ambil header sampai sebelum "Nomor" / "No" / "Perihal" / "Hal"
        $header = [];
        foreach ($lines as $l) {
            $l = trim($l);
            if ($l === '') continue;

            if (preg_match('/\b(nomor|nomer|no\.?|lamp|hal|perihal|tanggal)\b/i', $l)) {
                break;
            }

            // buang baris yang jelas bukan instansi
            if (preg_match('/\b(jl\.?|jalan|telp|fax|kode\s*pos|email|http|www)\b/i', $l)) continue;
            if (preg_match('/\d{1,6}\/[A-Z0-9]/i', $l) || substr_count($l, '/') >= 2) continue; // nomor surat
            if (preg_match('/\b(lhokseumawe|jakarta|medan|banda|aceh)\b/i', $l)) continue; // kota (opsional)

            // rapikan bracket
            $l = preg_replace('/[\[\]]/', '', $l);
            $l = preg_replace('/\s+/', ' ', trim($l));

            $header[] = $l;
            if (count($header) >= 10) break;
        }

        // 1) Prioritas: "HIMPUNAN ..." + singkatan (baris berikutnya pendek)
        $idxHimp = null;
        foreach ($header as $i => $l) {
            if (preg_match('/\bHIMPUNAN\b/i', $l)) { $idxHimp = $i; break; }
        }

        if ($idxHimp !== null) {
            $name = $header[$idxHimp];
            $abbr = $header[$idxHimp + 1] ?? '';

            // kalau baris setelahnya singkatan (contoh HIMATESIN), gabungkan sebagai "(HIMATESIN)"
            if ($abbr && mb_strlen($abbr) <= 15 && preg_match('/^[A-Z0-9\-]+$/', strtoupper($abbr))) {
                $asal = $name . ' (' . strtoupper($abbr) . ')';
            } else {
                $asal = $name;
            }
        }

        // 2) Fallback: ambil 1 baris "UNIVERSITAS/FAKULTAS/DINAS/PT/RS" terpanjang tapi masuk akal
        if (!$asal) {
            $candidates = [];
            foreach ($header as $l) {
                if (preg_match('/\b(UNIVERSITAS|FAKULTAS|DINAS|BADAN|KEMENTerian|SEKOLAH|MADRASAH|YAYASAN|PT|CV|RUMAH SAKIT)\b/i', $l)) {
                    $candidates[] = $l;
                }
            }
            if (!empty($candidates)) {
                usort($candidates, fn($a,$b) => mb_strlen($b) <=> mb_strlen($a));
                $asal = $candidates[0];
            }
        }

        // 3) kalau masih kosong, ambil baris pertama header
        if (!$asal && !empty($header)) {
            $asal = $header[0];
        }

        // hard limit biar nggak pernah kepanjangan
        if ($asal && mb_strlen($asal) > 60) {
            $asal = mb_substr($asal, 0, 60) . '…';
        }

        return [
            'nomor_surat'   => $nomor,
            'asal_surat'    => $asal,
            'tanggal_surat' => $tanggal,
            'jam'           => $jam,
            'perihal'       => $perihal,
            
        ];
    }


}
