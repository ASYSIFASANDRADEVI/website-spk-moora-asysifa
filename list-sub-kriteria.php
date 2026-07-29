<?php
require_once('includes/init.php');
cek_login($role = array(1,2));
$page = "Sub Kriteria";
require_once('template/header.php');

if(isset($_POST['tambah'])):    
    $id_kriteria = $_POST['id_kriteria'];
    $nama = $_POST['nama'];
    $nilai = $_POST['nilai'];

    if(!$id_kriteria) {
        $errors[] = 'ID kriteria tidak boleh kosong';
    }
    if(!$nama) {
        $errors[] = 'Nama kriteria tidak boleh kosong';
    }       
    if(!$nilai) {
        $errors[] = 'Nilai kriteria tidak boleh kosong';
    }   
    
    if(empty($errors)):
        $simpan = mysqli_query($koneksi,"INSERT INTO sub_kriteria (id_sub_kriteria, id_kriteria, nama, nilai) VALUES ('', '$id_kriteria', '$nama', '$nilai')");
        
        if($simpan) {
            $sts[] = 'Data berhasil disimpan';
        }else{
            $sts[] = 'Data gagal disimpan';
        }
    endif;
endif;

if(isset($_POST['edit'])):  
    $id_sub_kriteria = $_POST['id_sub_kriteria'];
    $id_kriteria = $_POST['id_kriteria'];
    $nama = $_POST['nama'];
    $nilai = $_POST['nilai'];

    if(!$id_kriteria) {
        $errors[] = 'ID kriteria tidak boleh kosong';
    }
    if(!$nama) {
        $errors[] = 'Nama kriteria tidak boleh kosong';
    }       
    if(!$nilai) {
        $errors[] = 'Nilai kriteria tidak boleh kosong';
    }   
    
    if(empty($errors)):
        $update = mysqli_query($koneksi,"UPDATE sub_kriteria SET nama = '$nama', nilai = '$nilai' WHERE id_kriteria = '$id_kriteria' AND id_sub_kriteria = '$id_sub_kriteria'");
        
        if($update) {
            $sts[] = 'Data berhasil diupdate';
        }else{
            $sts[] = 'Data gagal diupdate';
        }
    endif;
endif;
?>

<!-- Style Penyesuaian Agar Presisi Seperti Data Kriteria -->
<style>
    :root {
        --primary-magenta: #d63384;
        --dark-magenta: #b83280;
        --soft-magenta-bg: #fdf0f7;
        --shadow-3d: 0 10px 25px -5px rgba(184, 50, 128, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    /* Card Utama dengan Lengkungan & Bayangan Presisi */
    .card-subkriteria-3d {
        border-radius: 1.25rem;
        border: none;
        background: #ffffff;
        box-shadow: var(--shadow-3d);
    }

    /* Tabel Header & Sel Presisi */
    .table-magenta thead tr {
        background: linear-gradient(135deg, #e84393, #b83280) !important;
        color: #ffffff !important;
    }
    .table-magenta thead th {
        border: none !important;
        font-weight: 600;
        padding: 12px 15px !important;
        vertical-align: middle !important;
    }
    .table-magenta tbody td {
        padding: 12px 15px !important;
        vertical-align: middle !important;
    }
    .table-magenta tbody tr:hover {
        background-color: var(--soft-magenta-bg);
    }

    /* Badge Nilai */
    .badge-nilai-3d {
        background-color: #fce4ec;
        color: var(--dark-magenta);
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 6px;
        display: inline-block;
    }

    /* Tombol */
    .btn-3d-add {
        background: linear-gradient(135deg, #2ec4b6, #0f9f90);
        color: #ffffff !important;
        border: none;
        border-radius: 20px;
        padding: 6px 18px;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(46, 196, 182, 0.25);
        transition: all 0.2s ease;
    }
    .btn-3d-add:hover {
        transform: translateY(-2px);
    }

    .btn-3d-action {
        border-radius: 8px;
        padding: 6px 10px;
        border: none;
        color: #ffffff !important;
        transition: all 0.2s ease;
    }
    .btn-3d-action:hover {
        transform: translateY(-2px);
    }
    .btn-3d-edit {
        background: linear-gradient(135deg, #ffb703, #fb8500);
        box-shadow: 0 3px 8px rgba(251, 133, 0, 0.25);
    }
    .btn-3d-delete {
        background: linear-gradient(135deg, #e63946, #d62828);
        box-shadow: 0 3px 8px rgba(214, 40, 40, 0.25);
    }

    /* Modal Styling */
    .modal-content-3d {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        overflow: hidden;
    }
    .modal-header-magenta {
        background: linear-gradient(135deg, #e84393, #b83280);
        color: #ffffff;
    }
    .modal-header-magenta .close {
        color: #ffffff;
        opacity: 0.8;
    }

    /* Alert Style */
    .alert-custom-magenta {
        background-color: var(--soft-magenta-bg);
        border-left: 4px solid var(--primary-magenta);
        color: var(--dark-magenta);
        border-radius: 10px;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-fw fa-cubes mr-2" style="color: var(--primary-magenta);"></i>Data Sub Kriteria
    </h1>
</div>

<?php if(!empty($sts)): ?>
    <div class="alert alert-custom-magenta alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="fas fa-info-circle mr-2"></i>
        <?php foreach($sts as $st): ?>
            <?php echo $st; ?>
        <?php endforeach; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php
endif;

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
<?php
endif;

$i=0;
$query = mysqli_query($koneksi,"SELECT * FROM kriteria WHERE ada_pilihan='1' ORDER BY kode_kriteria ASC");
$cek = mysqli_num_rows($query);

if($cek <= 0) {
?>
<div class="card card-subkriteria-3d mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);"><i class="fa fa-table mr-2"></i>Daftar Data Sub Kriteria</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-custom-magenta m-0">
            <i class="fas fa-exclamation-circle mr-2"></i> Cara penilaian pada kriteria berjenis input langsung semua.
        </div>
    </div>
</div>
<?php
}else{
    while($data = mysqli_fetch_array($query)){
    $i++;
?>
<div class="card card-subkriteria-3d mb-4">
    <!-- Header Card Kriteria -->
    <div class="card-header bg-white py-3 border-0">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);">
                <i class="fa fa-table mr-2"></i><?= $data['nama']." (".$data['kode_kriteria'].")" ?>
            </h6>
            
            <a href="#tambah<?= $data['id_kriteria']; ?>" data-toggle="modal" class="btn btn-3d-add btn-sm"> 
                <i class="fa fa-plus mr-1"></i> Tambah Data 
            </a>
        </div>
    </div>
    
    <!-- Modal Tambah Sub Kriteria -->
    <div class="modal fade" id="tambah<?= $data['id_kriteria']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-3d">
                <div class="modal-header modal-header-magenta">
                    <h5 class="modal-title font-weight-bold" id="myModalLabel"><i class="fa fa-plus mr-2"></i>Tambah <?= $data['nama'] ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <form action="" method="post">
                    <div class="modal-body p-4">
                        <input type="text" name="id_kriteria" value="<?= $data['id_kriteria']; ?>" hidden>
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-800">Nama Sub Kriteria</label>
                            <input autocomplete="off" type="text" class="form-control" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-800">Nilai</label>
                            <input autocomplete="off" step="0.001" type="number" name="nilai" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal"><i class="fa fa-times mr-1"></i> Batal</button>
                        <button type="submit" name="tambah" class="btn btn-3d-add px-4"><i class="fa fa-save mr-1"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Body Card dengan Padding Bawaan (Membuat Tabel Tidak Mepet Lagi) -->
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-magenta m-0" id="dataTable<?= $i; ?>" width="100%" cellspacing="0">
                <thead>
                    <tr align="center">                    
                        <th width="8%">No</th>
                        <th>Nama Sub Kriteria</th>
                        <th width="20%">Nilai</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $no=1;
                        $id_kriteria = $data['id_kriteria'];
                        $q = mysqli_query($koneksi,"SELECT * FROM sub_kriteria WHERE id_kriteria = '$id_kriteria' ORDER BY nilai DESC");            
                        while($d = mysqli_fetch_array($q)){
                    ?>
                    <tr align="center">
                        <td class="align-middle font-weight-bold"><?=$no ?></td>
                        <td align="left" class="align-middle font-weight-bold text-gray-800"><?= $d['nama'] ?></td>
                        <td class="align-middle">
                            <span class="badge-nilai-3d"><?= $d['nilai'] ?></span>
                        </td>
                        <td class="align-middle">
                            <div class="btn-group" role="group">
                                <a data-toggle="modal" title="Edit Data" href="#editsk<?= $d['id_sub_kriteria'] ?>" class="btn btn-3d-action btn-3d-edit mr-1">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a data-toggle="tooltip" data-placement="bottom" title="Hapus Data" href="hapus-sub-kriteria.php?id=<?php echo $d['id_sub_kriteria']; ?>" onclick="return confirm('Apakah anda yakin untuk meghapus data ini')" class="btn btn-3d-action btn-3d-delete">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit Sub Kriteria -->
                    <div class="modal fade" id="editsk<?= $d['id_sub_kriteria'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content modal-content-3d">
                                <div class="modal-header modal-header-magenta">
                                    <h5 class="modal-title font-weight-bold" id="myModalLabel"><i class="fa fa-edit mr-2"></i>Edit <?= $d['nama'] ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                </div>
                                <form action="list-sub-kriteria.php?id=<?php echo $d['id_sub_kriteria']; ?>" method="post">
                                    <input type="text" name="id_sub_kriteria" value="<?= $d['id_sub_kriteria']; ?>" hidden>
                                    <div class="modal-body p-4">
                                        <input type="text" name="id_kriteria" value="<?= $d['id_kriteria'] ?>" hidden>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-gray-800">Nama Sub Kriteria</label>
                                            <input type="text" autocomplete="off" class="form-control" value="<?= $d['nama'] ?>" name="nama" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-gray-800">Nilai</label>
                                            <input type="number" step="0.001" autocomplete="off" name="nilai" class="form-control" value="<?= $d['nilai'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal"><i class="fa fa-times mr-1"></i> Batal</button>
                                        <button type="submit" name="edit" class="btn btn-3d-add px-4"><i class="fa fa-save mr-1"></i> Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php
                    $no++;
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
}
}
require_once('template/footer.php');
?>