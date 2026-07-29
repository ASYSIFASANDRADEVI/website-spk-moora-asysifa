<?php
require_once('includes/init.php');

$user_role = get_role();
if($user_role == 'admin' || $user_role == 'user') {

$page = "Hasil";
require_once('template/header.php');
?>

<style>
    :root {
        --primary-magenta: #d63384;
        --dark-magenta: #b83280;
        --soft-magenta-bg: #fdf0f7;
        --shadow-3d: 0 10px 25px -5px rgba(184, 50, 128, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }
    .card-perhitungan-3d { border-radius: 1.25rem; border: none; background: #ffffff; box-shadow: var(--shadow-3d); overflow: hidden; }
    .table-magenta thead tr { background: linear-gradient(135deg, #e84393, #b83280) !important; color: #ffffff !important; }
    .table-magenta thead th { border: none !important; font-weight: 600; padding: 12px 14px !important; vertical-align: middle !important; }
    .table-magenta tbody td { padding: 12px 14px !important; vertical-align: middle !important; }
    .table-magenta tbody tr:hover { background-color: var(--soft-magenta-bg); }
    .badge-alternatif-3d {
        background: linear-gradient(135deg, #f8f0fc, #fce4ec); color: var(--dark-magenta);
        font-weight: 700; padding: 5px 12px; border-radius: 8px; display: inline-block;
    }
    .img-product-3d { border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); object-fit: cover; transition: transform 0.2s ease; }
    .img-product-3d:hover { transform: scale(1.05); }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-fw fa-chart-area mr-2" style="color: var(--primary-magenta);"></i>Data Hasil Akhir
    </h1>
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

<div class="card card-perhitungan-3d mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);"><i class="fa fa-thumbs-up mr-2"></i>Rekomendasi Produk Terbaik</h6>
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
                        <td><span class="badge badge-light border text-dark px-3 py-2" style="border-radius:6px; font-weight:600;"><?= $kategori ?></span></td>
                        <td align="left" class="font-weight-bold text-gray-800"><?= $produk['nama'] ?></td>
                        <td><span class="badge-alternatif-3d"><?= $produk['alternatif'] ?></span></td>
                        <td class="font-weight-bold text-gray-800" style="background-color: var(--soft-magenta-bg);"><?= round($produk['nilai'], 4) ?></td>
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
                        <td colspan="5" class="text-muted font-italic py-4">Tidak ada produk untuk kategori <b><?= $kategori ?></b></td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once('template/footer.php');
} else {
    header('Location: login.php');
}
?>