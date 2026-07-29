<?php 
require_once('includes/init.php'); 
// Memastikan hanya Admin (role 1) yang bisa mengelola data akun Admin lainnya
cek_login($role = array(1)); 

$page = "User";
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

    .badge-admin-3d {
        background: linear-gradient(135deg, #f8f0fc, #fce4ec);
        color: var(--dark-magenta);
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 8px;
        display: inline-block;
    }
</style>

<!-- Header Halaman -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-fw fa-users-cog mr-2" style="color: var(--primary-magenta);"></i>Data Pengguna / Admin
    </h1>

    <a href="tambah-user.php" class="btn btn-magenta-3d"> 
        <i class="fa fa-plus mr-1"></i> Tambah Admin 
    </a>
</div>

<?php
$status = isset($_GET['status']) ? $_GET['status'] : '';
$msg = '';
$alert_type = 'alert-success';

switch($status):
    case 'sukses-baru':
        $msg = 'Data berhasil disimpan';
        break;
    case 'sukses-hapus':
        $msg = 'Data berhasil dihapus';
        break;
    case 'sukses-edit':
        $msg = 'Data berhasil diupdate';
        break;
    case 'gagal-hapus-diri-sendiri':
        $msg = 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan!';
        $alert_type = 'alert-danger';
        break;
    case 'gagal-admin-terakhir':
        $msg = 'Gagal menghapus! Harus tersisa minimal 1 Admin di sistem.';
        $alert_type = 'alert-danger';
        break;
endswitch;

if($msg):
    echo '<div class="alert '.$alert_type.' border-0 shadow-sm rounded-lg">'.$msg.'</div>';
endif;
?>

<!-- Card Tabel Pengguna -->
<div class="card card-magenta-3d mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);">
            <i class="fa fa-table mr-2"></i>Daftar Pengguna Sistem
        </h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-magenta m-0" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr align="center"> 
                        <th width="5%">No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Level Access</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 0;
                    $query = mysqli_query($koneksi, "SELECT * FROM user");
                    while($data = mysqli_fetch_array($query)):
                    $no++;
                    ?>
                        <tr align="center">
                            <td class="font-weight-bold"><?php echo $no; ?></td>
                            <td align="left" class="font-weight-bold text-gray-800"><?php echo $data['username']; ?></td>
                            <td align="left"><?php echo $data['nama']; ?></td>
                            <td>
                                <?php
                                if($data['role'] == 1) {
                                    echo '<span class="badge-admin-3d"><i class="fas fa-user-shield mr-1"></i> Administrator</span>';
                                } else {
                                    echo '<span class="badge badge-light border text-dark px-3 py-2" style="border-radius:6px;">User</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a data-toggle="tooltip" data-placement="bottom" title="Edit Data" href="edit-user.php?id=<?php echo $data['id_user']; ?>" class="btn btn-warning btn-sm rounded-circle mr-1"><i class="fa fa-edit"></i></a>
                                    <a data-toggle="tooltip" data-placement="bottom" title="Hapus Data" href="hapus-user.php?id=<?php echo $data['id_user']; ?>" onclick="return confirm('Apakah anda yakin untuk menghapus data ini?')" class="btn btn-danger btn-sm rounded-circle"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once('template/footer.php');
?>