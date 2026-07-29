<?php 
require_once('includes/init.php'); 
$kategori = $_POST['kategori'];
     $data = mysqli_query($koneksi, "SELECT * FROM alternatif WHERE kategori='$kategori'");           
     echo "<option value=''>Pilih Produk</option>";
     while ($p = mysqli_fetch_array($data)) {
     echo "<option value='$p[id_alternatif]'>$p[nama]</option>";
    }
?>