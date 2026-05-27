  </div><!-- /.main-content -->
</div><!-- /.app-wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<!-- App JS -->
<script src="<?= rtrim(dirname($_SERVER['PHP_SELF']), '/') ?>/assets/js/app.js?v=<?= filemtime(BASE_PATH.'/assets/js/app.js') ?>"></script>
<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
