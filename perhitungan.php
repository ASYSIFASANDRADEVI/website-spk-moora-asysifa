<?php
require_once('includes/init.php');

$user_role = get_role();
if($user_role == 'admin') {

$page = "Perhitungan";
require_once('template/header.php');

mysqli_query($koneksi,"TRUNCATE TABLE hasil;");

$kriterias = mysqli_query($koneksi,"SELECT * FROM kriteria ORDER BY kode_kriteria ASC");            
$alternatifs = mysqli_query($koneksi,"SELECT * FROM alternatif");

// Matrix Keputusan (X)
$matriks_x = array();
foreach($kriterias as $kriteria):
    foreach($alternatifs as $alternatif):
        
        $id_alternatif = $alternatif['id_alternatif'];
        $id_kriteria = $kriteria['id_kriteria'];
        
        if($kriteria['ada_pilihan']==1){
            $q4 = mysqli_query($koneksi,"SELECT sub_kriteria.nilai FROM penilaian JOIN sub_kriteria WHERE penilaian.nilai=sub_kriteria.id_sub_kriteria AND penilaian.id_alternatif='$alternatif[id_alternatif]' AND penilaian.id_kriteria='$kriteria[id_kriteria]'");
            $data = mysqli_fetch_array($q4);
            $nilai = isset($data['nilai']) ? $data['nilai'] : 0;
        }else{
            $q4 = mysqli_query($koneksi,"SELECT nilai FROM penilaian WHERE id_alternatif='$alternatif[id_alternatif]' AND id_kriteria='$kriteria[id_kriteria]'");
            $data = mysqli_fetch_array($q4);
            $nilai = isset($data['nilai']) ? $data['nilai'] : 0;
        }
        
        $matriks_x[$id_kriteria][$id_alternatif] = $nilai;
    endforeach;
endforeach;

// Matriks Ternormalisasi (R)
$matriks_r = array();
foreach($matriks_x as $id_kriteria => $penilaians):
    
    $jumlah_kuadrat = 0;
    foreach($penilaians as $penilaian):
        $jumlah_kuadrat += pow($penilaian, 2);
    endforeach;
    $akar_kuadrat = sqrt($jumlah_kuadrat);
    
    foreach($penilaians as $id_alternatif => $penilaian):
        $matriks_r[$id_kriteria][$id_alternatif] = ($akar_kuadrat != 0) ? ($penilaian / $akar_kuadrat) : 0;
    endforeach;
    
endforeach;

// Matriks Normalisasi Terbobot
$matriks_rb = array();
foreach($kriterias as $kriteria):
    foreach($alternatifs as $alternatif):
        
        $bobot = $kriteria['bobot'];
        $id_alternatif = $alternatif['id_alternatif'];
        $id_kriteria = $kriteria['id_kriteria'];
        
        $nilai_r = $matriks_r[$id_kriteria][$id_alternatif];
        $matriks_rb[$id_kriteria][$id_alternatif] = $bobot * $nilai_r;

    endforeach;
endforeach;

// Nilai Yi
$nilai_y_max = array();
$nilai_y_min = array();
foreach($alternatifs as $alternatif):
    $total_max = 0;
    $total_min = 0;
    foreach($kriterias as $kriteria):

        $id_kriteria = $kriteria['id_kriteria'];
        $id_alternatif = $alternatif['id_alternatif'];
        $type_kriteria = $kriteria['type'];
        
        $nilai_rb = $matriks_rb[$id_kriteria][$id_alternatif];
        
        if($type_kriteria == 'Benefit'):
            $total_max += $nilai_rb;
        elseif($type_kriteria == 'Cost'):
            $total_min += $nilai_rb;
        endif;
    endforeach;
    $nilai_y_max[$id_kriteria][$id_alternatif] = $total_max;
    $nilai_y_min[$id_kriteria][$id_alternatif] = $total_min;
endforeach;
?>

<!-- Custom CSS Tema Magenta & Style Modern 3D -->
<style>
    :root {
        --primary-magenta: #d63384;
        --dark-magenta: #b83280;
        --soft-magenta-bg: #fdf0f7;
        --shadow-3d: 0 10px 25px -5px rgba(184, 50, 128, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    /* Tab Custom Styling */
    .nav-tabs-magenta {
        border-bottom: 2px solid #f1d2e7;
    }
    .nav-tabs-magenta .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 600;
        padding: 10px 18px;
        border-radius: 10px 10px 0 0;
        transition: all 0.2s ease;
        margin-right: 4px;
    }
    .nav-tabs-magenta .nav-link:hover {
        color: var(--dark-magenta);
        background-color: var(--soft-magenta-bg);
    }
    .nav-tabs-magenta .nav-link.active {
        color: #ffffff !important;
        background: linear-gradient(135deg, #e84393, #b83280) !important;
        box-shadow: 0 4px 12px rgba(184, 50, 128, 0.25);
    }

    /* Container Card 3D */
    .card-perhitungan-3d {
        border-radius: 0 0 1.25rem 1.25rem;
        border: none;
        background: #ffffff;
        box-shadow: var(--shadow-3d);
        overflow: hidden;
    }

    /* Table Magenta Header & Spacing */
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

    /* Badges Style */
    .badge-alternatif-3d {
        background: linear-gradient(135deg, #f8f0fc, #fce4ec);
        color: var(--dark-magenta);
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 8px;
        display: inline-block;
    }
    .badge-type-3d {
        background-color: #ffffff;
        color: var(--dark-magenta);
        font-weight: 700;
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 6px;
        margin-left: 4px;
    }
</style>

<!-- Header Halaman -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-fw fa-calculator mr-2" style="color: var(--primary-magenta);"></i>Data Perhitungan
    </h1>
</div>

<!-- Navigasi Tab -->
<ul class="nav nav-tabs nav-tabs-magenta" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#tab-1"><i class="fa fa-table mr-1"></i> Keputusan (X)</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#tab-2"><i class="fa fa-table mr-1"></i> Preferensi (W)</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#tab-3"><i class="fa fa-table mr-1"></i> Ternormalisasi (R)</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#tab-4"><i class="fa fa-table mr-1"></i> Normalisasi Terbobot</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#tab-5"><i class="fa fa-table mr-1"></i> Nilai Yi</a>
    </li>
</ul>

<!-- Tab Contents -->
<div class="tab-content">

    <!-- TAB 1: Matriks Keputusan (X) -->
    <div id="tab-1" class="tab-pane active"><br>
        <div class="card card-perhitungan-3d mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);">
                    <i class="fa fa-table mr-2"></i>Matrix Keputusan (X)
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-magenta m-0" id="dataTable1" width="100%" cellspacing="0">
                        <thead>
                            <tr align="center">
                                <th width="5%">No</th>
                                <th>Nama Alternatif</th>
                                <?php foreach ($kriterias as $kriteria): ?>
                                    <th><?= $kriteria['kode_kriteria'] ?></th>
                                <?php endforeach ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($alternatifs as $alternatif): ?>
                            <tr align="center">
                                <td class="font-weight-bold"><?= $no; ?></td>
                                <td align="left">
                                    <span class="badge-alternatif-3d"><?= $alternatif['alternatif'] ?></span>
                                </td>
                                <?php
                                foreach ($kriterias as $kriteria):
                                    $id_alternatif = $alternatif['id_alternatif'];
                                    $id_kriteria = $kriteria['id_kriteria'];
                                    echo '<td class="font-weight-bold text-gray-800">';
                                    echo $matriks_x[$id_kriteria][$id_alternatif];
                                    echo '</td>';
                                endforeach;
                                ?>
                            </tr>
                            <?php
                            $no++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Bobot Preferensi (W) -->
    <div id="tab-2" class="tab-pane fade"><br>  
        <div class="card card-perhitungan-3d mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);">
                    <i class="fa fa-table mr-2"></i>Bobot Preferensi (W)
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-magenta m-0" id="dataTable2" width="100%" cellspacing="0">
                        <thead>
                            <tr align="center">
                                <?php foreach ($kriterias as $kriteria): ?>
                                    <th>
                                        <?= $kriteria['kode_kriteria'] ?> 
                                        <span class="badge-type-3d"><?= $kriteria['type'] ?></span>
                                    </th>
                                <?php endforeach ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr align="center">
                                <?php foreach ($kriterias as $kriteria): ?>
                                    <td class="font-weight-bold text-gray-800">
                                        <?= $kriteria['bobot']; ?>
                                    </td>
                                <?php endforeach ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- TAB 3: Matriks Ternormalisasi (R) -->
    <div id="tab-3" class="tab-pane fade"><br>
        <div class="card card-perhitungan-3d mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);">
                    <i class="fa fa-table mr-2"></i>Matriks Ternormalisasi (R)
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-magenta m-0" id="dataTable3" width="100%" cellspacing="0">
                        <thead>
                            <tr align="center">
                                <th width="5%">No</th>
                                <th>Nama Alternatif</th>
                                <?php foreach ($kriterias as $kriteria): ?>
                                    <th><?= $kriteria['kode_kriteria'] ?></th>
                                <?php endforeach ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($alternatifs as $alternatif): ?>
                            <tr align="center">
                                <td class="font-weight-bold"><?= $no; ?></td>
                                <td align="left">
                                    <span class="badge-alternatif-3d"><?= $alternatif['alternatif'] ?></span>
                                </td>
                                <?php                       
                                foreach($kriterias as $kriteria):
                                    $id_alternatif = $alternatif['id_alternatif'];
                                    $id_kriteria = $kriteria['id_kriteria'];
                                    echo '<td>';
                                    echo round($matriks_r[$id_kriteria][$id_alternatif], 4);
                                    echo '</td>';
                                endforeach;
                                ?>
                            </tr>
                            <?php
                            $no++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 4: Matriks Normalisasi Terbobot -->
    <div id="tab-4" class="tab-pane fade"><br>
        <div class="card card-perhitungan-3d mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);">
                    <i class="fa fa-table mr-2"></i>Matriks Normalisasi Terbobot
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-magenta m-0" id="dataTable4" width="100%" cellspacing="0">
                        <thead>
                            <tr align="center">
                                <th width="5%">No</th>
                                <th>Nama Alternatif</th>
                                <?php foreach ($kriterias as $kriteria): ?>
                                    <th><?= $kriteria['kode_kriteria'] ?></th>
                                <?php endforeach ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($alternatifs as $alternatif): ?>
                            <tr align="center">
                                <td class="font-weight-bold"><?= $no; ?></td>
                                <td align="left">
                                    <span class="badge-alternatif-3d"><?= $alternatif['alternatif'] ?></span>
                                </td>
                                <?php                       
                                foreach($kriterias as $kriteria):
                                    $id_alternatif = $alternatif['id_alternatif'];
                                    $id_kriteria = $kriteria['id_kriteria'];
                                    echo '<td>';
                                    echo round($matriks_rb[$id_kriteria][$id_alternatif], 4);
                                    echo '</td>';
                                endforeach;
                                ?>
                            </tr>
                            <?php
                            $no++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 5: Menghitung Nilai Yi -->
    <div id="tab-5" class="tab-pane fade"><br>
        <div class="card card-perhitungan-3d mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 font-weight-bold" style="color: var(--dark-magenta);">
                    <i class="fa fa-table mr-2"></i>Menghitung Nilai Yi
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-magenta m-0" id="dataTable5" width="100%" cellspacing="0">
                        <thead>
                            <tr align="center">
                                <th width="5%">No</th>
                                <th>Nama Alternatif</th>
                                <th>Maximum (
                                    <?php foreach ($kriterias as $kriteria):
                                        if ($kriteria['type']=="Benefit"){
                                            echo $kriteria['kode_kriteria']." ";
                                        }
                                    endforeach; ?>)
                                </th>
                                <th>Minimum (
                                    <?php foreach ($kriterias as $kriteria):
                                        if ($kriteria['type']=="Cost"){
                                            echo $kriteria['kode_kriteria']." ";
                                        }
                                    endforeach; ?>)
                                </th>
                                <th>Yi = Max - Min</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($alternatifs as $alternatif): ?>
                            <tr align="center">
                                <td class="font-weight-bold"><?= $no; ?></td>
                                <td align="left">
                                    <span class="badge-alternatif-3d"><?= $alternatif['alternatif'] ?></span>
                                </td>
                                <?php           
                                $total_max = 0;
                                $total_min = 0;
                                foreach($kriterias as $kriteria):
                                    $id_alternatif = $alternatif['id_alternatif'];
                                    $id_kriteria = $kriteria['id_kriteria'];
                                    $nilai_rb = $matriks_rb[$id_kriteria][$id_alternatif];
                                    if ($kriteria['type']=="Benefit"){
                                        $total_max += $nilai_rb;
                                    }else{
                                        $total_min += $nilai_rb;
                                    }
                                endforeach;
                                ?>
                                <td><?= round($total_max, 4); ?></td>
                                <td><?= round($total_min, 4); ?></td>
                                <td class="font-weight-bold text-gray-800" style="background-color: var(--soft-magenta-bg);">
                                    <?= $hasil = $total_max - $total_min; ?>
                                </td>
                            </tr>
                            <?php
                            $no++;
                            mysqli_query($koneksi,"INSERT INTO hasil (id_hasil, id_alternatif, nilai) VALUES ('', '$alternatif[id_alternatif]', '$hasil')");
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
require_once('template/footer.php');
}
else {
    header('Location: login.php');
}
?>