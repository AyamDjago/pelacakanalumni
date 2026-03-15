# Sistem Pelacakan Alumni Digital

Proyek ini adalah implementasi dari desain **Daily Project 2** untuk mata kuliah Rekayasa Kebutuhan. Sistem ini berupa aplikasi web berbasis Laravel yang berfungsi untuk melacak rekam jejak digital alumni secara otomatis menggunakan algoritma pencarian dinamis (Query Builder), ekstraksi sinyal identitas, dan perhitungan skor keyakinan (Confidence Score).

## Tautan Proyek
* **Source Code GitHub:** [https://github.com/AyamDjago/pelacakanalumni](https://github.com/AyamDjago/pelacakanalumni)
* **Live Website URL:** *(https://pelacakan-alumni.lovestoblog.com/?i=1)]*

## Teknologi yang Digunakan
* **Framework Backend:** Laravel (PHP)
* **Frontend:** Blade Templating Engine (HTML/CSS)
* **Data Source API:** HTTP Client & JSON Parsing (Integrasi SerpApi / Simulasi Data Scraping)

## Tabel Pengujian Aplikasi (Quality Assurance)
Pengujian Black-Box berikut dilakukan untuk memvalidasi pemenuhan aspek kualitas dan kesesuaian sistem dengan *pseudocode* yang dirancang pada Daily Project 2.

| No | Modul / Skenario Pengujian | Deskripsi Berdasarkan Desain | Hasil yang Diharapkan | Status |
|----|----------------------------|------------------------------|-----------------------|--------|
| 1 | **Query Builder** | Menguji pembentukan query dinamis dengan input "Nama", "Prodi", dan "Kampus" | [cite_start]Sistem berhasil membangun string pencarian dengan operator logika secara otomatis (contoh: `site:linkedin.com/in/ "Nama" "Prodi"`). | ✅ Lulus |
| 2 | **Perhitungan Confidence Score** | Menguji sistem pembobotan berdasarkan kecocokan sinyal identitas (Nama, Kampus, Prodi, Tahun) | [cite_start]Sistem berhasil menghitung Total Confidence Score (0-100) dengan benar[cite: 51]. (Contoh: Nama persis & kampus cocok = 50 poin, Prodi sesuai = 20 poin). | ✅ Lulus |
| 3 | **Decision Making: Teridentifikasi** | Memasukkan kandidat dengan skor kecocokan lebih dari 80 poin (> 80) | [cite_start]Sistem secara otomatis mengklasifikasikan status sebagai **"Teridentifikasi Otomatis"**. | ✅ Lulus |
| 4 | **Decision Making: Verifikasi** | Memasukkan kandidat dengan skor kecocokan menengah (antara 40 - 80 poin) | [cite_start]Sistem mengklasifikasikan status sebagai **"Perlu Verifikasi Manual"** untuk ditinjau oleh Admin. | ✅ Lulus |
| 5 | **Decision Making: Gagal** | Memasukkan kandidat dengan skor kecocokan sangat rendah (< 40 poin) atau tidak ada data relevan di internet | [cite_start]Sistem mengklasifikasikan status sebagai **"Jejak Tidak Ditemukan"**. | ✅ Lulus |
| 6 | **Persistence & Audit Trail** | Memverifikasi penyimpanan rekam jejak sebagai bukti penelusuran | [cite_start]Sistem berhasil menampilkan "Snapshot" temuan yang berisi: URL bukti asli, cuplikan teks (snippet), dan tanggal akses (timestamp). | ✅ Lulus |

## Cara Menjalankan Aplikasi di Lokal
1. Clone repository ini: `git clone https://github.com/AyamDjago/pelacakanalumni.git`
2. Masuk ke direktori: `cd pelacakanalumni`
3. Install dependencies: `composer install`
4. Copy file environment: `cp .env.example .env` lalu generate key `php artisan key:generate`
5. Jalankan server lokal: `php artisan serve`
6. Akses di browser: `http://127.0.0.1:8000`

---
*Dibuat oleh: Muhammad Hilman Al Hazmi (NIM: 202310370311229) - Kelas Rekayasa Kebutuhan A*
