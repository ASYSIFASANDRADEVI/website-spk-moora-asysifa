<?php
require_once('includes/init.php');

$user_role = get_role();
if ($user_role != 'admin' && $user_role != 'user') {
    header('Location: login.php');
    exit;
}

// Proses simpan ulasan jika form disubmit oleh user
if (isset($_POST['submit_ulasan']) && $user_role == 'user') {
    $tanggal = date('Y-m-d');
    $id_alternatif = intval($_POST['id_alternatif']);
    $ulasan = trim($_POST['ulasan']);
    $user = trim($_POST['nama_pengunjung'] ?? ($_SESSION['username'] ?? 'Pengunjung'));
    $rating = intval($_POST['rating']);

    if (!empty($id_alternatif) && !empty($ulasan) && $rating >= 1 && $rating <= 5 && !empty($user)) {
        
        $sql = "INSERT INTO data_ulasan (id_alternatif, ulasan, `user`, rating, tanggal) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "issis", $id_alternatif, $ulasan, $user, $rating, $tanggal);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['status_msg'] = '<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-lg" role="alert" style="background: linear-gradient(135deg, #e83e8c, #ff85a2); color: white;">
                    <i class="fas fa-check-circle mr-2"></i> <strong>Berhasil!</strong> Terima kasih, ulasan Anda telah terpublikasi.
                    <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>';
            } else {
                $_SESSION['status_msg'] = '<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-lg" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Gagal menyimpan ulasan: ' . htmlspecialchars(mysqli_error($koneksi)) . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>';
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['status_msg'] = '<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-lg" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i> Error Database: ' . htmlspecialchars(mysqli_error($koneksi)) . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>';
        }

    } else {
        $_SESSION['status_msg'] = '<div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-lg text-dark" role="alert" style="background: #ffe6ed;">
            <i class="fas fa-exclamation-circle mr-2 text-danger"></i> Harap isi nama pengunjung, pilih produk, tulis ulasan, dan beri bintang rating.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>';
    }

    // Redirect untuk mencegah resubmission form saat refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Ambil pesan status dari session jika ada
$status_msg = $_SESSION['status_msg'] ?? '';
unset($_SESSION['status_msg']); // Hapus setelah ditampilkan sekali

$page = "Ulasan Produk";
require_once('template/header.php');

// 1. Quick Query Statistik Umum
$stats_query = mysqli_query($koneksi, "SELECT COUNT(*) as total_ulasan, AVG(rating) as avg_rating FROM data_ulasan");
$stats = mysqli_fetch_assoc($stats_query);
$total_ulasan = $stats['total_ulasan'] ?? 0;
$avg_rating = number_format($stats['avg_rating'] ?? 0, 1);

// 2. Query Cari Produk Rating Tertinggi (Top Score) per Kategori + Foto Produk
$top_products = [
    'Elektronik' => ['nama' => 'Belum Ada', 'rating' => 0, 'gambar' => 'default.jpg', 'total_rev' => 0],
    'Rumah Tangga' => ['nama' => 'Belum Ada', 'rating' => 0, 'gambar' => 'default.jpg', 'total_rev' => 0],
    'Perawatan' => ['nama' => 'Belum Ada', 'rating' => 0, 'gambar' => 'default.jpg', 'total_rev' => 0]
];

$top_query_sql = "
    SELECT a.nama AS nama_produk, a.gambar, AVG(u.rating) as avg_prod_rating, COUNT(u.id) as total_rev
    FROM data_ulasan u
    JOIN alternatif a ON u.id_alternatif = a.id_alternatif
    GROUP BY a.id_alternatif, a.nama, a.gambar
";
$top_query = mysqli_query($koneksi, $top_query_sql);

if ($top_query) {
    while ($row = mysqli_fetch_assoc($top_query)) {
        $nama_p = strtolower($row['nama_produk']);
        $r_val = round($row['avg_prod_rating'], 1);
        $gambar_file = !empty($row['gambar']) ? $row['gambar'] : 'default.jpg';

        if (strpos($nama_p, 'kipas') !== false) {
            if ($r_val > $top_products['Elektronik']['rating']) {
                $top_products['Elektronik'] = ['nama' => $row['nama_produk'], 'rating' => $r_val, 'gambar' => $gambar_file, 'total_rev' => $row['total_rev']];
            }
        } elseif (strpos($nama_p, 'detergen') !== false || strpos($nama_p, 'sabun') !== false) {
            if ($r_val > $top_products['Rumah Tangga']['rating']) {
                $top_products['Rumah Tangga'] = ['nama' => $row['nama_produk'], 'rating' => $r_val, 'gambar' => $gambar_file, 'total_rev' => $row['total_rev']];
            }
        } elseif (strpos($nama_p, 'shampoo') !== false || strpos($nama_p, 'sampo') !== false) {
            if ($r_val > $top_products['Perawatan']['rating']) {
                $top_products['Perawatan'] = ['nama' => $row['nama_produk'], 'rating' => $r_val, 'gambar' => $gambar_file, 'total_rev' => $row['total_rev']];
            }
        }
    }
}
?>

<style>
/* Theme Pink Custom Color Variables */
.text-pink-theme {
    color: #b83280 !important;
}

/* Modern Glass & Gradient Header (Pink Theme) */
.header-hero {
    background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);
    border-radius: 16px;
    padding: 24px;
    color: #4a1525;
    box-shadow: 0 10px 25px rgba(248, 187, 208, 0.4);
}

.stats-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 12px 18px;
    border: 1px solid rgba(255, 255, 255, 0.8);
}

/* Card Top Score / Ranking Kategori */
.top-score-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 14px;
    border: none;
    box-shadow: 0 5px 18px rgba(184, 50, 128, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.top-score-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(184, 50, 128, 0.15);
}

.top-score-img-box {
    width: 75px;
    height: 75px;
    min-width: 75px;
    border-radius: 12px;
    overflow: hidden;
    background: #fff5f8;
    border: 2px solid #fff;
    box-shadow: 0 4px 10px rgba(184, 50, 128, 0.1);
}
.top-score-img-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.badge-top-rank {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 0.65rem;
    padding: 4px 8px;
    border-radius: 20px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Card Ulasan Grid */
.grid-ulasan {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 22px;
}

.card-ulasan {
    border: none;
    border-radius: 16px;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    background: #ffffff;
    box-shadow: 0 5px 15px rgba(184, 50, 128, 0.05);
}
.card-ulasan:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(184, 50, 128, 0.15) !important;
}

/* Form Styling (Pink Theme) */
.card-form-ulasan {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(184, 50, 128, 0.1);
}

.card-form-header {
    background: linear-gradient(135deg, #a8286a 0%, #c2185b 100%);
    color: #fff;
    padding: 18px 24px;
}

/* Star Input Styling */
.star-rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    font-size: 2rem;
}
.star-rating-input input {
    display: none;
}
.star-rating-input label {
    color: #f3d0db;
    cursor: pointer;
    margin-right: 6px;
    transition: transform 0.2s, color 0.2s;
}
.star-rating-input label:hover,
.star-rating-input label:hover ~ label,
.star-rating-input input:checked ~ label {
    color: #ffb703;
}
.star-rating-input label:hover {
    transform: scale(1.2);
}

/* Product & Avatar Image Wrappers */
.product-img-wrapper {
    position: relative;
    width: 100%;
    height: 170px;
    overflow: hidden;
    background: #fff5f8;
    border-radius: 12px;
}
.product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.card-ulasan:hover .product-img {
    transform: scale(1.08);
}

.avatar-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #d81b60 0%, #ff4081 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: 0 4px 10px rgba(216, 27, 96, 0.3);
}

.review-text {
    background: #fff5f8;
    border-left: 4px solid #d81b60;
    border-radius: 4px 10px 10px 4px;
    padding: 12px;
    font-size: 0.9rem;
    color: #4a1525;
    line-height: 1.5;
    font-style: italic;
}

/* Custom Pink Gradient Button */
.btn-gradient-submit {
    background: linear-gradient(135deg, #b83280 0%, #d81b60 100%);
    border: none;
    color: white;
    font-weight: 600;
    padding: 10px 24px;
    border-radius: 30px;
    box-shadow: 0 4px 15px rgba(184, 50, 128, 0.35);
    transition: all 0.3s ease;
}
.btn-gradient-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(184, 50, 128, 0.5);
    color: white;
}
</style>

<div class="container-fluid">

    <!-- HERO HEADER WITH STATS -->
    <div class="header-hero mb-4 d-flex flex-column flex-md-row align-items-center justify-content-between">
        <div class="mb-3 mb-md-0">
            <h2 class="font-weight-bold mb-1 text-dark">
                <i class="fas fa-comments text-pink-theme mr-2"></i>Ulasan & Feedback Pengunjung
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.95rem;">
                Daftar ulasan produk dan penilaian jujur dari para pengunjung kami.
            </p>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="stats-card d-flex align-items-center mr-3">
                <i class="fas fa-star text-warning fa-2x mr-3"></i>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark"><?= $avg_rating ?> / 5.0</h5>
                    <small class="text-muted font-weight-bold">Rata-rata Rating</small>
                </div>
            </div>
            <div class="stats-card d-flex align-items-center">
                <i class="fas fa-comment-dots text-pink-theme fa-2x mr-3"></i>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark"><?= $total_ulasan ?></h5>
                    <small class="text-muted font-weight-bold">Total Ulasan</small>
                </div>
            </div>
        </div>
    </div>

    <!-- WIDGET TOP SCORE ULASAN (3 KATEGORI DENGAN FOTO PRODUK) -->
    <div class="row mb-4">
        <div class="col-12">
            <h6 class="font-weight-bold text-dark mb-3">
                <i class="fas fa-trophy text-warning mr-2"></i>Top Score Produk Rating Tertinggi
            </h6>
        </div>

        <!-- Top Score: Elektronik -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="top-score-card d-flex align-items-center">
                <span class="badge badge-top-rank text-white" style="background: linear-gradient(135deg, #ec407a, #d81b60);">
                    <i class="fas fa-bolt mr-1"></i> Elektronik
                </span>
                <?php 
                    $img_elek = 'uploads/' . $top_products['Elektronik']['gambar'];
                    $src_elek = file_exists($img_elek) ? $img_elek : 'uploads/default.jpg';
                ?>
                <div class="top-score-img-box mr-3">
                    <img src="<?= $src_elek ?>" alt="Top Elektronik">
                </div>
                <div class="overflow-hidden pr-3">
                    <h6 class="font-weight-bold text-dark mb-1 text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($top_products['Elektronik']['nama']) ?>">
                        <?= htmlspecialchars($top_products['Elektronik']['nama']) ?>
                    </h6>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-warning text-dark font-weight-bold mr-2 px-2 py-1" style="font-size: 0.75rem; border-radius: 6px;">
                            <i class="fas fa-star text-warning"></i> <?= $top_products['Elektronik']['rating'] > 0 ? $top_products['Elektronik']['rating'] : '0.0' ?>
                        </span>
                        <small class="text-muted" style="font-size: 0.75rem;">(<?= $top_products['Elektronik']['total_rev'] ?> ulasan)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Score: Rumah Tangga -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="top-score-card d-flex align-items-center">
                <span class="badge badge-top-rank text-white" style="background: linear-gradient(135deg, #e91e63, #c2185b);">
                    <i class="fas fa-broom mr-1"></i> Rumah Tangga
                </span>
                <?php 
                    $img_rt = 'uploads/' . $top_products['Rumah Tangga']['gambar'];
                    $src_rt = file_exists($img_rt) ? $img_rt : 'uploads/default.jpg';
                ?>
                <div class="top-score-img-box mr-3">
                    <img src="<?= $src_rt ?>" alt="Top Rumah Tangga">
                </div>
                <div class="overflow-hidden pr-3">
                    <h6 class="font-weight-bold text-dark mb-1 text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($top_products['Rumah Tangga']['nama']) ?>">
                        <?= htmlspecialchars($top_products['Rumah Tangga']['nama']) ?>
                    </h6>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-warning text-dark font-weight-bold mr-2 px-2 py-1" style="font-size: 0.75rem; border-radius: 6px;">
                            <i class="fas fa-star text-warning"></i> <?= $top_products['Rumah Tangga']['rating'] > 0 ? $top_products['Rumah Tangga']['rating'] : '0.0' ?>
                        </span>
                        <small class="text-muted" style="font-size: 0.75rem;">(<?= $top_products['Rumah Tangga']['total_rev'] ?> ulasan)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Score: Perawatan -->
        <div class="col-md-4">
            <div class="top-score-card d-flex align-items-center">
                <span class="badge badge-top-rank text-white" style="background: linear-gradient(135deg, #ff4081, #f50057);">
                    <i class="fas fa-sparkles mr-1"></i> Perawatan
                </span>
                <?php 
                    $img_rawat = 'uploads/' . $top_products['Perawatan']['gambar'];
                    $src_rawat = file_exists($img_rawat) ? $img_rawat : 'uploads/default.jpg';
                ?>
                <div class="top-score-img-box mr-3">
                    <img src="<?= $src_rawat ?>" alt="Top Perawatan">
                </div>
                <div class="overflow-hidden pr-3">
                    <h6 class="font-weight-bold text-dark mb-1 text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($top_products['Perawatan']['nama']) ?>">
                        <?= htmlspecialchars($top_products['Perawatan']['nama']) ?>
                    </h6>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-warning text-dark font-weight-bold mr-2 px-2 py-1" style="font-size: 0.75rem; border-radius: 6px;">
                            <i class="fas fa-star text-warning"></i> <?= $top_products['Perawatan']['rating'] > 0 ? $top_products['Perawatan']['rating'] : '0.0' ?>
                        </span>
                        <small class="text-muted" style="font-size: 0.75rem;">(<?= $top_products['Perawatan']['total_rev'] ?> ulasan)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $status_msg; ?>

    <!-- FORM ULASAN (Hanya untuk USER / PENGUNJUNG) -->
    <?php if ($user_role == 'user'): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card card-form-ulasan shadow">
                <div class="card-form-header d-flex align-items-center justify-content-between">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-edit mr-2"></i> Tulis Ulasan Pengunjung
                    </h5>
                    <span class="badge badge-light px-3 py-1" style="border-radius: 12px; opacity: 0.9; color: #a8286a;">Form Feedback</span>
                </div>
                <div class="card-body p-4" style="background: #ffffff;">
                    <form method="post">
                        <div class="row">
                            <!-- Input Nama Pengunjung -->
                            <div class="col-md-4 form-group">
                                <label for="nama_pengunjung" class="font-weight-bold text-dark">Nama Pengunjung</label>
                                <input type="text" class="form-control" name="nama_pengunjung" style="border-radius: 10px; height: 48px;" placeholder="Masukkan Nama Anda..." value="<?= htmlspecialchars($_SESSION['username'] ?? ''); ?>" required>
                            </div>

                            <!-- Pilih Produk -->
                            <div class="col-md-4 form-group">
                                <label for="id_alternatif" class="font-weight-bold text-dark">Pilih Produk</label>
                                <select class="form-control custom-select" name="id_alternatif" style="border-radius: 10px; height: 48px;" required>
                                    <option value="">-- Pilih Produk --</option>
                                    <?php
                                    $produk_query = mysqli_query($koneksi, "SELECT id_alternatif, nama FROM alternatif ORDER BY nama ASC");
                                    while ($produk = mysqli_fetch_array($produk_query)) {
                                        echo "<option value='{$produk['id_alternatif']}'>" . htmlspecialchars($produk['nama']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <!-- Rating Bintang -->
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-dark d-block">Beri Rating</label>
                                <div class="star-rating-input">
                                    <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="Sangat Puas (5 Bintang)"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star4" name="rating" value="4"/><label for="star4" title="Puas (4 Bintang)"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star3" name="rating" value="3"/><label for="star3" title="Cukup (3 Bintang)"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star2" name="rating" value="2"/><label for="star2" title="Kurang (2 Bintang)"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star1" name="rating" value="1"/><label for="star1" title="Sangat Kurang (1 Bintang)"><i class="fas fa-star"></i></label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ulasan" class="font-weight-bold text-dark">Isi Ulasan</label>
                            <textarea class="form-control" name="ulasan" rows="3" style="border-radius: 12px; resize: none;" placeholder="Bagikan ulasan jujur Anda mengenai produk ini..." required></textarea>
                        </div>
                        
                        <div class="text-right mt-3">
                            <button type="submit" name="submit_ulasan" class="btn btn-gradient-submit">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Ulasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAMPILAN GRID ULASAN -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9; border-radius: 16px 16px 0 0;">
                    <h6 class="m-0 font-weight-bold text-dark" style="font-size: 1.1rem;">
                        <i class="fas fa-stream text-pink-theme mr-2"></i>Semua Ulasan Masuk
                    </h6>
                </div>
                <div class="card-body p-4" style="background-color: #fff8fa; border-radius: 0 0 16px 16px;">
                    <div class="grid-ulasan">
                        <?php
                        $query_sql = "
                            SELECT u.*, a.nama AS nama_produk, a.gambar 
                            FROM data_ulasan u 
                            LEFT JOIN alternatif a ON u.id_alternatif = a.id_alternatif 
                            ORDER BY u.id DESC
                        ";
                        $query = mysqli_query($koneksi, $query_sql);

                        if (mysqli_num_rows($query) == 0):
                        ?>
                            <div class="col-12 text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="100" class="mb-3 opacity-50" alt="Kosong">
                                <h5 class="text-secondary font-weight-bold">Belum Ada Ulasan</h5>
                                <p class="text-muted">Jadilah pengunjung pertama yang memberikan ulasan produk!</p>
                            </div>
                        <?php
                        else:
                            while ($data = mysqli_fetch_array($query)):
                                $nama_produk = strtolower($data['nama_produk'] ?? '');
                                
                                if (strpos($nama_produk, 'kipas') !== false) {
                                    $kategori = 'Elektronik';
                                    $bg_badge = 'background: linear-gradient(135deg, #ec407a, #d81b60);';
                                    $ikon_kategori = 'fas fa-bolt';
                                } elseif (strpos($nama_produk, 'detergen') !== false || strpos($nama_produk, 'sabun') !== false) {
                                    $kategori = 'Rumah Tangga';
                                    $bg_badge = 'background: linear-gradient(135deg, #e91e63, #c2185b);';
                                    $ikon_kategori = 'fas fa-broom';
                                } elseif (strpos($nama_produk, 'shampoo') !== false || strpos($nama_produk, 'sampo') !== false) {
                                    $kategori = 'Perawatan';
                                    $bg_badge = 'background: linear-gradient(135deg, #ff4081, #f50057);';
                                    $ikon_kategori = 'fas fa-sparkles';
                                } else {
                                    $kategori = 'Produk';
                                    $bg_badge = 'background: linear-gradient(135deg, #d81b60, #880e4f);';
                                    $ikon_kategori = 'fas fa-box';
                                }

                                $rating = intval($data['rating'] ?? 5);
                                $gambar_path = 'uploads/' . $data['gambar'];
                                $gambar = (!empty($data['gambar']) && file_exists($gambar_path)) ? $gambar_path : 'uploads/default.jpg';
                                $nama_pengunjung = !empty($data['user']) ? ucwords($data['user']) : 'Pengunjung';
                                $initial = strtoupper(substr($nama_pengunjung, 0, 1));
                        ?>
                            <div class="card card-ulasan">
                                <div class="card-body d-flex flex-column p-3">
                                    
                                    <!-- Identitas Pengunjung (User Avatar & Name) -->
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-circle mr-3">
                                            <?= $initial ?>
                                        </div>
                                        <div class="overflow-hidden">
                                            <h6 class="mb-0 font-weight-bold text-dark text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($nama_pengunjung) ?>">
                                                <?= htmlspecialchars($nama_pengunjung) ?>
                                            </h6>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                <i class="far fa-user mr-1 text-pink-theme"></i>Pengunjung
                                                <span class="mx-1">•</span>
                                                <i class="far fa-clock mr-1"></i><?= date('d M Y', strtotime($data['tanggal'])) ?>
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Kategori & Nama Produk -->
                                    <div class="mb-2">
                                        <span class="badge text-white px-2 py-1 mb-1" style="<?= $bg_badge ?> border-radius: 8px; font-size: 0.7rem; font-weight: 600;">
                                            <i class="<?= $ikon_kategori ?> mr-1"></i> <?= $kategori ?>
                                        </span>
                                        <h6 class="font-weight-bold text-dark mb-0 text-truncate" title="<?= htmlspecialchars($data['nama_produk'] ?? 'Produk') ?>">
                                            <?= htmlspecialchars($data['nama_produk'] ?? 'Produk Tidak Ditemukan') ?>
                                        </h6>
                                    </div>

                                    <!-- Gambar Produk Spesifik -->
                                    <div class="product-img-wrapper mb-3 shadow-sm">
                                        <img src="<?= $gambar ?>" alt="Foto Produk" class="product-img">
                                    </div>

                                    <!-- Rating Bintang -->
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div style="color: #ffb703; font-size: 0.9rem; letter-spacing: 1px;">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="<?= ($i <= $rating) ? 'fas' : 'far' ?> fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="border-radius: 8px; font-size: 0.75rem; background-color: #fff3cd;">
                                            <?= $rating ?>.0 / 5.0
                                        </span>
                                    </div>

                                    <!-- Teks Ulasan -->
                                    <div class="review-text flex-grow-1">
                                        "<?= htmlspecialchars($data['ulasan']) ?>"
                                    </div>

                                </div>
                            </div>
                        <?php 
                            endwhile;
                        endif; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('template/footer.php'); ?>