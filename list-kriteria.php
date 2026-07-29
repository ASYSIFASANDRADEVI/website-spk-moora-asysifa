<?php
require_once('includes/init.php');
cek_login($role = array(1,2));
$page = "Kriteria";
require_once('template/header.php');
?>

<style>
    /* Root Variable Warna Magenta & Shadow 3D */
    :root {
        --primary-magenta: #d63384;
        --dark-magenta: #b83280;
        --soft-magenta-bg: #fdf0f7;
        --shadow-3d: 0 10px 25px -5px rgba(184, 50, 128, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    /* Container Card Utama 3D */
    .card-kriteria-3d {
        border-radius: 1.25rem;
        border: none;
        background: #ffffff;
        box-shadow: var(--shadow-3d);
        overflow: hidden;
    }

    /* Header Tabel Magenta Matching dengan Sidebar */
    .table-magenta thead tr {
        background: linear-gradient(135deg, #e84393, #b83280) !important;
        color: #ffffff !important;
    }

    .table-magenta thead th {
        border: none !important;
        font-weight: 600;
        padding: 1rem !important;
    }

    .table-magenta tbody tr:hover {
        background-color: var(--soft-magenta-bg);
    }

    /* Badge Kode Kriteria 3D */
    .badge-kode-3d {
        background: #f1f3f9;
        color: var(--dark-magenta);
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 8px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
        display: inline-block;
    }

    /* Badge Type Modern (Cost & Benefit) */
    .badge-type-cost {
        background-color: #fff0f3;
        color: #e63946;
        border: 1px solid #ffb5a7;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
    }

    .badge-type-benefit {
        background-color: #e8f8f5;
        color: #2ec4b6;
        border: 1px solid #b2f2bb;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
    }

    /* Tombol-tombol 3D Pop-Out */
    .btn-3d-add {
        background: linear-gradient(135deg, #2ec4b6, #0f9f90);
        color: #ffffff !important;
        border: none;
        border-radius: 25px;
        padding: 8px 20px;
        font-weight: 600;
        box-shadow: 0 6px 15px rgba(46, 196, 182, 0.3);
        transition: all 0.25s ease;
    }
    .btn-3d-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(46, 196, 182, 0.45);
    }

    .btn-3d-action {
        border-radius: 8px;
        padding: 6px 10px;
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

    /* Custom Alert */
    .alert-custom-magenta {
        background-color: var(--soft-magenta-bg);
        border-left: 4px solid var(--primary-magenta);
        color: var(--dark-magenta);
        border-radius: 10px;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-fw fa-cube mr-2" style="color: var(--primary-magenta);"></i>Data Kriteria
    </h1>

    <a href="tambah-kriteria.php" class="btn btn-3d-add"> 
        <i class="fa fa-plus mr-1"></i> Tambah Data 
    </a>
</div>

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

<div class="card card-kriteria-3d mb-4">
    <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);">
            <i class="fa fa-table mr-2"></i>Daftar Data Kriteria
        </h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-magenta" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr align="center">
                        <th width="5%">No</th>
                        <th width="18%">Kode Kriteria</th>
                        <th>Nama Kriteria</th>
                        <th width="15%">Type</th>
                        <th width="15%">Bobot</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                $query = mysqli_query($koneksi,"SELECT * FROM kriteria ORDER BY kode_kriteria ASC");            
                while($data = mysqli_fetch_array($query)):
                ?>
                    <tr align="center">
                        <td class="align-middle font-weight-bold"><?php echo $no; ?></td>
                        <td class="align-middle">
                            <span class="badge-kode-3d"><?php echo $data['kode_kriteria']; ?></span>
                        </td>
                        <td align="left" class="align-middle font-weight-bold text-gray-800">
                            <?php echo $data['nama']; ?>
                        </td>
                        <td class="align-middle">
                            <?php if(strtolower($data['type']) == 'cost'): ?>
                                <span class="badge-type-cost"><i class="fas fa-arrow-down mr-1"></i>Cost</span>
                            <?php else: ?>
                                <span class="badge-type-benefit"><i class="fas fa-arrow-up mr-1"></i>Benefit</span>
                            <?php endif; ?>
                        </td>
                        <td class="align-middle font-weight-bold" style="color: var(--dark-magenta);"><?php echo $data['bobot']; ?></td>
                        <td class="align-middle">
                            <div class="btn-group" role="group">
                                <a data-toggle="tooltip" data-placement="bottom" title="Edit Data" href="edit-kriteria.php?id=<?php echo $data['id_kriteria']; ?>" class="btn btn-3d-action btn-3d-edit mr-1">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a data-toggle="tooltip" data-placement="bottom" title="Hapus Data" href="hapus-kriteria.php?id=<?php echo $data['id_kriteria']; ?>" onclick="return confirm('Apakah anda yakin untuk meghapus data ini')" class="btn btn-3d-action btn-3d-delete">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                    $no++;
                    endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once('template/footer.php');
?>