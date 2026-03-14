<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AlumniTrackerController extends Controller
{
    public function index()
    {
        return view('tracker');
    }

    public function track(Request $request)
    {
        $targetProfile = [
            'nama' => $request->input('nama'),
            'kampus' => 'Universitas Muhammadiyah Malang',
            'prodi' => $request->input('prodi'),
            'tahun_lulus' => $request->input('tahun_lulus'),
            'kota_asal' => $request->input('kota_asal')
        ];

        // Buat Query B sesuai desain (mencari di LinkedIn via Google)
        $queryB = 'site:linkedin.com/in/ "' . $targetProfile['nama'] . '" "' . $targetProfile['prodi'] . '"';

        // --- MENGAMBIL DATA ASLI MENGGUNAKAN SERPAPI ---
        // GANTI 'API_KEY_KAMU_DISINI' DENGAN API KEY DARI SERPAPI.COM
        $apiKey = '31cbe9af9e7e0bf643254453516d6ebd20d972d77182ca62e424c341205f3cce'; 
        
        $response = Http::get('https://serpapi.com/search.json', [
            'engine' => 'google',
            'q' => $queryB,
            'api_key' => $apiKey,
            'num' => 3 // Ambil 3 hasil teratas saja
        ]);

        $data = $response->json();
        $mockSignals = [
            'nama' => null, 'kampus' => null, 'tahun_aktif' => null, 
            'prodi' => null, 'lokasi' => null, 'cross_validated' => false
        ];
        $auditTrail = null;
        $score = 0;

        // 5) Sistem Menarik Data dari Sumber Publik & Melakukan Parsing [cite: 30, 32]
        if (isset($data['organic_results']) && count($data['organic_results']) > 0) {
            $hasilPertama = $data['organic_results'][0];
            $teksSnippet = strtolower($hasilPertama['snippet'] ?? '');
            $judul = strtolower($hasilPertama['title'] ?? '');

            // 6) Ekstraksi Sinyal Identitas [cite: 34]
            // Cek apakah nama dan prodi/kampus muncul di teks asli dari Google
            if (str_contains($judul, strtolower($targetProfile['nama']))) {
                $mockSignals['nama'] = $targetProfile['nama'];
            }
            if (str_contains($teksSnippet, 'muhammadiyah malang') || str_contains($teksSnippet, 'umm')) {
                $mockSignals['kampus'] = $targetProfile['kampus'];
            }
            if (str_contains($teksSnippet, strtolower($targetProfile['prodi']))) {
                $mockSignals['prodi'] = $targetProfile['prodi'];
            }

            $score = $this->calculateConfidenceScore($mockSignals, $targetProfile);
            
            // 10) Snapshot Bukti Temuan [cite: 63, 64]
            $auditTrail = [
                'snapshot_url' => $hasilPertama['link'],
                'cuplikan_teks' => $hasilPertama['snippet'] ?? 'Tidak ada cuplikan',
                'tanggal_akses' => Carbon::now()->toDateTimeString()
            ];
        }

        $status = $this->determineStatus($score);

        // Jika tidak ketemu
        if (!$auditTrail) {
            $auditTrail = [
                'snapshot_url' => '-', 'cuplikan_teks' => '-', 'tanggal_akses' => Carbon::now()->toDateTimeString()
            ];
        }

        return back()->with([
            'result' => true,
            'alumni' => $targetProfile,
            'queries' => ['Query_B' => $queryB],
            'score' => $score,
            'status' => $status,
            'audit' => $auditTrail
        ]);
    }

    private function calculateConfidenceScore($signals, $target)
    {
        $score = 0;
        if ($signals['nama'] == $target['nama']) $score += 30;
        if ($signals['kampus'] == $target['kampus']) $score += 20;
        if ($signals['prodi'] == $target['prodi']) $score += 20;
        return min($score, 100);
    }

    private function determineStatus($score)
    {
        if ($score > 80) return "Teridentifikasi Otomatis";
        elseif ($score >= 40 && $score <= 80) return "Perlu Verifikasi Manual";
        else return "Jejak Tidak Ditemukan";
    }
}