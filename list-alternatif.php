<?php 
require_once('includes/init.php'); 
cek_login($role = array(1)); 

$page = "Alternatif";
require_once('template/header.php');
?>

<!-- Custom CSS Tema Magenta & Style Modern 3D -->
<style>
    :root {
        --primary-magenta: #d63384;
        --dark-magenta: #b83280;
        --soft-magenta-bg: #fdf0f7;
        --shadow-3d: 0 10px 25px -5px rgba(184, 50, 128, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    /* Container Card 3D */
    .card-produk-3d {
        border-radius: 1.25rem;
        border: none;
        background: #ffffff;
        box-shadow: var(--shadow-3d);
        overflow: hidden;
    }

    /* Table Magenta Gradient Header & Spacing */
    .table-magenta thead tr {
        background: linear-gradient(135deg, #e84393, #b83280) !important;
        color: #ffffff !important;
    }
    .table-magenta thead th {
        border: none !important;
        font-weight: 600;
        padding: 14px 15px !important;
        vertical-align: middle !important;
    }
    .table-magenta tbody td {
        padding: 12px 15px !important;
        vertical-align: middle !important;
    }
    .table-magenta tbody tr:hover {
        background-color: var(--soft-magenta-bg);
    }

    /* Styling Gambar Produk */
    .img-produk-thumb {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: 2px solid #ffffff;
        transition: transform 0.2s ease;
    }
    .img-produk-thumb:hover {
        transform: scale(1.08);
    }

    /* Badge Kode Alternatif */
    .badge-alternatif-3d {
        background: linear-gradient(135deg, #f8f0fc, #fce4ec);
        color: var(--dark-magenta);
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 8px;
        display: inline-block;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    }

    /* Buttons Style */
    .btn-3d-add {
        background: linear-gradient(135deg, #2ec4b6, #0f9f90);
        color: #ffffff !important;
        border: none;
        border-radius: 20px;
        padding: 8px 20px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(46, 196, 182, 0.3);
        transition: all 0.25s ease;
    }
    .btn-3d-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(46, 196, 182, 0.45);
    }

    .btn-3d-action {
        border-radius: 8px;
        padding: 6px 12px;
        border: none;
        transition: all 0.2s ease;
        color: #ffffff !important;
    }
    .btn-3d-action:hover {
        transform: translateY(-2px);
    }
    .btn-3d-edit {
        background: linear-gradient(135deg, #ffb703, #fb8500);
        box-shadow: 0 4px 10px rgba(251, 133, 0, 0.3);
    }
    .btn-3d-delete {
        background: linear-gradient(135deg, #e63946, #d62828);
        box-shadow: 0 4px 10px rgba(214, 40, 40, 0.3);
    }

    /* Alert Style */
    .alert-custom-magenta {
        background-color: var(--soft-magenta-bg);
        border-left: 4px solid var(--primary-magenta);
        color: var(--dark-magenta);
        border-radius: 10px;
    }
</style>

<!-- Header Halaman -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-fw fa-dolly mr-2" style="color: var(--primary-magenta);"></i>Data Produk
    </h1>

    <a href="tambah-alternatif.php" class="btn btn-3d-add"> 
        <i class="fa fa-plus mr-1"></i> Tambah Data 
    </a>
</div>

<!-- Alert Notifikasi -->
<?php
$status = isset($_GET['status']) ? $_GET['status'] : '';
$msg = '';
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
endswitch;

if($msg):
?>
    <div class="alert alert-custom-magenta alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="fas fa-check-circle mr-2"></i> <strong>Informasi:</strong> <?php echo $msg; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Main Card Container -->
<div class="card card-produk-3d mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);">
            <i class="fa fa-table mr-2"></i>Daftar Data Produk
        </h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-magenta m-0" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr align="center"> 
                        <th width="5%">No</th>
                        <th width="10%">Gambar</th>
                        <th width="15%">Kode Alternatif</th>
                        <th>Nama Produk</th>
                        <th width="15%">Kategori Produk</th>
                        <th width="15%">Harga Produk</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>         
                <?php
                $no = 0;
                $query = mysqli_query($koneksi, "SELECT * FROM alternatif");         
                while($data = mysqli_fetch_array($query)):
                    $no++;

                    // Pembersihan aman untuk tampilan harga tanpa merusak data asli
                    $harga_clean = preg_replace('/[^0-9]/', '', $data['harga']);
                    $harga_display = !empty($harga_clean) ? 'Rp ' . number_format((float)$harga_clean, 0, ',', '.') : '-';
                ?>
                    <tr align="center">
                        <td class="align-middle font-weight-bold"><?php echo $no; ?></td>
                        <td class="align-middle">
                            <?php if (!empty($data['gambar']) && file_exists('uploads/' . $data['gambar'])): ?>
                                <img src="uploads/<?php echo $data['gambar']; ?>" class="img-produk-thumb" alt="Gambar Produk" />
                            <?php else: ?>
                                <img src="uploads/default.jpg" class="img-produk-thumb" alt="Default Gambar" />
                            <?php endif; ?>
                        </td>
                        <td class="align-middle">
                            <span class="badge-alternatif-3d"><?php echo $data['alternatif']; ?></span>
                        </td>
                        <td align="left" class="align-middle font-weight-bold text-gray-800">
                            <?php echo $data['nama']; ?>
                        </td>
                        <td class="align-middle text-gray-700">
                            <?php echo $data['kategori']; ?>
                        </td>
                        <td class="align-middle font-weight-bold text-success">
                            <?php echo $harga_display; ?>
                        </td>
                        <td class="align-middle">
                            <div class="btn-group" role="group">
                                <a data-toggle="tooltip" data-placement="bottom" title="Edit Data" href="edit-alternatif.php?id=<?php echo $data['id_alternatif']; ?>" class="btn btn-3d-action btn-3d-edit mr-1">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a data-toggle="tooltip" data-placement="bottom" title="Hapus Data" href="hapus-alternatif.php?id=<?php echo $data['id_alternatif']; ?>" onclick="return confirm('Apakah anda yakin untuk menghapus data ini?')" class="btn btn-3d-action btn-3d-delete">
                                    <i class="fa fa-trash"></i>
                                </a>
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