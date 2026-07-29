</div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white shadow-sm mt-auto">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; <?= date('Y'); ?> <strong>SPK MOORA</strong> — Developed by <span style="color: #d81b60; font-weight: 700;">Asysifa Sandra Devi, S.Kom</span></span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>
  
  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content border-0 shadow">
        <div class="modal-header text-white" style="background: linear-gradient(135deg, #d81b60, #a8286a);">
          <h5 class="modal-title font-weight-bold" id="exampleModalLabel">
            <i class="fas fa-sign-out-alt mr-2"></i>Konfirmasi Logout
          </h5>
          <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body py-4 text-dark">
          Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem?
        </div>
        <div class="modal-footer bg-light">
          <button class="btn btn-secondary px-3" type="button" data-dismiss="modal">
            <i class="fas fa-fw fa-times mr-1"></i>Batal
          </button>
          <a class="btn text-white px-3" href="logout.php" style="background: linear-gradient(135deg, #d81b60, #a8286a);">
            <i class="fas fa-fw fa-sign-out-alt mr-1"></i>Logout
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="assets/js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="assets/vendor/chart.js/Chart.min.js"></script>
  
  <!-- Page level plugins -->
  <script src="assets/vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="assets/js/demo/datatables-demo.js"></script>
  
  <script>
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  });

  $('#dataTable1').DataTable({ "pageLength": 10 });
  $('#dataTable2').DataTable({ "pageLength": 10 });
  $('#dataTable3').DataTable({ "pageLength": 10 });
  $('#dataTable4').DataTable({ "pageLength": 10 });
  $('#dataTable5').DataTable({ "pageLength": 10 });
  </script>
</body>

</html>
<?php
if(isset($pdo)) {
  // Tutup Koneksi
  $pdo = null;
}
?>