<?php 
require_once('includes/init.php'); 

$user_role = get_role();
if($user_role == 'admin' || $user_role == 'user') {

$errors = array();
$sukses = false;
$id_user = $_SESSION["user_id"];

if(isset($_POST['submit'])):
    $password  = $_POST['password'];
    $password2 = $_POST['password2'];
    $nama      = $_POST['nama'];
    $email     = $_POST['email'];
    
    if(!$nama) {
        $errors[] = 'Nama tidak boleh kosong';
    }       
    
    if(!$email) {
        $errors[] = 'Email tidak boleh kosong';
    }
    
    if(!$id_user) {
        $errors[] = 'Id User salah';
    }
    
    if($password && ($password != $password2)) {
        $errors[] = 'Password harus sama keduanya';
    }
    
    if(empty($errors)):
        $update = mysqli_query($koneksi, "UPDATE user SET nama = '$nama', email = '$email' WHERE id_user = '$id_user'");
        
        if($password) {
            $pass = sha1($password);
            $update = mysqli_query($koneksi, "UPDATE user SET nama = '$nama', password = '$pass', email = '$email' WHERE id_user = '$id_user'");
        }       
        if($update) {
            $sukses = 'Data profil berhasil diupdate!';
        } else {
            $errors[] = 'Data gagal diupdate';
        }
    endif;
endif;

$page = "Profile";
require_once('template/header.php');
?>

<!-- Style Magenta 3D Modern -->
<style>
    :root {
        --primary-magenta: #d63384;
        --dark-magenta: #b83280;
        --soft-magenta-bg: #fdf0f7;
        --shadow-3d: 0 10px 25px -5px rgba(184, 50, 128, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    .card-magenta-3d {
        border-radius: 1.25rem;
        border: none;
        background: #ffffff;
        box-shadow: var(--shadow-3d);
        overflow: hidden;
    }

    .card-magenta-header {
        background: linear-gradient(135deg, #e84393, #b83280) !important;
        color: #ffffff !important;
        border: none;
        padding: 16px 20px;
    }

    .btn-magenta-3d {
        background: linear-gradient(135deg, #e84393, #b83280);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 9px 22px;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(184, 50, 128, 0.25);
        transition: all 0.3s ease;
    }

    .btn-magenta-3d:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(184, 50, 128, 0.35);
    }

    .form-control:focus {
        border-color: var(--primary-magenta);
        box-shadow: 0 0 0 0.2rem rgba(214, 51, 132, 0.15);
    }
</style>

<!-- Header Halaman -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-fw fa-user mr-2" style="color: var(--primary-magenta);"></i>Data Profile
    </h1>
</div>

<!-- Alert Notifikasi -->
<?php if(!empty($errors)): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-lg mb-4">
        <ul class="mb-0 pl-3">
            <?php foreach($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if($sukses): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-lg mb-4">
        <i class="fas fa-check-circle mr-1"></i> <?php echo $sukses; ?>
    </div>
<?php endif; ?>

<!-- Form Data Profile -->
<form action="" method="post">
    <div class="card card-magenta-3d mb-4">
        <div class="card-header card-magenta-header">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-fw fa-edit mr-2"></i>Edit Data Profile</h6>
        </div>
        
        <?php
        if(!$id_user) {
        ?>
            <div class="card-body">
                <div class="alert alert-danger border-0 rounded-lg">Data user tidak ditemukan.</div>
            </div>
        <?php
        } else {
            $data = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id_user'");
            $cek = mysqli_num_rows($data);
            if($cek <= 0) {
        ?>
                <div class="card-body">
                    <div class="alert alert-danger border-0 rounded-lg">Data tidak ada di database.</div>
                </div>
        <?php
            } else {
                while($d = mysqli_fetch_array($data)){
        ?>
        <div class="card-body p-4">
            <div class="row">
                <div class="form-group col-md-6 mb-3">
                    <label class="font-weight-bold text-gray-800">Username</label>
                    <input autocomplete="off" type="text" readonly required value="<?php echo htmlspecialchars($d['username']); ?>" class="form-control bg-light" />
                    <small class="form-text text-muted">Username tidak dapat diubah.</small>
                </div>
                
                <div class="form-group col-md-6 mb-3">
                    <label class="font-weight-bold text-gray-800">Nama Lengkap</label>
                    <input autocomplete="off" type="text" name="nama" required value="<?php echo htmlspecialchars($d['nama']); ?>" class="form-control" />
                </div>

                <div class="form-group col-md-6 mb-3">
                    <label class="font-weight-bold text-gray-800">E-Mail</label>
                    <input autocomplete="off" type="email" name="email" required value="<?php echo htmlspecialchars($d['email']); ?>" class="form-control" />
                </div>

                <div class="form-group col-md-6 mb-3">
                    <label class="font-weight-bold text-gray-800">Password Baru</label>
                    <input autocomplete="off" type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" class="form-control" />
                </div>
                
                <div class="form-group col-md-6 mb-3">
                    <label class="font-weight-bold text-gray-800">Ulangi Password Baru</label>
                    <input autocomplete="off" type="password" name="password2" placeholder="Masukkan ulang password baru" class="form-control" />
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-0 text-right pb-4 pr-4">
            <button name="submit" value="submit" type="submit" class="btn btn-magenta-3d mr-2"><i class="fa fa-save mr-1"></i> Update Profile</button>
            <button type="reset" class="btn btn-secondary rounded-lg px-4" style="border-radius: 10px;"><i class="fa fa-sync-alt mr-1"></i> Reset</button>
        </div>
        <?php
                }
            }
        }
        ?>
    </div>
</form>

<?php
require_once('template/footer.php');
} else {
    header('Location: login.php');
}
?>