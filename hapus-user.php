<?php 
require_once('includes/init.php'); 
// Memastikan hanya Admin (role 1) yang bisa menghapus data
cek_login($role = array(1)); 

$ada_error = false;
$id_user_hapus = (isset($_GET['id'])) ? trim($_GET['id']) : '';

// Ambil ID user yang sedang login dari Session (sesuaikan nama session ID jika berbeda, misal: $_SESSION['id_user'])
$id_user_login = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '');

if(!$id_user_hapus) {
    $ada_error = 'Maaf, data tidak dapat diproses (ID tidak ditemukan).';
} else {
    // 1. Cek apakah data user yang mau dihapus ada di database
    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = '$id_user_hapus'");
    $cek = mysqli_num_rows($query);
    
    if($cek <= 0) {
        $ada_error = 'Maaf, data user tidak ditemukan di database.';
    } else {
        // 2. KEAMANAN MULTI-ADMIN: Cek jika admin mencoba menghapus akunnya sendiri
        if ($id_user_login && $id_user_hapus == $id_user_login) {
            redirect_to('list-user.php?status=gagal-hapus-diri-sendiri');
            exit;
        }

        // 3. KEAMANAN MULTI-ADMIN: Cek jumlah total Admin yang tersisa
        $query_admin = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user WHERE role = 1");
        $data_admin = mysqli_fetch_assoc($query_admin);

        if ($data_admin['total'] <= 1) {
            // Tolak hapus jika ini adalah satu-satunya Admin di sistem
            redirect_to('list-user.php?status=gagal-admin-terakhir');
            exit;
        }

        // 4. Jika semua syarat aman, jalankan perintah HAPUS
        $hapus = mysqli_query($koneksi, "DELETE FROM user WHERE id_user = '$id_user_hapus'");
        
        if($hapus) {
            // Redirect kembali ke list-user.php
            redirect_to('list-user.php?status=sukses-hapus');
            exit;
        } else {
            $ada_error = 'Gagal menghapus data dari database.';
        }
    }
}

// Tampilan jika terjadi error di luar redirect
$page = "User";
require_once('template/header.php');
?>

<div class="container-fluid mt-4">
    <?php if($ada_error): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-lg py-3">
            <i class="fas fa-exclamation-triangle mr-2"></i> <?php echo $ada_error; ?>
        </div>
        <a href="list-user.php" class="btn btn-secondary mt-2"><i class="fa fa-arrow-left mr-1"></i> Kembali ke Data User</a>
    <?php endif; ?>
</div>

<?php
require_once('template/footer.php');
?>