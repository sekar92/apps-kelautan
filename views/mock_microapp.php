<?php
// Interactive Mock Micro-App Engine (GAS & SPA Simulation inside iFrame)
$type = $type ?? 'pos';
$titles = [
    'pos' => 'Sistem Kasir Pelelangan Ikan (POS TPI)',
    'logbook' => 'E-Logbook Penangkapan Ikan Nahkoda',
    'cuaca' => 'Google Apps Script: Oseanografi & Cuaca BMKG',
    'presensi' => 'Presensi & Jadwal Awak Kapal (ABK)',
    'pnbp' => 'Kalkulator Simulasi Tarif PNBP Pascaproduksi',
    'spb' => 'Pengajuan SPB Digital (Surat Persetujuan Berlayar)',
    'alat-tangkap' => 'Inventarisasi Alat Tangkap Ikan Berkelanjutan',
    'cold-storage' => 'Sensor IoT Suhu Ruang Beku Kapal'
];

$title = $titles[$type] ?? 'Aplikasi Mikro Kelautan Terpadu';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a;
            color: #f8fafc;
            padding: 24px;
            font-size: 14px;
        }

        .micro-card {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .auth-banner {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 8px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .auth-pill {
            background: #10b981;
            color: #000;
            font-weight: 700;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 99px;
            text-transform: uppercase;
        }

        .btn-action {
            background: #0284c7;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }

        .btn-action:hover {
            background: #0369a1;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 640px) {
            .grid-2 { grid-template-columns: 1fr; }
        }

        .stat-box {
            background: #0b1329;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .item-list {
            list-style: none;
            margin-top: 10px;
        }

        .item-list li {
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 6px;
            margin-bottom: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

    <!-- Sesi Autentikasi yang Diteruskan (FR 5.3) -->
    <div class="auth-banner">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 18px;"></i>
            <div>
                <strong>Autentikasi Terverifikasi dari Parent Portal (FR 5.3):</strong>
                <span style="color: #94a3b8;">User: <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)</span>
            </div>
        </div>
        <div class="auth-pill"><i class="fa-solid fa-lock"></i> Sesi Aktif</div>
    </div>

    <!-- Konten Interaktif Micro-App -->
    <div class="micro-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
            <div>
                <h2 style="font-size: 20px; font-weight: 700; color: #38bdf8;"><?= htmlspecialchars($title) ?></h2>
                <p style="color: #94a3b8; font-size: 13px;">Micro-app berjalan di lingkungan iFrame terisolasi (sandbox).</p>
            </div>
            <div>
                <button class="btn-action" onclick="alert('Sinkronisasi data ke server kelautan berhasil!')">
                    <i class="fa-solid fa-arrows-rotate"></i> Sinkronisasi
                </button>
            </div>
        </div>

        <div class="grid-2">
            <div class="stat-box">
                <div style="color: #94a3b8; font-size: 12px; margin-bottom: 4px;">Status Operasional Dermaga</div>
                <div style="font-size: 24px; font-weight: 700; color: #10b981;">Normal / Aktif</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Terakhir diperbarui: Baru saja</div>
            </div>

            <div class="stat-box">
                <div style="color: #94a3b8; font-size: 12px; margin-bottom: 4px;">Volume Transaksi / Entri Hari Ini</div>
                <div style="font-size: 24px; font-weight: 700; color: #f59e0b;" id="counterDisplay">148 Lot</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Target harian: 200 Lot</div>
            </div>
        </div>
    </div>

    <div class="micro-card">
        <h3 style="font-size: 15px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-clipboard-list" style="color: #38bdf8;"></i> Daftar Entri Terkini
        </h3>

        <ul class="item-list">
            <li>
                <div>
                    <strong>Ikan Cakalang Segar (Katsuwonus pelamis)</strong>
                    <div style="font-size: 11px; color: #94a3b8;">Kapal KM Mina Bahari 08 &bull; Dermaga 2</div>
                </div>
                <div><span style="color: #10b981; font-weight: 600;">450 Kg</span> &bull; Rp 11.250.000</div>
            </li>
            <li>
                <div>
                    <strong>Tuna Sirip Kuning Grade A (Thunnus albacares)</strong>
                    <div style="font-size: 11px; color: #94a3b8;">Kapal KM Samudra Perkasa &bull; Dermaga 1</div>
                </div>
                <div><span style="color: #10b981; font-weight: 600;">820 Kg</span> &bull; Rp 49.200.000</div>
            </li>
            <li>
                <div>
                    <strong>Tongkol Komo (Euthynnus affinis)</strong>
                    <div style="font-size: 11px; color: #94a3b8;">Kapal KM Berkah Nelayan &bull; Dermaga 3</div>
                </div>
                <div><span style="color: #10b981; font-weight: 600;">210 Kg</span> &bull; Rp 4.200.000</div>
            </li>
        </ul>

        <div style="margin-top: 16px; display: flex; gap: 10px;">
            <button class="btn-action" onclick="addMockEntry()">
                <i class="fa-solid fa-plus"></i> Tambah Transaksi Demo
            </button>
            <button class="btn-action" style="background: rgba(255,255,255,0.08);" onclick="printReceipt()">
                <i class="fa-solid fa-print"></i> Cetak Dokumen
            </button>
        </div>
    </div>

    <script>
        let counter = 148;
        function addMockEntry() {
            counter++;
            document.getElementById('counterDisplay').innerText = counter + ' Lot';
            alert('Entri baru berhasil dicatat oleh pengguna ' + <?= json_encode($user['name']) ?> + '!');
        }

        function printReceipt() {
            alert('Mencetak berkas manifest / struk TPI...');
        }
    </script>
</body>
</html>
