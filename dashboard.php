<?php 
require_once('includes/init.php');

$jkriteria = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM kriteria"));
$jsub      = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM sub_kriteria"));
$jproduk   = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM alternatif"));
$jnilai    = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM penilaian GROUP BY id_alternatif"));
$julasan   = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM data_ulasan"));
$user_role = get_role();

if ($user_role == 'admin' || $user_role == 'user') {
    $page = "Dashboard";
    require_once('template/header.php');
?>

<style>
    /* ================================================================= */
    /* STYLING EFEK 3D & MODERN MATERIAL (SEIRAMA WARNA SIDEBAR MAGENTA) */
    /* ================================================================= */
    
    /* Root Variable Warna */
    :root {
        --primary-magenta: #b83280;
        --dark-magenta: #8a1f5d;
        --light-magenta: #e84393;
        --soft-pink-bg: #fcf2f8;
        --shadow-3d: 0 12px 28px -6px rgba(184, 50, 128, 0.22), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        --shadow-3d-hover: 0 20px 35px -8px rgba(184, 50, 128, 0.35), 0 12px 15px -8px rgba(0, 0, 0, 0.08);
    }

    .card {
        border-radius: 1.25rem;
        border: none;
        background: #ffffff;
    }

    /* Hero Banner 3D Mesh Gradient */
    .welcome-hero-3d {
        position: relative;
        background: radial-gradient(circle at 80% 20%, #d63384 0%, #b83280 45%, #72124d 100%);
        color: #ffffff;
        border-radius: 1.5rem;
        padding: 3rem 2.5rem;
        box-shadow: var(--shadow-3d);
        overflow: hidden;
        perspective: 1000px;
    }
    
    /* Geometri Hiasan 3D di Latar Hero Banner */
    .welcome-hero-3d::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0) 70%);
        pointer-events: none;
    }

    .welcome-hero-3d::after {
        content: '';
        position: absolute;
        bottom: -80px;
        left: 20%;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(232, 67, 147, 0.3) 0%, rgba(255,255,255,0) 70%);
        pointer-events: none;
    }

    /* Icon Floating 3D */
    .icon-3d-float {
        display: inline-block;
        filter: drop-shadow(0 15px 15px rgba(0, 0, 0, 0.3));
        transform: translateY(0px) rotate(-5deg);
        animation: float3d 4s ease-in-out infinite;
    }

    @keyframes float3d {
        0%, 100% { transform: translateY(0px) rotate(-5deg); }
        50% { transform: translateY(-12px) rotate(2deg); }
    }

    /* Glassmorphism Badge */
    .badge-glass {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        border-radius: 30px;
        font-size: 0.85rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Tombol 3D Pop-Out */
    .btn-3d-white {
        background: #ffffff;
        color: var(--primary-magenta) !important;
        border: none;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15), inset 0 -3px 0 rgba(0, 0, 0, 0.1);
        transition: all 0.25s ease;
    }
    .btn-3d-white:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.22), inset 0 -3px 0 rgba(0, 0, 0, 0.1);
        background: #fff0f7;
    }

    .btn-3d-outline {
        background: rgba(255, 255, 255, 0.08);
        border: 1.5px solid rgba(255, 255, 255, 0.5);
        color: #ffffff !important;
        backdrop-filter: blur(5px);
        transition: all 0.25s ease;
    }
    .btn-3d-outline:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-3px);
        border-color: #ffffff;
    }

    /* Card Info 3D Shift Effect */
    .card-3d {
        border-radius: 1.25rem;
        transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 6px 18px rgba(0,0,0,0.04);
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .card-3d:hover {
        transform: translateY(-8px) scale(1.01);
        box-shadow: var(--shadow-3d-hover);
    }

    /* Feature Icon 3D Circle */
    .feature-icon-3d {
        width: 58px;
        height: 58px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        color: #ffffff;
    }

    .bg-icon-magenta {
        background: linear-gradient(135deg, #d63384, #b83280);
    }
    .bg-icon-purple {
        background: linear-gradient(135deg, #a55eea, #8e44ad);
    }

    /* Kustom Efek Admin 3D Card Modern */
    .admin-card-3d {
        border-radius: 1.25rem;
        border: 1px solid rgba(255,255,255,0.8);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.01);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
    }
    .admin-card-3d:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 30px -5px rgba(184, 50, 128, 0.18);
    }
    .admin-icon-wrapper {
        width: 54px;
        height: 54px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 2px 4px rgba(255,255,255,0.6), 0 6px 12px rgba(0,0,0,0.08);
    }
</style>

<div class="mb-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-fw fa-home mr-2" style="color: var(--primary-magenta);"></i>Dashboard
        </h1>
    </div>

    <?php if ($user_role == 'admin') { ?>
    <!-- ================================================================= -->
    <!-- DASHBOARD ADMIN (PENAMPILAN 3D & ELEGANT MODEREN)                  -->
    <!-- ================================================================= -->
    
    <!-- Hero Banner Admin 3D -->
    <div class="welcome-hero-3d mb-4">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-9">
                <span class="badge badge-glass px-3 py-2 mb-3 font-weight-bold">
                    <i class="fas fa-user-shield mr-1"></i> Mode Administrator
                </span>
                <h2 class="mb-2 font-weight-bold">Selamat Datang Kembali, <?php echo $_SESSION['username']; ?>! 👋</h2>
                <p class="lead mb-0" style="opacity: 0.95; font-size: 1.05rem;">
                    Kelola data kriteria, alternatif produk, penilaian, hingga laporan ulasan pengguna SPK MOORA dengan cepat dan presisi.
                </p>
            </div>
            <div class="col-lg-3 text-center d-none d-lg-block">
                <div class="icon-3d-float">
                    <i class="fas fa-chart-line fa-7x text-white" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards Stats Admin 3D Grid -->
    <div class="row">
        <?php
        $dashboard_cards = [
            ['count' => $jkriteria, 'link' => 'list-kriteria.php', 'text' => 'Data Kriteria', 'icon' => 'fa-cube', 'gradient' => 'linear-gradient(135deg, #e84393, #b83280)', 'bg_soft' => '#fcf0f7'],
            ['count' => $jsub, 'link' => 'list-sub-kriteria.php', 'text' => 'Data Sub Kriteria', 'icon' => 'fa-cubes', 'gradient' => 'linear-gradient(135deg, #a55eea, #8e44ad)', 'bg_soft' => '#f8f0fc'],
            ['count' => $jproduk, 'link' => 'list-alternatif.php', 'text' => 'Data Produk', 'icon' => 'fa-dolly', 'gradient' => 'linear-gradient(135deg, #ff7675, #e84393)', 'bg_soft' => '#fdf0f4'],
            ['count' => $jnilai, 'link' => 'list-penilaian.php', 'text' => 'Data Penilaian', 'icon' => 'fa-edit', 'gradient' => 'linear-gradient(135deg, #55efc4, #00b894)', 'bg_soft' => '#f0fbf7'],
            ['count' => 'Hitung', 'link' => 'perhitungan.php', 'text' => 'Data Perhitungan', 'icon' => 'fa-calculator', 'gradient' => 'linear-gradient(135deg, #a29bfe, #6c5ce7)', 'bg_soft' => '#f4f0fd'],
            ['count' => 'Hasil', 'link' => 'hasil.php', 'text' => 'Data Hasil Akhir', 'icon' => 'fa-chart-area', 'gradient' => 'linear-gradient(135deg, #ffeaa7, #fdcb6e)', 'bg_soft' => '#fef9f0'],
            ['count' => $julasan, 'link' => 'ulasan_pengguna.php', 'text' => 'Ulasan Produk', 'icon' => 'fa-comments', 'gradient' => 'linear-gradient(135deg, #ff7675, #d63031)', 'bg_soft' => '#fff0f5']
        ];

        foreach ($dashboard_cards as $card) {
        ?>
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4 d-flex">
            <div class="card admin-card-3d w-100" style="background-color: <?= $card['bg_soft'] ?>;">
                <a href="<?= $card['link'] ?>" class="text-decoration-none card-body d-flex align-items-center justify-content-between p-3.5">
                    <div>
                        <span class="text-uppercase text-xs font-weight-bold tracking-wider" style="color: #636e72; letter-spacing: 0.5px;"><?= $card['text'] ?></span>
                        <div class="h3 font-weight-bold text-gray-800 mb-0 mt-1">
                            <?= ($card['count'] !== '') ? $card['count'] : '-' ?>
                        </div>
                    </div>
                    <div class="admin-icon-wrapper" style="background: <?= $card['gradient'] ?>;">
                        <i class="fas <?= $card['icon'] ?> fa-lg text-white"></i>
                    </div>
                </a>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Chart Admin 3D Card -->
    <div class="row">
        <div class="col-12">
            <div class="card card-3d mb-4">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: var(--primary-magenta);">
                        <i class="fas fa-chart-bar mr-2"></i>Grafik Distribusi Produk per Kategori
                    </h6>
                    <span class="badge badge-pill font-weight-normal px-3 py-1" style="background-color: var(--soft-pink-bg); color: var(--primary-magenta);">Visualisasi Real-time</span>
                </div>
                <div class="card-body">
                    <canvas id="myChart" style="max-height: 320px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
    <?php
    $mapel = [];
    $jumlah = [];
    $sql = mysqli_query($koneksi, "SELECT * FROM alternatif GROUP BY kategori");
    while ($dr = mysqli_fetch_array($sql)) {
        $mapel[] = $dr['kategori'];
        $jumlah[] = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM alternatif WHERE kategori='{$dr['kategori']}'"));
    }
    ?>
    const ctx = document.getElementById('myChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($mapel); ?>,
            datasets: [{
                label: 'Jumlah Produk',
                data: <?php echo json_encode($jumlah); ?>,
                backgroundColor: ['#b83280', '#e84393', '#8e44ad', '#6c5ce7', '#d63031'],
                borderRadius: 10,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: { 
                y: { 
                    beginAtZero: true,
                    grid: { display: true, color: '#f1f1f1' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
    </script>

    <?php } elseif ($user_role == 'user') { ?>
    <!-- ================================================================= -->
    <!-- DASHBOARD PENGUNJUNG / USER (3D MODERN STYLING)                   -->
    <!-- ================================================================= -->

    <!-- Hero Banner 3D -->
    <div class="welcome-hero-3d mb-5">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-8">
                <span class="badge badge-glass px-3 py-2 mb-3 font-weight-bold">
                    <i class="fas fa-sparkles mr-1" style="color: #ffd1e8;"></i> SPK Metode MOORA
                </span>
                <h2 class="mb-3 font-weight-bold">Bingung Memilih Produk Non-Makanan Terbaik?</h2>
                <p class="lead mb-4" style="opacity: 0.95; font-size: 1.05rem;">
                    Selamat datang, <strong style="color: #ffe0f0;"><?php echo $_SESSION['username']; ?></strong>! Sistem ini membantu Anda menemukan rekomendasi produk non-makanan terbaik di <strong>Pamella Supermarket Yogyakarta</strong> secara objektif dan akurat.
                </p>
                <a href="pemilihan-produk.php" class="btn btn-3d-white btn-lg font-weight-bold px-4 py-3 mr-2 mb-2" style="border-radius: 30px;">
                    <i class="fas fa-search-dollar mr-2"></i> Cari Rekomendasi Sekarang
                </a>
                <a href="ulasan_pengguna.php" class="btn btn-3d-outline btn-lg font-weight-bold px-4 py-3 mb-2" style="border-radius: 30px;">
                    <i class="fas fa-comments mr-2"></i> Lihat Ulasan
                </a>
            </div>
            <!-- Elemen 3D Visual Floating -->
            <div class="col-lg-4 text-center d-none d-lg-block">
                <div class="icon-3d-float">
                    <i class="fas fa-shopping-bag fa-9x text-white" style="opacity: 0.25;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pilihan Cepat Menu Pengunjung (Cards Modern 3D) -->
    <h5 class="text-gray-800 font-weight-bold mb-3"><i class="fas fa-compass mr-2" style="color: var(--primary-magenta);"></i>Eksplorasi Sistem</h5>
    <div class="row mb-4">
        <!-- Card 1: Rekomendasi -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card card-3d h-100 py-3" style="border-top: 5px solid var(--primary-magenta);">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: var(--primary-magenta);">Menu Utama</div>
                            <div class="h5 mb-2 font-weight-bold text-gray-800">Pemilihan Produk</div>
                            <p class="text-muted small mb-4">Tentukan prioritas kriteria Anda untuk mendapatkan rekomendasi produk terbaik.</p>
                            <a href="pemilihan-produk.php" class="btn btn-sm text-white font-weight-bold px-4 py-2" style="background: linear-gradient(135deg, #d63384, #b83280); border-radius: 20px; box-shadow: 0 4px 12px rgba(184, 50, 128, 0.3);">
                                Buka Pemilihan <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        <div class="col-auto">
                            <div class="feature-icon-3d bg-icon-magenta"><i class="fas fa-check-square"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Ulasan -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card card-3d h-100 py-3" style="border-top: 5px solid #8e44ad;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #8e44ad;">Feedback Konsumen</div>
                            <div class="h5 mb-2 font-weight-bold text-gray-800">Ulasan Pengguna</div>
                            <p class="text-muted small mb-4">Lihat apa kata konsumen lain atau berikan ulasan terhadap produk yang ada.</p>
                            <a href="ulasan_pengguna.php" class="btn btn-sm text-white font-weight-bold px-4 py-2" style="background: linear-gradient(135deg, #a55eea, #8e44ad); border-radius: 20px; box-shadow: 0 4px 12px rgba(142, 68, 173, 0.3);">
                                Lihat <?= $julasan ?> Ulasan <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        <div class="col-auto">
                            <div class="feature-icon-3d bg-icon-purple"><i class="fas fa-comments"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Produk & Metode MOORA -->
    <div class="row">
        <!-- Grafik Kategori untuk Pengunjung -->
        <div class="col-lg-7 mb-4">
            <div class="card card-3d h-100">
                <div class="card-header bg-white py-3 border-0 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" style="color: var(--primary-magenta);"><i class="fas fa-chart-pie mr-2"></i>Ketersediaan Produk Rekomendasi</h6>
                    <span class="badge font-weight-normal px-3 py-1" style="background-color: var(--soft-pink-bg); color: var(--primary-magenta); border-radius: 20px;">Total <?= $jproduk ?> Produk</span>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">Berikut adalah persebaran kategori produk non-makanan dari Pamella Supermarket Yogyakarta yang siap dinilai oleh sistem:</p>
                    <canvas id="userChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Edukasi / Kepercayaan terhadap sistem -->
        <div class="col-lg-5 mb-4">
            <div class="card card-3d h-100 border-0" style="background: linear-gradient(180deg, #fdf5f9 0%, #ffffff 100%);">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <div class="text-center mb-3">
                        <i class="fas fa-award fa-3x" style="color: var(--primary-magenta); filter: drop-shadow(0 4px 8px rgba(184, 50, 128, 0.2));"></i>
                    </div>
                    <h5 class="font-weight-bold text-center text-gray-800 mb-2">Mengapa Menggunakan Sistem Ini?</h5>
                    <p class="text-muted small text-center mb-4">Sistem ini didukung oleh metode SPK <strong>MOORA</strong> yang menghitung rekomendasi secara matematis & transparan.</p>
                    
                    <ul class="list-unstyled text-sm text-gray-700 mb-0">
                        <li class="mb-2.5 d-flex align-items-center"><i class="fas fa-check-circle mr-2" style="color: var(--primary-magenta);"></i> <span><strong>Objektif:</strong> Hasil tidak memihak merek tertentu.</span></li>
                        <li class="mb-2.5 d-flex align-items-center"><i class="fas fa-check-circle mr-2" style="color: var(--primary-magenta);"></i> <span><strong>Multi-Kriteria:</strong> Menilai harga, kualitas, & ulasan.</span></li>
                        <li class="d-flex align-items-center"><i class="fas fa-check-circle mr-2" style="color: var(--primary-magenta);"></i> <span><strong>Efisien:</strong> Menghemat waktu saat berbelanja.</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Grafik Khusus User -->
    <script>
    <?php
    $user_mapel = [];
    $user_jumlah = [];
    $sql_user = mysqli_query($koneksi, "SELECT * FROM alternatif GROUP BY kategori");
    while ($dr_u = mysqli_fetch_array($sql_user)) {
        $user_mapel[] = $dr_u['kategori'];
        $user_jumlah[] = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM alternatif WHERE kategori='{$dr_u['kategori']}'"));
    }
    ?>
    const ctxUser = document.getElementById('userChart');
    if (ctxUser) {
        new Chart(ctxUser, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($user_mapel); ?>,
                datasets: [{
                    data: <?php echo json_encode($user_jumlah); ?>,
                    backgroundColor: ['#b83280', '#e84393', '#8e44ad', '#6c5ce7', '#fd79a8'],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    </script>
    <?php }
    require_once('template/footer.php');
} else {
    header('Location: login.php');
}
?>