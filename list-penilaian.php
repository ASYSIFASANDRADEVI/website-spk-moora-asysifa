<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1)); ?>

<?php
// AKTIFKAN ERROR UNTUK DEBUG
ini_set('display_errors', 1);
error_reporting(E_ALL);

$kriterias = mysqli_query($koneksi,"SELECT * FROM kriteria ORDER BY kode_kriteria ASC");
$alternatifs = mysqli_query($koneksi,"SELECT * FROM alternatif");

// Matrix Keputusan (X)
$matriks_x = array();
foreach($kriterias as $kriteria):
    foreach($alternatifs as $alternatif):
        
        $id_alternatif = $alternatif['id_alternatif'];
        $id_kriteria = $kriteria['id_kriteria'];
        
        if($kriteria['ada_pilihan'] == 1){
            $q4 = mysqli_query($koneksi, "
                SELECT sub_kriteria.nilai 
                FROM penilaian 
                JOIN sub_kriteria 
                ON penilaian.nilai = sub_kriteria.id_sub_kriteria 
                WHERE penilaian.id_alternatif = '$id_alternatif' 
                AND penilaian.id_kriteria = '$id_kriteria'
            ");
            $data = mysqli_fetch_array($q4);
            $nilai = isset($data['nilai']) ? $data['nilai'] : '-';
        } else {
            $q4 = mysqli_query($koneksi, "
                SELECT nilai 
                FROM penilaian 
                WHERE id_alternatif = '$id_alternatif' 
                AND id_kriteria = '$id_kriteria'
            ");
            $data = mysqli_fetch_array($q4);
            $nilai = isset($data['nilai']) ? $data['nilai'] : '-';
        }
        
        $matriks_x[$id_kriteria][$id_alternatif] = $nilai;

    endforeach;
endforeach;

if(isset($_POST['tambah'])):    
    $id_alternatif = $_POST['id_alternatif'];
    $id_kriteria = $_POST['id_kriteria'];
    $nilai = $_POST['nilai'];

    if(!$id_kriteria) {
        $errors[] = 'ID kriteria tidak boleh kosong';
    }
    if(!$id_alternatif) {
        $errors[] = 'ID Alternatif kriteria tidak boleh kosong';
    }       
    if(!$nilai) {
        $errors[] = 'Nilai kriteria tidak boleh kosong';
    }   
    
    if(empty($errors)):
        $i = 0;
        foreach ($nilai as $key) {
            $simpan = mysqli_query($koneksi,"INSERT INTO penilaian (id_penilaian, id_alternatif, id_kriteria, nilai) VALUES ('', '$id_alternatif', '$id_kriteria[$i]', '$key')");
            $i++;
        }
        if($simpan) {
            header("Location: list-penilaian.php?status=sukses");
            exit;
        } else {
            header("Location: list-penilaian.php?status=gagal");
            exit;
        }
    endif;
endif;

if(isset($_POST['edit'])):  
    $id_alternatif = $_POST['id_alternatif'];
    $id_kriteria = $_POST['id_kriteria'];
    $nilai = $_POST['nilai'];

    if(!$id_kriteria) {
        $errors[] = 'ID kriteria tidak boleh kosong';
    }
    if(!$id_alternatif) {
        $errors[] = 'ID Alternatif kriteria tidak boleh kosong';
    }       
    if(!$nilai) {
        $errors[] = 'Nilai kriteria tidak boleh kosong';
    }   
    
    if(empty($errors)):
        $i = 0;
        mysqli_query($koneksi,"DELETE FROM penilaian WHERE id_alternatif = '$id_alternatif';");
        foreach ($nilai as $key) {
            $simpan = mysqli_query($koneksi,"INSERT INTO penilaian (id_penilaian, id_alternatif, id_kriteria, nilai) VALUES ('', '$id_alternatif', '$id_kriteria[$i]', '$key')");
            $i++;
        }
        if($simpan) {
            header("Location: list-penilaian.php?status=update_sukses");
            exit;
        } else {
            header("Location: list-penilaian.php?status=update_gagal");
            exit;
        }
    endif;
endif;

$page = "Penilaian";
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
    .card-penilaian-3d {
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

    /* Badge Kode Alternatif & Keterangan */
    .badge-alternatif-3d {
        background: linear-gradient(135deg, #f8f0fc, #fce4ec);
        color: var(--dark-magenta);
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 8px;
        display: inline-block;
    }
    
    .badge-keterangan-3d {
        background-color: #fce4ec;
        color: var(--dark-magenta);
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-block;
        font-size: 0.85rem;
    }

    /* Buttons Style */
    .btn-3d-action {
        border-radius: 20px;
        padding: 6px 16px;
        border: none;
        font-weight: 600;
        transition: all 0.2s ease;
        color: #ffffff !important;
    }
    .btn-3d-action:hover {
        transform: translateY(-2px);
    }
    .btn-3d-input {
        background: linear-gradient(135deg, #2ec4b6, #0f9f90);
        box-shadow: 0 4px 10px rgba(46, 196, 182, 0.3);
    }
    .btn-3d-edit {
        background: linear-gradient(135deg, #ffb703, #fb8500);
        box-shadow: 0 4px 10px rgba(251, 133, 0, 0.3);
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

<!-- Header Halaman -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-fw fa-edit mr-2" style="color: var(--primary-magenta);"></i>Data Penilaian
    </h1>
</div>

<!-- Alert Notifikasi -->
<?php
if(isset($_GET['status'])):
    $status = $_GET['status'];
    $pesan = '';
    if($status == 'sukses') $pesan = 'Data berhasil disimpan.';
    elseif($status == 'gagal') $pesan = 'Data gagal disimpan.';
    elseif($status == 'update_sukses') $pesan = 'Data berhasil diupdate.';
    elseif($status == 'update_gagal') $pesan = 'Data gagal diupdate.';
?>
<div class="alert alert-custom-magenta alert-dismissible fade show shadow-sm mb-4" role="alert">
    <i class="fas fa-info-circle mr-2"></i> <strong>Informasi:</strong> <?= $pesan ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<!-- Main Card Container -->
<div class="card card-penilaian-3d mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);">
            <i class="fa fa-table mr-2"></i>Daftar Data Penilaian
        </h6>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-magenta m-0" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr align="center">
                        <th width="5%">No</th>
                        <th width="15%">Nama Alternatif</th>
                        <th>Nama Produk</th>
                        <?php foreach ($kriterias as $kriteria): ?>
                            <th><?= $kriteria['kode_kriteria'] ?><br><small style="font-weight: normal; opacity: 0.9;"><?= $kriteria['nama'] ?></small></th>
                        <?php endforeach ?>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($koneksi,"SELECT * FROM alternatif");         
                    while($data = mysqli_fetch_array($query)){
                        $id_alternatif = $data['id_alternatif'];
                        $jum = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM penilaian WHERE id_alternatif='$id_alternatif'"));
                    ?>
                    <tr align="center">
                        <td class="align-middle font-weight-bold"><?=$no ?></td>
                        <td class="align-middle">
                            <span class="badge-alternatif-3d"><?= $data['alternatif'] ?></span>
                        </td>
                        <td align="left" class="align-middle font-weight-bold text-gray-800"><?= $data['nama'] ?></td>
                        
                        <?php
                        foreach ($kriterias as $kriteria):
                            $id_kriteria = $kriteria['id_kriteria'];
                            $dt = $matriks_x[$id_kriteria][$id_alternatif];
                            $keter = '';
                            
                            if($dt==10 and $kriteria['kode_kriteria']=='C1'){$keter = 'Sangat mahal';}  
                            if($dt==20 and $kriteria['kode_kriteria']=='C1'){$keter = 'Mahal';}
                            if($dt==30 and $kriteria['kode_kriteria']=='C1'){$keter = 'Cukup murah';}
                            if($dt==40 and $kriteria['kode_kriteria']=='C1'){$keter = 'Murah';}
                            if($dt==50 and $kriteria['kode_kriteria']=='C1'){$keter = 'Sangat murah';}
                            
                            if($dt==10 and $kriteria['kode_kriteria']=='C2'){$keter = 'Sangat buruk';}  
                            if($dt==20 and $kriteria['kode_kriteria']=='C2'){$keter = 'Buruk';}
                            if($dt==30 and $kriteria['kode_kriteria']=='C2'){$keter = 'Cukup baik';}
                            if($dt==40 and $kriteria['kode_kriteria']=='C2'){$keter = 'Baik';}
                            if($dt==50 and $kriteria['kode_kriteria']=='C2'){$keter = 'Sangat Baik';}
                            
                            if($dt==10 and $kriteria['kode_kriteria']=='C3'){$keter = 'Sangat tidak menarik';}  
                            if($dt==20 and $kriteria['kode_kriteria']=='C3'){$keter = 'Kurang menarik';}
                            if($dt==30 and $kriteria['kode_kriteria']=='C3'){$keter = 'Standar';}
                            if($dt==40 and $kriteria['kode_kriteria']=='C3'){$keter = 'Menarik';}
                            if($dt==50 and $kriteria['kode_kriteria']=='C3'){$keter = 'Sangat menarik';}
                            
                            echo '<td class="align-middle">';
                            if($jum <> 0 && !empty($keter)){
                                echo '<span class="badge-keterangan-3d">'.$keter.'</span>';
                            } else if($jum <> 0) {
                                echo '<span class="badge-keterangan-3d">'.$dt.'</span>';
                            } else {
                                echo '<span class="text-muted">-</span>';
                            }
                            echo '</td>';
                        endforeach;
                        ?>
                        
                        <?php
                        $q = mysqli_query($koneksi,"SELECT * FROM penilaian WHERE id_alternatif='$id_alternatif'");
                        $cek_tombol = mysqli_num_rows($q);
                        ?>
                        <td class="align-middle">
                        <?php if ($cek_tombol==0) { ?>
                            <a data-toggle="modal" href="#set<?= $data['id_alternatif'] ?>" class="btn btn-3d-action btn-3d-input btn-sm">
                                <i class="fa fa-plus mr-1"></i> Input
                            </a>
                        <?php } else { ?>
                            <a data-toggle="modal" href="#edit<?= $data['id_alternatif'] ?>" class="btn btn-3d-action btn-3d-edit btn-sm">
                                <i class="fa fa-edit mr-1"></i> Edit
                            </a>
                        <?php } ?>
                        </td>
                    </tr>
                
                    <!-- Modal Input Penilaian -->
                    <div class="modal fade" id="set<?= $data['id_alternatif'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content modal-content-3d">
                                <div class="modal-header modal-header-magenta">
                                    <h5 class="modal-title font-weight-bold" id="myModalLabel"><i class="fa fa-plus mr-2"></i>Input Penilaian</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                </div>
                                <form action="" method="post">
                                    <div class="modal-body p-4">
                                        <?php
                                        $q2 = mysqli_query($koneksi,"SELECT * FROM kriteria ORDER BY kode_kriteria ASC");           
                                        while($d = mysqli_fetch_array($q2)){
                                        ?>
                                        <input type="text" name="id_alternatif" value="<?= $data['id_alternatif'] ?>" hidden>
                                        <input type="text" name="id_kriteria[]" value="<?= $d['id_kriteria'] ?>" hidden>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-gray-800">(<?= $d['kode_kriteria'] ?>) <?= $d['nama'] ?></label>
                                            <?php
                                            if($d['ada_pilihan']==1){
                                            ?>
                                            <select name="nilai[]" class="form-control" required>
                                                <option value="">--Pilih--</option>
                                                <?php
                                                $id_kriteria = $d['id_kriteria'];
                                                $q3 = mysqli_query($koneksi,"SELECT * FROM sub_kriteria WHERE id_kriteria = '$id_kriteria' ORDER BY nilai ASC");            
                                                while($d3 = mysqli_fetch_array($q3)){
                                                ?>
                                                <option value="<?= $d3['id_sub_kriteria'] ?>"><?= $d3['nama'] ?> </option>
                                                <?php } ?>
                                            </select>
                                            <?php
                                            }else{
                                            ?>
                                            <input type="number" name="nilai[]" class="form-control" step="0.001" required autocomplete="off">
                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal"><i class="fa fa-times mr-1"></i> Batal</button>
                                        <button type="submit" name="tambah" class="btn btn-3d-action btn-3d-input px-4"><i class="fa fa-save mr-1"></i> Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Edit Penilaian -->
                    <div class="modal fade" id="edit<?= $data['id_alternatif'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content modal-content-3d">
                                <div class="modal-header modal-header-magenta">
                                    <h5 class="modal-title font-weight-bold" id="myModalLabel"><i class="fa fa-edit mr-2"></i>Edit Penilaian</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                </div>
                                <form action="" method="post">
                                    <div class="modal-body p-4">
                                        <?php
                                        $q2 = mysqli_query($koneksi,"SELECT * FROM kriteria ORDER BY kode_kriteria ASC");           
                                        while($d = mysqli_fetch_array($q2)){
                                        $id_kriteria = $d['id_kriteria'];
                                        $id_alternatif = $data['id_alternatif'];
                                        $q4 = mysqli_query($koneksi,"SELECT * FROM penilaian WHERE id_alternatif='$id_alternatif' AND id_kriteria='$id_kriteria'");         
                                        $d4 = mysqli_fetch_array($q4);
                                        ?>
                                        <input type="text" name="id_alternatif" value="<?= $data['id_alternatif'] ?>" hidden>
                                        <input type="text" name="id_kriteria[]" value="<?= $d['id_kriteria'] ?>" hidden>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-gray-800">(<?= $d['kode_kriteria'] ?>) <?= $d['nama'] ?></label>
                                            <?php
                                            if($d['ada_pilihan']==1){
                                            ?>
                                            <select name="nilai[]" class="form-control" required>
                                                <option value="">--Pilih--</option>
                                                <?php
                                                if(isset($id_kriteria)){
                                                    $q3 = mysqli_query($koneksi,"SELECT * FROM sub_kriteria WHERE id_kriteria = '$id_kriteria' ORDER BY nilai ASC");
                                                    if($q3){
                                                        while($d3 = mysqli_fetch_array($q3)){
                                                            $selected = (isset($d4['nilai']) && $d3['id_sub_kriteria'] == $d4['nilai']) ? "selected" : "";
                                                            echo "<option value='{$d3['id_sub_kriteria']}' $selected>{$d3['nama']}</option>";
                                                        }
                                                    } else {
                                                        echo "<option disabled>Data sub kriteria tidak ditemukan</option>";
                                                    }
                                                } else {
                                                    echo "<option disabled>Pilih kriteria terlebih dahulu</option>";
                                                }
                                                ?>
                                            </select>
                                            <?php
                                            }else{
                                            ?>
                                            <input type="number" name="nilai[]" class="form-control" step="0.001" value="<?= $d4['nilai'] ?>" required autocomplete="off">
                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal"><i class="fa fa-times mr-1"></i> Batal</button>
                                        <button type="submit" name="edit" class="btn btn-3d-action btn-3d-edit px-4"><i class="fa fa-save mr-1"></i> Update</button>
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
require_once('template/footer.php');
?>