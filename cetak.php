<?php 
require_once('includes/init.php');

$user_role = get_role();
if($user_role != 'admin' && $user_role != 'user') {
    header('Location: login.php');
    exit;
}
?>  
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Akhir Perankingan MOORA</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333333;
            font-size: 11pt;
            line-height: 1.4;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Header / Kop Laporan */
        .header {
            text-align: center;
            border-bottom: 3px double #d63384;
            padding-bottom: 12px;
            margin-bottom: 25px;
        }
        .header h2 {
            margin: 0;
            color: #b83280;
            font-size: 18pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h4 {
            margin: 5px 0 0 0;
            color: #555555;
            font-size: 13pt;
            font-weight: normal;
        }
        .header p {
            margin: 3px 0 0 0;
            color: #777777;
            font-size: 9pt;
        }

        /* Meta Info */
        .meta-info {
            margin-bottom: 15px;
            width: 100%;
        }
        .meta-info td {
            font-size: 9.5pt;
            color: #555555;
        }

        /* Styling Tabel */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background-color: #d63384;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            padding: 10px 8px;
            font-size: 10pt;
            border: 1px solid #c22975;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
            font-size: 9.5pt;
        }
        table.data-table tr:nth-child(even) {
            background-color: #fdf0f7;
        }

        /* Element Styling */
        .img-product {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .badge-rank {
            background-color: #b83280;
            color: #ffffff;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 12px;
            display: inline-block;
            font-size: 10pt;
        }
        .badge-rank-top {
            background-color: #d63384;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-weight-bold { font-weight: bold; }

        /* Footer & Tanda Tangan */
        .footer-section {
            margin-top: 30px;
            width: 100%;
        }
        .ttd-box {
            float: right;
            width: 220px;
            text-align: center;
            font-size: 10pt;
        }
        .ttd-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <!-- Header Kop Document -->
    <div class="header">
        <h2>SPK METODE MOORA</h2>
        <h4>LAPORAN HASIL AKHIR PERANKINGAN</h4>
        <p>Sistem Pendukung Keputusan Penilaian Produk & Alternatif Terbaik</p>
    </div>

    <!-- Meta Informasi -->
    <table class="meta-info">
        <tr>
            <td><strong>Tanggal Cetak:</strong> <?= date('d F Y'); ?></td>
            <td style="text-align: right;"><strong>Dicetak Oleh:</strong> Administrator</td>
        </tr>
    </table>

    <!-- Tabel Data -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="12%">Gambar</th>
                <th width="30%">Nama Produk</th>
                <th width="20%">Alternatif</th>
                <th width="18%">Nilai (Yi)</th>
                <th width="15%">Peringkat</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sql = "SELECT * FROM hasil JOIN alternatif ON hasil.id_alternatif=alternatif.id_alternatif ORDER BY hasil.nilai DESC";
            $result = $koneksi->query($sql);
            $ranking = 1;
            $prevNilai = null;
            $data = [];
            $counter = 0;

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $counter++;
                    if ($prevNilai !== null && $row['nilai'] == $prevNilai) {
                        // Rank tetap sama jika nilai identik
                    } else {
                        $ranking = $counter; 
                    }    
                    $data[] = [
                        'nama' => $row['nama'],
                        'nilai' => $row['nilai'],
                        'alternatif' => $row['alternatif'],
                        'gambar' => $row['gambar'],
                        'ranking' => $ranking
                    ];
                    $prevNilai = $row['nilai'];
                }
            }

            if (!empty($data)):
                foreach ($data as $d): 
                    $gambarPath = 'uploads/' . $d['gambar'];
                    $hasGambar = !empty($d['gambar']) && file_exists($gambarPath);
            ?>
            <tr>
                <td class="text-center">
                    <?php if ($hasGambar): ?>
                        <img src="<?= $gambarPath; ?>" class="img-product" />
                    <?php else: ?>
                        <img src="uploads/default.jpg" class="img-product" />
                    <?php endif; ?>
                </td>
                <td class="text-left font-weight-bold" style="color: #2c3e50;"><?= htmlspecialchars($d['nama']); ?></td>
                <td class="text-center"><?= htmlspecialchars($d['alternatif']); ?></td>
                <td class="text-center font-weight-bold" style="color: #b83280;"><?= number_format($d['nilai'], 4); ?></td>
                <td class="text-center">
                    <span class="badge-rank <?= ($d['ranking'] == 1) ? 'badge-rank-top' : ''; ?>">
                        Rank <?= $d['ranking']; ?>
                    </span>
                </td>
            </tr>
            <?php 
                endforeach;
            else:
            ?>
            <tr>
                <td colspan="5" class="text-center" style="padding: 20px; color: #888;">Belum ada data perankingan.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Area Tanda Tangan / Legitimasi Dokumen -->
    <div class="footer-section">
        <div class="ttd-box">
            <p>Diketahui oleh,</p>
            <p><strong>Administrator SPK</strong></p>
            <div class="ttd-space"></div>
            <p><u>(_______________________)</u></p>
        </div>
    </div>

    <!-- Script Auto-Cetak Langsung (Kompatibel 100% di Server Hosting) -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>