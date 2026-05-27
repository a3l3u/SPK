<?php
/**
 * View: Dashboard
 * Halaman utama — statistik + kartu kasus
 */
?>

<!-- Page Header -->
<div class="page-header fade-in-up">
  <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="page-title">
        Selamat Datang di <span class="gradient-text">Smart DSS TOPSIS</span>
      </h1>
      <p class="page-subtitle">Smart Decision Support System Using TOPSIS — pilih alternatif terbaik secara objektif &amp; ilmiah</p>
    </div>
    <a href="index.php?page=kasus&action=create" class="btn btn-gradient">
      <i class="bi bi-plus-lg me-1"></i> Buat Kasus Baru
    </a>
  </div>
</div>

<!-- Flash Message -->
<?php if ($flash): ?>
<div class="alert-glass alert-glass-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible mb-4 fade-in-up" role="alert">
  <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
  <?= htmlspecialchars($flash['message']) ?>
  <button type="button" class="btn-close btn-close-white opacity-50 float-end" onclick="this.closest('.alert-glass').remove()"></button>
</div>
<?php endif; ?>

<!-- Stats Row -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3 fade-in-up delay-1">
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(99,102,241,.2);">
        <i class="bi bi-folder2-open" style="color:#818cf8;"></i>
      </div>
      <div class="stat-value gradient-text"><?= (int)$stats['total_kasus'] ?></div>
      <div class="stat-label">Total Kasus</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 fade-in-up delay-2">
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(139,92,246,.2);">
        <i class="bi bi-sliders2" style="color:#a78bfa;"></i>
      </div>
      <div class="stat-value" style="color:#a78bfa;"><?= (int)$stats['total_kriteria'] ?></div>
      <div class="stat-label">Total Kriteria</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 fade-in-up delay-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(34,197,94,.2);">
        <i class="bi bi-geo-alt" style="color:#4ade80;"></i>
      </div>
      <div class="stat-value" style="color:#4ade80;"><?= (int)$stats['total_alternatif'] ?></div>
      <div class="stat-label">Total Alternatif</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 fade-in-up delay-4">
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(251,191,36,.2);">
        <i class="bi bi-table" style="color:#fbbf24;"></i>
      </div>
      <div class="stat-value" style="color:#fbbf24;"><?= (int)$stats['total_penilaian'] ?></div>
      <div class="stat-label">Total Penilaian</div>
    </div>
  </div>
</div>

<!-- Kasus Grid -->
<div class="d-flex align-items-center justify-content-between mb-3 fade-in-up">
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--text);">
    <i class="bi bi-lightning-fill me-2" style="color:#818cf8;"></i>Kasus Aktif
  </h2>
  <a href="index.php?page=kasus" class="btn btn-ghost btn-sm">Lihat Semua</a>
</div>

<?php if (empty($kasus_list)): ?>
<!-- Empty State -->
<div class="glass-card fade-in-up">
  <div class="card-body empty-state">
    <i class="bi bi-folder-plus d-block"></i>
    <p class="mb-1" style="color:var(--text-muted);font-size:1.05rem;">Belum ada kasus.</p>
    <p class="mb-3" style="color:var(--text-faint);font-size:.875rem;">Buat kasus pertama Anda untuk mulai menggunakan Smart DSS TOPSIS.</p>
    <a href="index.php?page=kasus&action=create" class="btn btn-gradient">
      <i class="bi bi-plus-lg me-1"></i> Buat Kasus Pertama
    </a>
  </div>
</div>
<?php else: ?>

<div class="row g-3">
<?php foreach ($kasus_list as $idx => $k):
  $prog = getKasusProgress((int)$k['id']);
  $done_count = ($prog['kriteria'] ? 1:0) + ($prog['alternatif'] ? 1:0) + ($prog['penilaian'] ? 1:0);
  $pct = round($done_count / 3 * 100);
?>
  <div class="col-md-6 col-xl-4 fade-in-up" style="animation-delay: <?= $idx * .06 ?>s;">
    <div class="kasus-card">
      <div class="kasus-tipe">
        <i class="bi bi-tag me-1"></i><?= htmlspecialchars($k['tipe_tempat'] ?: 'Umum') ?>
      </div>
      <div class="kasus-title"><?= htmlspecialchars($k['nama']) ?></div>
      <div class="kasus-desc text-truncate-2">
        <?= htmlspecialchars($k['deskripsi'] ?: 'Tidak ada deskripsi.') ?>
      </div>

      <div class="kasus-meta">
        <span class="kasus-meta-item"><i class="bi bi-sliders2"></i> <?= (int)$k['jumlah_kriteria'] ?> Kriteria</span>
        <span class="kasus-meta-item"><i class="bi bi-geo-alt"></i> <?= (int)$k['jumlah_alternatif'] ?> Alternatif</span>
      </div>

      <!-- Progress bar -->
      <div class="d-flex align-items-center gap-2 mb-3">
        <div class="progress-bar-custom flex-grow-1">
          <div class="progress-fill" data-width="<?= $pct ?>"></div>
        </div>
        <span style="font-size:.75rem;color:var(--text-muted);flex-shrink:0;"><?= $pct ?>%</span>
      </div>

      <div class="d-flex gap-2 mt-auto">
        <?php if ($prog['penilaian']): ?>
          <a href="index.php?page=hasil&kasus_id=<?= $k['id'] ?>" class="btn btn-gradient btn-sm flex-grow-1">
            <i class="bi bi-trophy me-1"></i> Lihat Hasil
          </a>
        <?php else: ?>
          <a href="index.php?page=kasus&action=show&id=<?= $k['id'] ?>" class="btn btn-gradient btn-sm flex-grow-1">
            <i class="bi bi-arrow-right me-1"></i> Lanjutkan
          </a>
        <?php endif; ?>
        <a href="index.php?page=kasus&action=edit&id=<?= $k['id'] ?>" class="btn btn-ghost btn-sm">
          <i class="bi bi-pencil"></i>
        </a>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>

<?php endif; ?>

<!-- How it Works -->
<div class="mt-5 fade-in-up">
  <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:1rem;color:var(--text-muted);">
    <i class="bi bi-info-circle me-2"></i>Cara Kerja TOPSIS
  </h2>
  <div class="row g-2">
    <?php
    $steps = [
      ['bi-folder-plus',   '#818cf8', 'Buat Kasus',        'Tentukan nama & jenis tempat yang ingin dipilih'],
      ['bi-sliders2',      '#a78bfa', 'Tambah Kriteria',   'Tentukan kriteria + bobot + tipe (benefit/cost)'],
      ['bi-geo-alt',       '#4ade80', 'Tambah Alternatif', 'Masukkan daftar tempat yang jadi pilihan'],
      ['bi-table',         '#fbbf24', 'Isi Penilaian',     'Beri nilai 1–10 setiap alternatif per kriteria'],
      ['bi-trophy-fill',   '#f472b6', 'Lihat Ranking',     'Sistem hitung TOPSIS & tampilkan peringkat terbaik'],
    ];
    foreach ($steps as $i => $s): ?>
    <div class="col-sm-6 col-lg-4 col-xl-auto flex-xl-fill">
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:.875rem;display:flex;align-items:flex-start;gap:.75rem;">
        <div style="width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.07);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <i class="bi <?= $s[0] ?>" style="color:<?= $s[1] ?>;font-size:1rem;"></i>
        </div>
        <div>
          <div style="font-size:.8rem;font-weight:700;color:var(--text);margin-bottom:2px;"><?= $i+1 ?>. <?= $s[2] ?></div>
          <div style="font-size:.73rem;color:var(--text-muted);"><?= $s[3] ?></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
