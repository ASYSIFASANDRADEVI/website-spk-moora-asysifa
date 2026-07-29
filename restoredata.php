
<?php
require_once('includes/init.php');
require_once('template/header.php');
?>
<div class="mb-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-database"></i> Restore Database</h1>
    </div>
 <div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info"><i class="fa fa-database"></i> Restore Database</h6>
    </div>

    <div class="card-body">  
        <form method="POST" enctype="multipart/form-data">
              <p>Klik Tombol dibawah ini untuk merestore database </p>
         <div class='col-md-12 mb-4'>
         <input type="file" name="sql_file" accept=".sql" required>
            </div>
				<div class='col-md-12 mb-4'>
                     <button type="submit" name="submit" class="btn btn-success">RESTORE</button>
             </div>
            </form>
		
         </div>
	 </div>
	</div>
	  
        
	<?php

require_once('includes/init.php');
// Fungsi restore
function restoreDatabase($koneksi, $filePath) {
    $templine = '';
    $lines = file($filePath);

    foreach ($lines as $line) {
        if (substr($line, 0, 2) == '--' || trim($line) == '')
            continue;

        $templine .= $line;

        if (substr(trim($line), -1, 1) == ';') {
            if (!$koneksi->query($templine)) {
               
            }
            $templine = '';
        }
    }

    echo "<div style='color:green;'>Restore database selesai!</div>";
}

if (isset($_POST['submit'])) {
    if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] == 0) {
        $uploadedFile = $_FILES['sql_file']['tmp_name'];

        $ext = pathinfo($_FILES['sql_file']['name'], PATHINFO_EXTENSION);
        if (strtolower($ext) != 'sql') {
            echo "<div style='color:red;'>File bukan .sql</div>";
        } else {
           
           
            if ($koneksi->connect_error) {
                die("Koneksi gagal: " . $koneksi->connect_error);
            }

            // Proses restore
            restoreDatabase($koneksi, $uploadedFile);
            $koneksi->close();
        }
    } else {
        echo "<div style='color:red;'>Gagal upload file.</div>";
    }
}
?>


