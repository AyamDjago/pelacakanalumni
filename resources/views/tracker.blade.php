<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pelacakan Alumni Digital</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f7f6; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input, button { display: block; width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; border: none; cursor: pointer; font-weight: bold; }
        .result-box { background-color: #e9ecef; padding: 15px; border-left: 5px solid #28a745; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Sistem Pelacakan Alumni Digital (Daily Project 2)</h2>
        <form action="{{ route('track.alumni') }}" method="POST">
            @csrf
            <label>Nama Lengkap:</label>
            <input type="text" name="nama" required placeholder="Contoh: Muhammad Hilman Al Hazmi">
            
            <label>Program Studi:</label>
            <input type="text" name="prodi" required placeholder="Contoh: Informatika">
            
            <label>Tahun Lulus:</label>
            <input type="number" name="tahun_lulus" required placeholder="Contoh: 2024">
            
            <label>Kota Asal:</label>
            <input type="text" name="kota_asal" required placeholder="Contoh: Malang">
            
            <button type="submit">Jalankan Pelacakan Otomatis</button>
        </form>

        @if(session('result'))
            <div class="result-box">
                <h3>Hasil Pelacakan untuk: {{ session('alumni')['nama'] }}</h3>
                <p><strong>Confidence Score:</strong> {{ session('score') }}/100</p>
                <p><strong>Status Klasifikasi:</strong> {{ session('status') }}</p>
                
                <h4>Query yang Dihasilkan:</h4>
                <ul>
                    @foreach(session('queries') as $label => $query)
                        <li><strong>{{ $label }}:</strong> <code>{{ $query }}</code></li>
                    @endforeach
                </ul>

                <h4>Audit Trail (Bukti):</h4>
                <ul>
                    <li><strong>URL:</strong> <a href="{{ session('audit')['snapshot_url'] }}" target="_blank">{{ session('audit')['snapshot_url'] }}</a></li>
                    <li><strong>Cuplikan:</strong> "{{ session('audit')['cuplikan_teks'] }}"</li>
                    <li><strong>Tanggal:</strong> {{ session('audit')['tanggal_akses'] }}</li>
                </ul>
            </div>
        @endif
    </div>
</body>
</html>