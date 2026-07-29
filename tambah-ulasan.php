<?php 
require_once('includes/init.php'); 

// Tangani submit terlebih dahulu, sebelum ada output HTML
if (isset($_POST['submit'])) {
    $tanggal = date('Y-m-d');
    $ida = $_POST['ida'];
    $ulasan = $_POST['ulasan'];
    $usr = $_POST['usr'];
    $rating = $_POST['rating'];

    $cek = mysqli_query($koneksi, "SELECT * FROM data_ulasan WHERE id_alternatif='$ida' AND user='$usr'");
    if (mysqli_num_rows($cek) > 0) {
        echo '<div class="alert alert-warning">Kamu sudah memberi ulasan untuk produk ini.</div>';
        echo '<meta http-equiv="refresh" content="2;url=data_ulasan.php">';
        exit;
    }

    $simpan = mysqli_query($koneksi, "INSERT INTO data_ulasan(id,tanggal,id_alternatif,user,rating,ulasan) 
        VALUES ('','$tanggal','$ida','$usr','$rating','$ulasan')");

    if ($simpan) {
        header('Location: ulasan_pengguna.php');
        exit;
    }else {
    echo '<div class="alert alert-danger">Gagal menyimpan ulasan.</div>';
}
}

require_once('template/header.php'); // Setelah logika POST
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-dolly"></i> Ulasan Produk</h1>
</div>

<?php 
$ida = $_GET['ida'];
$usr = $_GET['usr'];

$data = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM alternatif  WHERE id_alternatif='$ida'"));
$getC2 = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_kriteria FROM kriteria WHERE kode_kriteria='C2'"));
$getC3 = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_kriteria FROM kriteria WHERE kode_kriteria='C3'"));

$getKualitas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT s.nama FROM penilaian p 
    JOIN sub_kriteria s ON p.nilai = s.id_sub_kriteria 
    WHERE p.id_kriteria = '{$getC2['id_kriteria']}' AND p.id_alternatif = '$ida'"));

$getDesain = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT s.nama FROM penilaian p 
    JOIN sub_kriteria s ON p.nilai = s.id_sub_kriteria 
    WHERE p.id_kriteria = '{$getC3['id_kriteria']}' AND p.id_alternatif = '$ida'"));
?>	

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="ida" value="<?= $ida; ?>">
    <input type="hidden" name="usr" value="<?= $usr; ?>">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-fw fa-plus"></i> Tambah Ulasan Produk</h6>
        </div>
        <div class="card-body">
            <div class="row">				
                <div class="form-group col-md-6">
                    <label class="font-weight-bold">Kategori Produk</label>
                    <input type="text" value="<?= $data['kategori'] ?>" class="form-control" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label class="font-weight-bold">Nama Produk</label>
                    <input type="text" value="<?= $data['nama'] ?>" class="form-control" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label class="font-weight-bold">Harga Produk</label>
                    <input type="text" class="form-control" value="Rp <?= htmlspecialchars($data['harga']) ?>" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label class="font-weight-bold">Gambar Produk</label><br>
                    <?php if (!empty($data['gambar']) && file_exists('uploads/' . $data['gambar'])): ?>
                        <img src="uploads/<?php echo $data['gambar']; ?>" class="img-fluid" width="240px">
                    <?php else: ?>
                        <img src="uploads/default.jpg" class="img-fluid" width="128px">
                    <?php endif; ?>
                </div>

                <!-- Rating bintang -->
                <div class="form-group col-md-12">
                    <label class="font-weight-bold">Rating Produk</label><br>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="mr-2">
                            <input type="radio" name="rating" value="<?= $i ?>" required> <?= str_repeat('★', $i) ?>
                        </label>
                    <?php endfor; ?>
                </div>

                <div class="form-group col-md-12">
                    <label class="font-weight-bold">Ulasan Produk</label>
                    <textarea class="form-control" name="ulasan" rows="5" required></textarea>
                </div>
            </div>

            <div class="card-footer text-right">
                <button name="submit" value="submit" type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</form>

<?php require_once('template/footer.php'); ?>