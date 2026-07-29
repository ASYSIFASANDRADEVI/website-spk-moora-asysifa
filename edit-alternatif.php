<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1)); ?>

<?php
$errors = array();
$sukses = false;

$ada_error = false;
$id_alternatif = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if(isset($_POST['submit'])):    
    $alternatif = $_POST['alternatif'];
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $harga_input = isset($_POST['harga']) ? trim($_POST['harga']) : '';

    if(!$alternatif) $errors[] = 'alternatif tidak boleh kosong';
    if(!$nama) $errors[] = 'Nama tidak boleh kosong';
    if(!$kategori) $errors[] = 'Kategori tidak boleh kosong';
    if(!$harga_input) $errors[] = 'Harga tidak boleh kosong';

    // Bersihkan karakter selain angka untuk disimpan murni sebagai angka di database
    $harga = preg_replace('/[^0-9]/', '', $harga_input);

    if(empty($errors)):
        $gambar_baru = $_FILES['gambar']['name'];
        $gambar_tmp = $_FILES['gambar']['tmp_name'];
        $gambar_lama = '';

        $get_old = mysqli_query($koneksi, "SELECT gambar FROM alternatif WHERE id_alternatif='$id_alternatif'");
        if ($row = mysqli_fetch_assoc($get_old)) {
            $gambar_lama = $row['gambar'];
        }

        $upload_gambar = false;
        if (!empty($gambar_baru)) {
            $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
            $ext = strtolower(pathinfo($gambar_baru, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_ext)) {
                $nama_file_baru = uniqid() . '.' . $ext;
                $upload_path = 'uploads/' . $nama_file_baru;
                move_uploaded_file($gambar_tmp, $upload_path);
                $upload_gambar = true;

                if (!empty($gambar_lama) && file_exists('uploads/' . $gambar_lama) && $gambar_lama != 'default.jpg') {
                    unlink('uploads/' . $gambar_lama);
                }
            } else {
                $errors[] = 'Format gambar tidak valid (Hanya JPG, JPEG, PNG, GIF)';
            }
        }

        if ($upload_gambar) {
            $update = mysqli_query($koneksi,"UPDATE alternatif SET alternatif = '$alternatif', nama = '$nama', kategori = '$kategori', gambar = '$nama_file_baru', harga = '$harga' WHERE id_alternatif = '$id_alternatif'");
        } else {
            $update = mysqli_query($koneksi,"UPDATE alternatif SET alternatif = '$alternatif', nama = '$nama', kategori = '$kategori', harga = '$harga' WHERE id_alternatif = '$id_alternatif'");
        }

        if($update) {
            redirect_to('list-alternatif.php?status=sukses-edit');
        } else {
            $errors[] = 'Data gagal diupdate';
        }
    endif;
endif;
?>

<?php
$page = "Alternatif";
require_once('template/header.php');
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-dolly"></i> Edit Data Produk</h1>
    <a href="list-alternatif.php" class="btn btn-secondary btn-icon-split">
        <span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
        <span class="text">Kembali</span>
    </a>
</div>

<?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach($errors as $error): ?>
            <div><?php echo $error; ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
if(!$id_alternatif) {
    echo '<div class="alert alert-danger">ID tidak valid</div>';
} else {
    $data = mysqli_query($koneksi,"SELECT * FROM alternatif WHERE id_alternatif='$id_alternatif'");
    if(mysqli_num_rows($data) <= 0) {
        echo '<div class="alert alert-danger">Data tidak ditemukan</div>';
    } else {
        $d = mysqli_fetch_assoc($data);

        // Format tampilan harga awal di input form (misal: 250000 -> 250.000)
        $harga_val = !empty($d['harga']) ? number_format((float)preg_replace('/[^0-9]/', '', $d['harga']), 0, ',', '.') : '';
?>

<form action="edit-alternatif.php?id=<?php echo $id_alternatif; ?>" method="post" enctype="multipart/form-data">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-fw fa-edit"></i> Form Edit Produk</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-6">
                    <label class="font-weight-bold">Nama Alternatif</label>
                    <input type="text" name="alternatif" required value="<?php echo htmlspecialchars($d['alternatif']); ?>" class="form-control"/>
                </div>

                <div class="form-group col-md-6">
                    <label class="font-weight-bold">Nama Produk</label>
                    <input type="text" name="nama" required class="form-control" value="<?php echo htmlspecialchars($d['nama']); ?>">
                </div>

                <div class="form-group col-md-6">
                    <label class="font-weight-bold">Kategori Produk</label>
                    <select name="kategori" required class="form-control">
                        <option value="">--Pilih--</option>
                        <option value="Elektronik" <?php if($d['kategori'] == "Elektronik") echo "selected"; ?>>Elektronik</option>
                        <option value="Mandi" <?php if($d['kategori'] == "Mandi") echo "selected"; ?>>Mandi</option>
                        <option value="Rumah Tangga" <?php if($d['kategori'] == "Rumah Tangga") echo "selected"; ?>>Rumah Tangga</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label class="font-weight-bold">Harga Produk (Rp)</label>
                    <input type="text" id="harga" name="harga" required class="form-control" placeholder="Contoh: 200.000" value="<?php echo $harga_val; ?>">
                </div>

                <div class="form-group col-md-6">
                    <label class="font-weight-bold">Gambar Produk</label>
                    <input type="file" name="gambar" class="form-control mb-2">
                    <?php if (!empty($d['gambar']) && file_exists('uploads/' . $d['gambar'])): ?>
                        <img src="uploads/<?php echo $d['gambar']; ?>" class="img-fluid" width="240px">
                    <?php else: ?>
                        <img src="uploads/default.jpg" class="img-fluid" width="128px">
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button type="submit" name="submit" value="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>
            <button type="reset" class="btn btn-info"><i class="fa fa-sync-alt"></i> Reset</button>
        </div>
    </div>
</form>

<script>
    var hargaInput = document.getElementById('harga');

    if (hargaInput) {
        hargaInput.addEventListener('keyup', function(e) {
            hargaInput.value = formatRupiah(this.value);
        });
    }

    function formatRupiah(angka) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split   = number_string.split(','),
            sisa    = split[0].length % 3,
            rupiah  = split[0].substr(0, sisa),
            ribuan  = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }
</script>

<?php
    }
}
require_once('template/footer.php');
?>