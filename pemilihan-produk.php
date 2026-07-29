<?php 
require_once('includes/init.php');
$user_role = get_role();

// 1. Sanitasi GET parameter
$kate = isset($_GET['kate']) ? mysqli_real_escape_string($koneksi, $_GET['kate']) : '';
$idk1 = isset($_GET['idk1']) ? intval($_GET['idk1']) : '';
$idk2 = isset($_GET['idk2']) ? intval($_GET['idk2']) : '';
$idk3 = isset($_GET['idk3']) ? intval($_GET['idk3']) : '';

// 2. Fetch sub_kriteria pilihan
$c1 = $idk1 ? mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM sub_kriteria WHERE id_sub_kriteria='$idk1'")) : [];
$c2 = $idk2 ? mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM sub_kriteria WHERE id_sub_kriteria='$idk2'")) : [];
$c3 = $idk3 ? mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM sub_kriteria WHERE id_sub_kriteria='$idk3'")) : [];

// Map untuk kemudahan akses di looping
$selected_sub = [
    '1' => ['id' => $idk1, 'data' => $c1],
    '2' => ['id' => $idk2, 'data' => $c2],
    '3' => ['id' => $idk3, 'data' => $c3],
];

// 3. Helper Functions untuk Deskripsi Teks
function getDeskripsiKualitas($kategori, $nama_kualitas) {
    $kat = strtolower($kategori);
    $kual = strtolower($nama_kualitas);

    if ($kat === 'elektronik') {
        switch ($kual) {
            case 'sangat baik': return "Kipas angin dengan motor kuat, 3-5 mode kecepatan, fitur remote, bahan logam/aluminium berkualitas, dan suara sangat halus.";
            case 'baik': return "Kipas angin dengan 3 tingkat kecepatan, material plastik kokoh, putaran angin stabil dan cukup hening.";
            case 'cukup baik': case 'standar': case 'satndar': return "Kipas angin standar dengan 2-3 kecepatan, suara angin sedikit bising, dan bodi cukup ringan.";
            case 'buruk': return "Kipas angin dengan bahan plastik tipis, getaran besar, suara bising, dan kecepatan angin tidak stabil.";
            case 'sangat buruk': return "Kipas angin mudah rusak, tidak bisa berputar dengan lancar, sangat berisik, dan berbau saat dinyalakan.";
        }
    } elseif ($kat === 'rumah tangga') {
        switch ($kual) {
            case 'sangat baik': return "Detergen dengan daya bersih maksimal, formula anti noda membandel, wangi tahan lama, dan ramah untuk kulit.";
            case 'baik': return "Detergen mampu membersihkan noda ringan hingga sedang, cukup wangi dan tidak merusak kain.";
            case 'cukup baik': case 'standar': case 'satndar': return "Detergen biasa, dapat membersihkan pakaian sehari-hari, namun kurang ampuh untuk noda berat.";
            case 'buruk': return "Detergen dengan daya bersih rendah, kurang wangi, dan bisa meninggalkan residu di pakaian.";
            case 'sangat buruk': return "Detergen tidak mampu membersihkan noda, berbau kimia menyengat, dan bisa menyebabkan iritasi kulit.";
        }
    } elseif ($kat === 'mandi') {
        switch ($kual) {
            case 'sangat baik': return "Shampoo memberikan kelembutan maksimal, menghilangkan ketombe, harum tahan lama, dan menjaga kelembapan kulit kepala.";
            case 'baik': return "Shampoo cukup efektif membersihkan rambut, melembutkan, dan memiliki aroma yang menyegarkan.";
            case 'cukup baik': case 'standar': case 'satndar': return "Shampoo dengan pembersih dasar, cocok untuk pemakaian harian meski efeknya biasa saja.";
            case 'buruk': return "Shampoo membuat rambut cepat lepek, tidak efektif membersihkan ketombe, dan tidak tahan lama wanginya.";
            case 'sangat buruk': return "Shampoo menyebabkan iritasi, rambut menjadi kering, kasar, rontok, dan berbau menyengat.";
        }
    }
    return '';
}

function getDeskripsiDesain($kategori, $nama_desain) {
    if (empty($nama_desain)) return '';
    
    $kat = strtolower($kategori);
    $des = strtolower($nama_desain);

    if ($kat === 'elektronik') {
        if (strpos($des, 'sangat menarik') !== false) return "Desain kipas angin modern dengan warna elegan, tombol sentuh, dan bentuk ramping.";
        if (strpos($des, 'menarik') !== false) return "Kipas angin dengan bentuk estetis, warna cerah, dan fitur ergonomis.";
        if (strpos($des, 'standar') !== false || strpos($des, 'satndar') !== false) return "Desain biasa dengan tampilan sederhana, tanpa ornamen tambahan.";
        if (strpos($des, 'kurang menarik') !== false) return "Kipas angin terlihat kaku, warna membosankan, dan bentuk jadul.";
        if (strpos($des, 'sangat tidak menarik') !== false) return "Desain ketinggalan zaman, kombinasi warna buruk, dan tampak murahan.";
    } elseif ($kat === 'rumah tangga') {
        if (strpos($des, 'sangat menarik') !== false) return "Kemasan detergen modern, ergonomis, dan ramah pengguna.";
        if (strpos($des, 'menarik') !== false) return "Desain kemasan detergen dengan warna cerah dan label informatif.";
        if (strpos($des, 'standar') !== false || strpos($des, 'satndar') !== false) return "Desain umum pada kemasan detergen tanpa fitur mencolok.";
        if (strpos($des, 'kurang menarik') !== false) return "Kemasan kurang praktis, warna pudar, dan desain monoton.";
        if (strpos($des, 'sangat tidak menarik') !== false) return "Desain kemasan tidak menarik, mudah rusak, dan sulit dibuka.";
    } elseif ($kat === 'mandi') {
        if (strpos($des, 'sangat menarik') !== false) return "Botol shampoo bergaya elegan, warna menarik, dan mudah digenggam.";
        if (strpos($des, 'menarik') !== false) return "Desain kemasan shampoo segar dan fungsional.";
        if (strpos($des, 'standar') !== false || strpos($des, 'satndar') !== false) return "Desain umum tanpa keunggulan estetika.";
        if (strpos($des, 'kurang menarik') !== false) return "Botol terlihat biasa saja dan tidak ergonomis.";
        if (strpos($des, 'sangat tidak menarik') !== false) return "Desain terlihat murah dan tidak menarik bagi konsumen.";
    }
    return '';
}

$page = "Pemilihan Produk";
require_once('template/header.php');
?>

<style>
    /* Styling khusus warna Magenta & Efek 3D */
    .text-magenta {
        color: #b83280 !important;
    }
    .btn-magenta {
        background: linear-gradient(135deg, #d63384 0%, #b83280 100%);
        color: #ffffff !important;
        border: none;
        box-shadow: 0 4px 12px rgba(184, 50, 128, 0.3);
        transition: all 0.3s ease;
    }
    .btn-magenta:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(184, 50, 128, 0.4);
    }
    .btn-outline-magenta {
        color: #b83280 !important;
        border: 1.5px solid #b83280;
        transition: all 0.2s ease;
    }
    .btn-outline-magenta:hover {
        background-color: #b83280;
        color: #ffffff !important;
    }
    .card-3d-item {
        border-radius: 1rem;
        border: none;
        border-left: 5px solid #b83280 !important;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .card-3d-item:hover {
        transform: translateY(-5px) scale(1.01);
        box-shadow: 0 12px 25px rgba(184, 50, 128, 0.2);
    }
    .card-custom-header {
        background-color: #ffffff;
        border-bottom: 2px solid #f8f0fc;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-check-square text-magenta mr-2"></i>Pemilihan Produk
    </h1>
</div>

<div class="row">
    <!-- Panel Kriteria -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card shadow-sm mb-4 style-card-main" style="border-radius: 1rem; border: none;">
            <div class="card-header py-3 card-custom-header">
                <h6 class="m-0 font-weight-bold text-magenta">
                    <i class="fa fa-sliders-h mr-1"></i> Pilih Kriteria Produk
                </h6>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="font-weight-bold text-gray-700">Kategori</label>
                    <select name="kategori" id="kategori" class="form-control" style="border-radius: 0.5rem;" required>
                        <option value="<?= htmlspecialchars($kate); ?>">
                            <?= $kate ? htmlspecialchars($kate) : '--Pilih Kategori--'; ?>
                        </option>
                        <?php
                        $sql = mysqli_query($koneksi, "SELECT kategori FROM alternatif GROUP BY kategori");
                        while ($k = mysqli_fetch_array($sql)) {
                            $selected = ($k['kategori'] == $kate) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($k['kategori']) . "' {$selected}>" . htmlspecialchars($k['kategori']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="15%">KODE</th>
                                <th>KRITERIA</th>
                                <th width="50%">KEPUTUSAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q = mysqli_query($koneksi, "SELECT * FROM kriteria");
                            while ($dt = mysqli_fetch_array($q)):
                                $idx = substr($dt['kode_kriteria'], -1);
                                $curr_sub = $selected_sub[$idx] ?? ['id' => '', 'data' => []];
                            ?>
                            <tr>
                                <td class="font-weight-bold text-center text-magenta"><?= htmlspecialchars($dt['kode_kriteria']) ?></td>
                                <td class="font-weight-bold"><?= htmlspecialchars($dt['nama']) ?></td>
                                <td>
                                    <select id="idk<?= $idx; ?>" class="form-control" style="border-radius: 0.5rem;" required>
                                        <?php if (!empty($curr_sub['id'])): ?>
                                            <option value="<?= $curr_sub['id'] ?>">
                                                <?= htmlspecialchars($curr_sub['data']['nama'] ?? '') ?>
                                            </option>
                                        <?php endif; ?>
                                        <option value="">--Pilih--</option>
                                        <?php
                                        $qsub = mysqli_query($koneksi, "SELECT * FROM sub_kriteria WHERE id_kriteria = '{$dt['id_kriteria']}' ORDER BY nilai ASC");
                                        while ($sub = mysqli_fetch_array($qsub)) {
                                            echo "<option value='{$sub['id_sub_kriteria']}'>" . htmlspecialchars($sub['nama']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-right mt-3">
                    <button id="cari" class="btn btn-magenta px-4 font-weight-bold" style="border-radius: 20px;">
                        <i class="fa fa-cog fa-spin-hover mr-1"></i> Proses Rekomendasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Hasil Rekomendasi -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card shadow-sm mb-4" style="border-radius: 1rem; border: none;">
            <div class="card-header py-3 card-custom-header">
                <h6 class="m-0 font-weight-bold text-magenta">
                    <i class="fa fa-star mr-1"></i> Rekomendasi Terbaik
                </h6>
            </div>
            <div class="card-body">
                <?php if ($kate && $idk1 && $idk2 && $idk3): ?>
                    <?php
                    $getC1 = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_kriteria FROM kriteria WHERE kode_kriteria='C1'"));
                    $getC2 = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_kriteria FROM kriteria WHERE kode_kriteria='C2'"));
                    $getC3 = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_kriteria FROM kriteria WHERE kode_kriteria='C3'"));

                    $sql = "
                    SELECT b.* FROM alternatif b
                    JOIN penilaian p1 ON b.id_alternatif = p1.id_alternatif AND p1.id_kriteria = '{$getC1['id_kriteria']}' AND p1.nilai = '$idk1'
                    JOIN penilaian p2 ON b.id_alternatif = p2.id_alternatif AND p2.id_kriteria = '{$getC2['id_kriteria']}' AND p2.nilai = '$idk2'
                    JOIN penilaian p3 ON b.id_alternatif = p3.id_alternatif AND p3.id_kriteria = '{$getC3['id_kriteria']}' AND p3.nilai = '$idk3'
                    WHERE b.kategori = '$kate'
                    LIMIT 3
                    ";

                    $query = mysqli_query($koneksi, $sql);
                    $jp = mysqli_num_rows($query);
                    ?>

                    <?php if ($jp == 0): ?>
                        <div class="alert alert-warning text-center" style="border-radius: 0.75rem;">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Data rekomendasi tidak ditemukan untuk kriteria ini.
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php while ($data = mysqli_fetch_array($query)): ?>
                                <?php
                                $ida = $data['id_alternatif'];
                                $getKualitas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT s.nama FROM penilaian p JOIN sub_kriteria s ON p.nilai = s.id_sub_kriteria WHERE p.id_kriteria = '{$getC2['id_kriteria']}' AND p.id_alternatif = '$ida'"));
                                $getDesain = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT s.nama FROM penilaian p JOIN sub_kriteria s ON p.nilai = s.id_sub_kriteria WHERE p.id_kriteria = '{$getC3['id_kriteria']}' AND p.id_alternatif = '$ida'"));

                                $nama_kualitas = $getKualitas['nama'] ?? '-';
                                $nama_desain = $getDesain['nama'] ?? '-';

                                $deskripsi_kualitas = getDeskripsiKualitas($data['kategori'], $nama_kualitas);
                                $deskripsi_desain = getDeskripsiDesain($data['kategori'], $nama_desain);
                                $gambar_path = "uploads/" . $data['gambar'];
                                $gambar = (!empty($data['gambar']) && file_exists($gambar_path)) ? $gambar_path : 'uploads/default.jpg';

                                // Perbaikan pembersihan angka untuk harga
                                $harga_clean = preg_replace('/[^0-9]/', '', $data['harga']);
                                $harga_formatted = !empty($harga_clean) ? number_format((float)$harga_clean, 0, ',', '.') : '0';
                                ?>
                                <div class="col-md-12 mb-3">
                                    <div class="card card-3d-item h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                <img src="<?= $gambar ?>" class="img-thumbnail rounded mr-3 flex-shrink-0" style="width: 100px; height: 100px; object-fit: cover; border-radius: 0.75rem !important;" alt="Foto Produk">
                                                <div class="flex-grow-1">
                                                    <h5 class="card-title font-weight-bold text-magenta mb-2"><?= htmlspecialchars($data['nama']) ?></h5>
                                                    
                                                    <p class="mb-1 text-dark">
                                                        <strong>Harga:</strong> <span class="badge badge-light text-dark font-weight-bold" style="font-size: 0.9rem;">Rp <?= $harga_formatted ?></span>
                                                    </p>
                                                    
                                                    <p class="mb-1 text-dark">
                                                        <strong>Kualitas:</strong> <?= htmlspecialchars($nama_kualitas) ?>
                                                    </p>
                                                    <?php if (!empty($deskripsi_kualitas)): ?>
                                                        <p class="small text-muted mb-2"><em><?= htmlspecialchars($deskripsi_kualitas) ?></em></p>
                                                    <?php endif; ?>

                                                    <p class="mb-1 text-dark">
                                                        <strong>Desain:</strong> <?= htmlspecialchars($nama_desain) ?>
                                                    </p>
                                                    <?php if (!empty($deskripsi_desain)): ?>
                                                        <p class="small text-muted mb-2"><em><?= htmlspecialchars($deskripsi_desain) ?></em></p>
                                                    <?php endif; ?>

                                                    <a href="tambah-ulasan.php?ida=<?= $data['id_alternatif'] ?>&usr=<?= urlencode($_SESSION['username'] ?? '') ?>" class="btn btn-sm btn-outline-magenta mt-2 font-weight-bold" style="border-radius: 20px;">
                                                        <i class="fa fa-plus mr-1"></i> Tambah Ulasan
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="alert alert-info text-center" style="border-radius: 0.75rem; background-color: #fcf0f7; color: #b83280; border-color: #f7d6e6;">
                        <i class="fas fa-info-circle mr-1"></i> Silakan lengkapi pilihan kategori dan kriteria terlebih dahulu.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    $('#cari').click(function () {
        var kate = $('#kategori').val();
        var idk1 = $('#idk1').val();
        var idk2 = $('#idk2').val();
        var idk3 = $('#idk3').val();
        
        if(!kate || !idk1 || !idk2 || !idk3) {
            alert('Harap pilih semua opsi kriteria!');
            return false;
        }

        location.replace("pemilihan-produk.php?kate=" + encodeURIComponent(kate) + "&idk1=" + idk1 + "&idk2=" + idk2 + "&idk3=" + idk3);
    });
</script>

<?php require_once('template/footer.php'); ?>