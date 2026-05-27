<?php
/**
 * View: Kasus/Show
 * Detail kasus + progress 4 langkah + quick links
 */
$kid = $kasus['id'];
?>

<div class="page-header fade-in-up">
  <div class="d-flex align-items-center gap-2 mb-1">
    <a href="index.php?page=kasus" style="color:var(--text-muted);text-decoration:none;font-size:.875rem;">
      <i class="bi bi-arrow-left me-1"></i>Kasus
    </a>
    <span style="color:var(--text-faint);">/</span>
    <span style="font-size:.875rem;color:var(--text-muted);">Detail</span>
  </div>
  <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="page-title"><?= htmlspecialchars($kasus['nama']) ?></h1>
      <p class="page-subtitle">
        <span class="badge-bobot me-2"><i class="bi bi-tag me-1"></i><?= htmlspecialchars($kasus['tipe_tempat'] ?: 'Umum') ?></span>
        Dibuat: <?= date('d M Y, H:i', strtotime($kasus['created_at'])) ?>
      </p>
    </div>
    <div class="d-flex gap-2">
      <a href="index.php?page=kasus&action=edit&id=<?= $kid ?>" class="btn btn-ghost btn-sm">
        <i class="bi bi-pencil me-1"></i> Edit
      </a>
      <form id="del-kasus-<?= $kid ?>" method="POST" action="index.php" style="display:inline;">
        <input type="hidden" name="_page"   value="kasus">
        <input type="hidden" name="_action" value="delete">
        <input type="hidden" name="id"      value="<?= $kid ?>">
      </form>
      <button type="button" class="btn btn-danger-soft btn-sm"
              onclick="confirmDelete('del-kasus-<?= $kid ?>', '<?= addslashes(htmlspecialchars($kasus['nama'])) ?>')">
        <i class="bi bi-trash me-1"></i> Hapus
      </button>
    </div>
  </div>
</div>

<?php if ($flash): ?>
<div class="alert-glass alert-glass-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible mb-4 fade-in-up">
  <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
  <?= htmlspecialchars($flash['message']) ?>
  <button type="button" class="btn-close btn-close-white opacity-50 float-end" onclick="this.closest('.alert-glass').remove()"></button>
</div>
<?php endif; ?>

<!-- Deskripsi -->
<?php if ($kasus['deskripsi']): ?>
<div class="glass-card mb-4 fade-in-up">
  <div class="card-body" style="padding:1.25rem 1.5rem;">
    <p style="color:var(--text-muted);margin:0;font-size:.9rem;">
      <i class="bi bi-card-text me-2" style="color:#818cf8;"></i>
      <?= htmlspecialchars($kasus['deskripsi']) ?>
    </p>
  </div>
</div>
<?php endif; ?>

<!-- Progress Steps -->
<?php
$steps_data = [
  [
    'num'   => 1,
    'label' => 'Tambah Kriteria',
    'desc'  => $progress['jml_kriteria'] . ' kriteria ditambahkan (min. 2)',
    'done'  => $progress['kriteria'],
    'url'   => "index.php?page=kriteria&kasus_id=$kid",
    'icon'  => 'bi-sliders2',
    'hint'  => 'Tentukan kriteria + bobot + tipe benefit/cost',
  ],
  [
    'num'   => 2,
    'label' => 'Tambah Alternatif',
    'desc'  => $progress['jml_alternatif'] . ' alternatif ditambahkan (min. 2)',
    'done'  => $progress['alternatif'],
    'url'   => "index.php?page=alternatif&kasus_id=$kid",
    'icon'  => 'bi-geo-alt',
    'hint'  => 'Masukkan daftar tempat yang menjadi kandidat pilihan',
  ],
  [
    'num'   => 3,
    'label' => 'Isi Matriks Penilaian',
    'desc'  => $progress['jml_penilaian'] . ' / ' . $progress['total_required'] . ' nilai terisi',
    'done'  => $progress['penilaian'],
    'url'   => "index.php?page=penilaian&kasus_id=$kid",
    'icon'  => 'bi-table',
    'hint'  => 'Beri nilai 1–10 untuk setiap alternatif pada setiap kriteria',
  ],
  [
    'num'   => 4,
    'label' => 'Lihat Hasil Ranking',
    'desc'  => $progress['penilaian'] ? 'Hasil siap dihitung!' : 'Selesaikan langkah 1–3 terlebih dahulu',
    'done'  => $progress['penilaian'],
    'url'   => "index.php?page=hasil&kasus_id=$kid",
    'icon'  => 'bi-trophy-fill',
    'hint'  => 'Sistem akan hitung TOPSIS dan tampilkan ranking terbaik',
  ],
];

$done_count = array_sum(array_map(fn($s) => $s['done'] ? 1 : 0, $steps_data));
$pct        = round($done_count / count($steps_data) * 100);
?>

<div class="glass-card mb-4 fade-in-up">
  <div class="card-body">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 style="font-size:1rem;font-weight:700;margin:0;">Progress Kasus</h2>
      <span class="badge-bobot"><?= $done_count ?>/<?= count($steps_data) ?> Langkah</span>
    </div>
    <div class="progress-bar-custom mb-4">
      <div class="progress-fill" data-width="<?= $pct ?>"></div>
    </div>

    <div class="row g-3">
    <?php foreach ($steps_data as $s): ?>
      <div class="col-sm-6 col-xl-3">
        <a href="<?= $s['url'] ?>" class="step-card <?= $s['done'] ? 'done' : '' ?>" style="text-decoration:none;">
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="step-num <?= $s['done'] ? 'done' : '' ?>">
              <?= $s['done'] ? '<i class="bi bi-check2" style="font-size:.9rem;"></i>' : $s['num'] ?>
            </div>
            <i class="bi <?= $s['icon'] ?>" style="color:<?= $s['done'] ? '#4ade80' : '#818cf8' ?>;font-size:1.1rem;"></i>
          </div>
          <div style="font-weight:700;font-size:.9rem;margin-bottom:.25rem;color:var(--text);">
            <?= $s['label'] ?>
          </div>
          <div style="font-size:.78rem;color:var(--text-muted);margin-bottom:.5rem;"><?= $s['desc'] ?></div>
          <div style="font-size:.72rem;color:var(--text-faint);"><?= $s['hint'] ?></div>
        </a>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Quick Action: Hitung TOPSIS -->
<?php if ($progress['penilaian']): ?>
<div class="winner-card fade-in-up">
  <div>
    <h3 style="font-size:1.15rem;font-weight:800;margin-bottom:.4rem;">Data Lengkap! Siap Dihitung 🎉</h3>
    <p style="color:rgba(255,255,255,.6);font-size:.875rem;margin-bottom:1.25rem;">
      Semua kriteria, alternatif, dan penilaian sudah tersedia. Klik tombol di bawah untuk menjalankan kalkulasi TOPSIS.
    </p>
    <a href="index.php?page=hasil&kasus_id=<?= $kid ?>" class="btn btn-gradient winner-pulse">
      <i class="bi bi-calculator me-2"></i>Hitung TOPSIS & Lihat Ranking
    </a>
  </div>
</div>
<?php endif; ?>
