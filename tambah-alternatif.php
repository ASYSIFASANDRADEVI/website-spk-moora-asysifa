<?php require_once('includes/init.php'); ?> 
<?php cek_login($role = array(1)); ?>

<?php
$errors = array();
$sukses = false;

$alternatif = (isset($_POST['alternatif'])) ? trim($_POST['alternatif']) : '';
$nama = (isset($_POST['nama'])) ? trim($_POST['nama']) : '';
$kategori = (isset($_POST['kategori'])) ? trim($_POST['kategori']) : '';
$harga = (isset($_POST['harga'])) ? trim($_POST['harga']) : '';

if (isset($_POST['submit'])):

    // Validasi: format angka dengan titik sebagai ribuan atau desimal, contoh: 200.000 - 300.500
    if (!preg_match('/^\s*\d{1,3}(\.\d{3})*(,\d+)?\s*-\s*\d{1,3}(\.\d{3})*(,\d+)?\s*$/', $harga)) {
        $errors[] = 'Format harga tidak valid. Gunakan format seperti: 200.000 - 300.500';
    }

    if (!$alternatif) {
        $errors[] = 'alternatif tidak boleh kosong';
    }

    if (!$nama) {
        $errors[] = 'Nama tidak boleh kosong';
    }

    if (!$kategori) {
        $errors[] = 'kategori tidak boleh kosong';
    }

    // Validasi file gambar
    if ($_FILES['gambar']['error'] === 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $allowed_mime = ['image/jpeg', 'image/png', 'image/gif'];

        $file_name = $_FILES['gambar']['name'];
        $file_tmp  = $_FILES['gambar']['tmp_name'];
        $file_size = $_FILES['gambar']['size'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_mime = mime_content_type($file_tmp);

        if (!in_array($file_ext, $allowed_ext) || !in_array($file_mime, $allowed_mime)) {
            $errors[] = "File harus berupa gambar dengan format jpg, jpeg, png, atau gif.";
        }

        if ($file_size > 10 * 1024 * 1024) {
            $errors[] = "Ukuran gambar maksimal 10MB.";
        }

        $new_file_name = time() . '-' . basename($file_name);
        $upload_path = 'uploads/' . $new_file_name;

    } else {
        $errors[] = 'Gambar wajib diunggah.';
    }

    if (empty($errors)):
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $simpan = mysqli_query($koneksi, "INSERT INTO alternatif (id_alternatif, alternatif, nama, kategori, gambar, harga) VALUES ('', '$alternatif', '$nama', '$kategori', '$new_file_name', '$harga')");
            if ($simpan) {
                redirect_to('list-alternatif.php?status=sukses-baru');
            } else {
                $errors[] = 'Data gagal disimpan ke database.';
            }
        } else {
            $errors[] = 'Gagal mengunggah file.';
        }
    endif;

endif;

$page = "Alternatif";
require_once('template/header.php');
?>


<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-dolly"></i> Data Produk</h1>

    <a href="list-alternatif.php" class="btn btn-secondary btn-icon-split">
        <span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
        <span class="text">Kembali</span>
    </a>
</div>
			
<?php if(!empty($errors)): ?>
	<div class="alert alert-info">
		<?php foreach($errors as $error): ?>
			<?php echo $error; ?><br>
		<?php endforeach; ?>
	</div>
<?php endif; ?>			
			
<form action="tambah-alternatif.php" method="post" enctype="multipart/form-data">
	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-info"><i class="fas fa-fw fa-plus"></i> Tambah Data Produk</h6>
		</div>
		<div class="card-body">
			<div class="row">				
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Nama Alternatif</label>
					<input autocomplete="off" type="text" name="alternatif" required value="<?php echo $alternatif; ?>" class="form-control"/>
				</div>

				<div class="form-group col-md-6">
					<label class="font-weight-bold">Nama Produk</label>
					<input autocomplete="off" type="text" name="nama" required value="<?php echo $nama; ?>" class="form-control"/>
				</div>
                  
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Kategori Produk</label>
					<select name="kategori" required class="form-control">
						<option value="">--Pilih--</option>
						<option value="Elektronik" <?php if($kategori == "Elektronik") echo "selected"; ?>>Elektronik</option>
						<option value="Mandi" <?php if($kategori == "Mandi") echo "selected"; ?>>Mandi</option>
						<option value="Rumah Tangga" <?php if($kategori == "Rumah Tangga") echo "selected"; ?>>Rumah Tangga</option>
					</select>
				</div>

                <div class="form-group col-md-6">
					<label class="font-weight-bold">Harga Produk (cth: 200.000 - 300.500)</label>
					<input type="text" name="harga" required class="form-control" id="duit" value="<?php echo htmlspecialchars($harga); ?>">
				</div>

				<div class="form-group col-md-6">
					<label class="font-weight-bold">File Gambar</label>
					<input type="file" name="gambar" required class="form-control"/>
				</div>
			</div>
		</div>
		<div class="card-footer text-right">
            <button name="submit" value="submit" type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
            <button type="reset" class="btn btn-info"><i class="fa fa-sync-alt"></i> Reset</button>
        </div>
	</div>
</form>

<script>
 var duit = document.getElementById('duit');
 duit.addEventListener('keyup', function(e) {
     duit.value = formatRupiah(this.value);
 });
</script>

<?php
require_once('template/footer.php');
?>