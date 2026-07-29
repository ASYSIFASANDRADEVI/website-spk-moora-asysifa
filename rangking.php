<?php
require_once('includes/init.php');

$user_role = get_role();
if($user_role == 'admin' || $user_role == 'user') {

$page = "Rank";
require_once('template/header.php');
?>

<!-- Custom CSS Tema Magenta & Style Modern 3D -->
<style>
    :root {
        --primary-magenta: #d63384;
        --dark-magenta: #b83280;
        --soft-magenta-bg: #fdf0f7;
        --shadow-3d: 0 10px 25px -5px rgba(184, 50, 128, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    /* Tab Custom Styling */
    .nav-tabs-magenta {
        border-bottom: 2px solid #f1d2e7;
    }
    .nav-tabs-magenta .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 600;
        padding: 10px 18px;
        border-radius: 10px 10px 0 0;
        transition: all 0.2s ease;
        margin-right: 4px;
    }
    .nav-tabs-magenta .nav-link:hover {
        color: var(--dark-magenta);
        background-color: var(--soft-magenta-bg);
    }
    .nav-tabs-magenta .nav-link.active {
        color: #ffffff !important;
        background: linear-gradient(135deg, #e84393, #b83280) !important;
        box-shadow: 0 4px 12px rgba(184, 50, 128, 0.25);
    }

    /* Container Card 3D */
    .card-perhitungan-3d {
        border-radius: 0 0 1.25rem 1.25rem;
        border: none;
        background: #ffffff;
        box-shadow: var(--shadow-3d);
        overflow: hidden;
    }

    /* Table Magenta Header & Spacing */
    .table-magenta thead tr {
        background: linear-gradient(135deg, #e84393, #b83280) !important;
        color: #ffffff !important;
    }
    .table-magenta thead th {
        border: none !important;
        font-weight: 600;
        padding: 12px 14px !important;
        vertical-align: middle !important;
    }
    .table-magenta tbody td {
        padding: 12px 14px !important;
        vertical-align: middle !important;
    }
    .table-magenta tbody tr:hover {
        background-color: var(--soft-magenta-bg);
    }

    /* Badges & Button Style */
    .btn-magenta-3d {
        background: linear-gradient(135deg, #e84393, #b83280);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 8px 18px;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(184, 50, 128, 0.25);
        transition: all 0.3s ease;
    }
    .btn-magenta-3d:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(184, 50, 128, 0.35);
    }

    .badge-alternatif-3d {
        background: linear-gradient(135deg, #f8f0fc, #fce4ec);
        color: var(--dark-magenta);
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 8px;
        display: inline-block;
    }

    .badge-rank-1 {
        background: linear-gradient(135deg, #ffb302, #f39c12);
        color: #fff;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        box-shadow: 0 3px 8px rgba(243, 156, 18, 0.3);
    }
    .badge-rank-other {
        background: #f1f3f5;
        color: #495057;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
    }

    .img-product-3d {
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        object-fit: cover;
        transition: transform 0.2s ease;
    }
    .img-product-3d:hover {
        transform: scale(1.05);
    }
</style>

<!-- Header Halaman -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-fw fa-chart-area mr-2" style="color: var(--primary-magenta);"></i>Data Perangkingan
    </h1>
    <a href="cetak.php" target="_blank" class="btn btn-magenta-3d">
        <i class="fa fa-print mr-1"></i> Cetak Data
    </a>
</div>

<?php
$sql = "SELECT * FROM hasil 
        JOIN alternatif ON hasil.id_alternatif=alternatif.id_alternatif 
        ORDER BY hasil.nilai DESC";
$result = $koneksi->query($sql);
$ranking = 1;
$prevNilai = null;
$data = [];
$counter = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $counter++;
        if ($prevNilai !== null && $row['nilai'] == $prevNilai) {
            // ranking tetap
        } else {
            $ranking = $counter;
        }

        $data[] = [
            'nama' => $row['nama'],
            'nilai' => $row['nilai'],
            'alternatif' => $row['alternatif'],
            'gambar' => $row['gambar'],
            'kategori' => $row['kategori'],
            'ranking' => $ranking
        ];

        $prevNilai = $row['nilai'];
    }
}
?>

<!-- Tab Navigasi -->
<ul class="nav nav-tabs nav-tabs-magenta" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#hasil"><i class="fa fa-trophy mr-1"></i> Data Perangkingan</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#rekomendasi"><i class="fa fa-thumbs-up mr-1"></i> Rekomendasi Produk</a>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content">
    
    <!-- Tab 1: Hasil Akhir Perankingan -->
    <div id="hasil" class="tab-pane active"><br>
        <div class="card card-perhitungan-3d mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);">
                    <i class="fa fa-table mr-2"></i>Hasil Akhir Perankingan
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-magenta m-0" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr align="center">
                                <th>Gambar Produk</th>
                                <th>Nama Produk</th>
                                <th>Nama Alternatif</th>
                                <th>Kategori</th>
                                <th>Nilai Yi</th>
                                <th width="15%">Rank</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $d) { ?>
                            <tr align="center">
                                <td>
                                    <?php if (!empty($d['gambar']) && file_exists('uploads/' . $d['gambar'])): ?>
                                        <img src="uploads/<?php echo $d['gambar']; ?>" class="img-product-3d" width="90px" height="90px" />
                                    <?php else: ?>
                                        <img src="uploads/default.jpg" class="img-product-3d" width="90px" height="90px" />
                                    <?php endif; ?>
                                </td>
                                <td align="left" class="font-weight-bold text-gray-800"><?= $d['nama'] ?></td>
                                <td><span class="badge-alternatif-3d"><?= $d['alternatif'] ?></span></td>
                                <td><span class="badge badge-light border text-dark px-3 py-2" style="border-radius:6px;"><?= $d['kategori'] ?></span></td>
                                <td class="font-weight-bold text-gray-800"><?= round($d['nilai'], 4) ?></td>
                                <td>
                                    <?php if($d['ranking'] == 1): ?>
                                        <span class="badge-rank-1"><i class="fas fa-crown mr-1"></i> Ranking 1</span>
                                    <?php else: ?>
                                        <span class="badge-rank-other">Ranking <?= $d['ranking'] ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Rekomendasi Produk -->
    <div id="rekomendasi" class="tab-pane fade"><br>
        <div class="card card-perhitungan-3d mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);">
                    <i class="fa fa-star mr-2"></i>Rekomendasi Produk Terbaik per Kategori
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-magenta m-0" width="100%" cellspacing="0">
                        <thead>
                            <tr align="center">
                                <th>Kategori Produk</th>
                                <th>Nama Produk</th>
                                <th>Alternatif</th>
                                <th>Nilai Yi</th>
                                <th>Gambar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $kategori_dicari = ['Elektronik' => null, 'Rumah Tangga' => null, 'Mandi' => null];
                            foreach ($data as $d) {
                                if (array_key_exists($d['kategori'], $kategori_dicari) && $kategori_dicari[$d['kategori']] === null) {
                                    $kategori_dicari[$d['kategori']] = $d;
                                }
                            }

                            foreach ($kategori_dicari as $kategori => $produk) {
                                if ($produk) {
                            ?>
                            <tr align="center">
                                <td>
                                    <span class="badge badge-light border text-dark px-3 py-2" style="border-radius:6px; font-weight:600;">
                                        <?= $kategori ?>
                                    </span>
                                </td>
                                <td align="left" class="font-weight-bold text-gray-800"><?= $produk['nama'] ?></td>
                                <td><span class="badge-alternatif-3d"><?= $produk['alternatif'] ?></span></td>
                                <td class="font-weight-bold text-gray-800" style="background-color: var(--soft-magenta-bg);">
                                    <?= round($produk['nilai'], 4) ?>
                                </td>
                                <td>
                                    <?php if (!empty($produk['gambar']) && file_exists('uploads/' . $produk['gambar'])): ?>
                                        <img src="uploads/<?php echo $produk['gambar']; ?>" class="img-product-3d" width="80px" height="80px" />
                                    <?php else: ?>
                                        <img src="uploads/default.jpg" class="img-product-3d" width="80px" height="80px" />
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php } else { ?>
                            <tr align="center">
                                <td colspan="5" class="text-muted font-italic py-4">Tidak ada data rekomendasi produk untuk kategori <b><?= $kategori ?></b></td>
                            </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
require_once('template/footer.php');
} else {
    header('Location: login.php');
}
?>