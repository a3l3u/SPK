<?php
/**
 * Layout: Header + Sidebar
 * Variabel yang diharapkan dari controller:
 *   $title       string  — judul halaman
 *   $active_page string  — untuk highlight nav aktif
 *   $kasus_id    int|null — jika dalam konteks kasus tertentu
 *   $kasus       array|null — data kasus aktif (untuk nama di sidebar)
 */
$title       = $title       ?? 'Smart DSS';
$active_page = $active_page ?? 'dashboard';
$kasus_id    = $kasus_id    ?? null;
$kasus_nav   = ($kasus_id && isset($kasus)) ? $kasus : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?> | Smart DSS TOPSIS</title>
  <meta name="description" content="Smart Decision Support System Using TOPSIS — sistem pendukung keputusan berbasis metode TOPSIS untuk memilih alternatif terbaik secara objektif & ilmiah">
  <meta name="theme-color" content="#0f0f1a">

  <!-- Preconnect fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link href="<?= rtrim(dirname($_SERVER['PHP_SELF']), '/') ?>/assets/css/style.css?v=<?= filemtime(BASE_PATH.'/assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="app-wrapper">

  <!-- ════════════════════════════════════════════════════════
       SIDEBAR
  ════════════════════════════════════════════════════════ -->
  <nav class="sidebar">

    <!-- Brand -->
    <a href="index.php" class="nav-brand">
      <div class="brand-icon">
        <img src="<?= rtrim(dirname($_SERVER['PHP_SELF']), '/') ?>/assets/logo.png" alt="Smart DSS Logo" class="brand-logo-img" width="44" height="44">
      </div>
      <div>
        <div class="brand-name">Smart DSS</div>
        <div class="brand-sub">Smart Decision Support System</div>
      </div>
    </a>

    <!-- Menu Utama -->
    <div class="nav-section-label">Menu Utama</div>
    <a href="index.php"
       class="nav-link-custom <?= $active_page === 'dashboard' ? 'active' : '' ?>">
      <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>
    <a href="index.php?page=kasus"
       class="nav-link-custom <?= in_array($active_page, ['kasus','kasus_show']) ? 'active' : '' ?>">
      <i class="bi bi-folder2-open"></i> Kasus Saya
    </a>
    <a href="index.php?page=kasus&action=create"
       class="nav-link-custom <?= $active_page === 'kasus_create' ? 'active' : '' ?>">
      <i class="bi bi-plus-circle"></i> Buat Kasus Baru
    </a>

    <?php if ($kasus_id): ?>
    <!-- Kasus Aktif -->
    <div class="divider" style="margin: .75rem 0;"></div>
    <div class="nav-section-label">Kasus Aktif</div>

    <?php if ($kasus_nav): ?>
    <div style="padding: .3rem .75rem .6rem; font-size:.8rem; color:#818cf8; font-weight:600; line-height:1.3;">
      <i class="bi bi-lightning-fill me-1"></i>
      <?= htmlspecialchars(mb_strimwidth($kasus_nav['nama'], 0, 28, '…')) ?>
    </div>
    <?php endif; ?>

    <a href="index.php?page=kasus&action=show&id=<?= $kasus_id ?>"
       class="nav-link-custom <?= $active_page === 'kasus_show' ? 'active' : '' ?>">
      <i class="bi bi-info-circle"></i> Ringkasan
    </a>
    <a href="index.php?page=kriteria&kasus_id=<?= $kasus_id ?>"
       class="nav-link-custom <?= $active_page === 'kriteria' ? 'active' : '' ?>">
      <i class="bi bi-sliders2"></i> Kriteria
    </a>
    <a href="index.php?page=alternatif&kasus_id=<?= $kasus_id ?>"
       class="nav-link-custom <?= $active_page === 'alternatif' ? 'active' : '' ?>">
      <i class="bi bi-geo-alt"></i> Alternatif
    </a>
    <a href="index.php?page=penilaian&kasus_id=<?= $kasus_id ?>"
       class="nav-link-custom <?= $active_page === 'penilaian' ? 'active' : '' ?>">
      <i class="bi bi-table"></i> Matriks Penilaian
    </a>
    <a href="index.php?page=hasil&kasus_id=<?= $kasus_id ?>"
       class="nav-link-custom <?= $active_page === 'hasil' ? 'active' : '' ?>">
      <i class="bi bi-trophy-fill"></i> Hasil Ranking
    </a>
    <?php endif; ?>

    <!-- Footer sidebar -->
    <div class="sidebar-footer">
      <div class="sidebar-info-box">
        <strong>Smart DSS TOPSIS</strong>
        Smart Decision Support System<br>Using TOPSIS Method
      </div>
    </div>
  </nav>

  <!-- ════════════════════════════════════════════════════════
       MAIN CONTENT WRAPPER
  ════════════════════════════════════════════════════════ -->
  <div class="main-content">
