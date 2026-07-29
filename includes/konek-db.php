<?php 
$koneksi = mysqli_connect("sql210.infinityfree.com", "if0_42506818", "Asysifa17", "if0_42506818_spk_moora");
 
// Check connection
if (mysqli_connect_errno()){
    echo "Koneksi database gagal : " . mysqli_connect_error();
}

?>