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

        $queryB = 'site:linkedin.com/in/ "' . $targetProfile['nama'] . '" "' . $targetProfile['prodi'] . '"';

        $apiKey = '31cbe9af9e7e0bf643254453516d6ebd20d972d77182ca62e424c341205f3cce'; 
        
        $response = Http::get('https://serpapi.com/search.json', [
            'engine' => 'google',
            'q' => $queryB,
            'api_key' => $apiKey,
            'num' => 3
        ]);

        $data = $response->json();
        $mockSignals = [
            'nama' => null, 'kampus' => null, 'tahun_aktif' => null, 
            'prodi' => null, 'lokasi' => null, 'cross_validated' => false
        ];
        $auditTrail = null;
        $score = 0;

        if (isset($data['organic_results']) && count($data['organic_results']) > 0) {
            $hasilPertama = $data['organic_results'][0];
            $teksSnippet = strtolower($hasilPertama['snippet'] ?? '');
            $judul = strtolower($hasilPertama['title'] ?? '');

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
            
            $auditTrail = [
                'snapshot_url' => $hasilPertama['link'],
                'cuplikan_teks' => $hasilPertama['snippet'] ?? 'Tidak ada cuplikan',
                'tanggal_akses' => Carbon::now()->toDateTimeString()
            ];
        }

        $status = $this->determineStatus($score);

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