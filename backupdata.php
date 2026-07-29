<?php
require_once('includes/init.php');
require_once('template/header.php');
?><div class="mb-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-database"></i> Backup Database</h1>
    </div>
 <div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info"><i class="fa fa-database"></i> Backup Database</h6>
    </div>

    <div class="card-body">  
       
		<button id="confirm" class="btn btn-primary kanan">BACKUP</button>
		<div class="notif"></div>
         </div>
	 </div>
	</div>
	  
         <script>
	$('#confirm').click(function() {
        $('.notif').load('backup.php');
        console.log('sukses');
    });
	 
</script>
	
                           