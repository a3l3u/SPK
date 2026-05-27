<?php
/**
 * View: Alternatif/Index
 * Kelola alternatif (tempat) per kasus
 * Variabel: $kasus, $alternatif_list, $flash, $edit_alternatif
 */
$kid = $kasus['id'];
?>

<div class="page-header fade-in-up">
  <div class="d-flex align-items-center gap-2 mb-1">
    <a href="index.php?page=kriteria&kasus_id=<?= $kid ?>" style="color:var(--text-muted);text-decoration:none;font-size:.875rem;">
      <i class="bi bi-arrow-left me-1"></i>Kriteria
    </a>
    <span style="color:var(--text-faint);">/</span>
    <span style="font-size:.875rem;color:var(--text-muted);">Alternatif</span>
  </div>
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="page-title">Kelola <span class="gradient-text">Alternatif</span></h1>
      <p class="page-subtitle">Tambahkan tempat-tempat yang menjadi kandidat pilihan</p>
    </div>
    <a href="index.php?page=penilaian&kasus_id=<?= $kid ?>" class="btn btn-ghost btn-sm">
      Lanjut: Penilaian <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>
</div>

<?php if ($flash): ?>
<div class="alert-glass alert-glass-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible mb-4 fade-in-up" role="alert">
  <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
  <?= htmlspecialchars($flash['message']) ?>
  <button type="button" class="btn-close btn-close-white opacity-50 float-end" onclick="this.closest('.alert-glass').remove()"></button>
</div>
<?php endif; ?>

<div class="row g-4">
  <!-- Tabel Alternatif -->
  <div class="col-lg-7">
    <div class="glass-card fade-in-up">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h2 style="font-size:1rem;font-weight:700;margin:0;">
            <i class="bi bi-geo-alt me-2" style="color:#4ade80;"></i>
            Daftar Tempat <span class="badge-bobot ms-1"><?= count($alternatif_list) ?></span>
          </h2>
        </div>

        <?php if (empty($alternatif_list)): ?>
        <div class="empty-state py-4">
          <i class="bi bi-geo-alt d-block" style="font-size:2.5rem;"></i>
          <p style="color:var(--text-muted);font-size:.9rem;">Belum ada alternatif. Tambahkan di form sebelah →</p>
        </div>
        <?php else: ?>
        <div class="table-wrapper">
          <table class="table table-dark-custom mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama Tempat</th>
                <th>Alamat</th>
                <th class="text-end">Aksi</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($alternatif_list as $i => $alt): ?>
              <tr>
                <td style="color:var(--text-faint);font-size:.85rem;"><?= $i + 1 ?></td>
                <td>
                  <div style="font-weight:600;"><?= htmlspecialchars($alt['nama']) ?></div>
                  <?php if ($alt['keterangan']): ?>
                  <div style="font-size:.75rem;color:var(--text-muted);">
                    <?= htmlspecialchars(mb_strimwidth($alt['keterangan'], 0, 55, '…')) ?>
                  </div>
                  <?php endif; ?>
                </td>
                <td style="font-size:.82rem;color:var(--text-muted);">
                  <?= htmlspecialchars(mb_strimwidth($alt['alamat'] ?: '—', 0, 40, '…')) ?>
                </td>
                <td class="text-end">
                  <div class="d-flex gap-1 justify-content-end">
                    <a href="index.php?page=alternatif&kasus_id=<?= $kid ?>&edit_id=<?= $alt['id'] ?>"
                       class="btn btn-ghost btn-sm" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form id="del-alt-<?= $alt['id'] ?>" method="POST" action="index.php">
                      <input type="hidden" name="_page"    value="alternatif">
                      <input type="hidden" name="_action"  value="delete">
                      <input type="hidden" name="id"       value="<?= $alt['id'] ?>">
                      <input type="hidden" name="kasus_id" value="<?= $kid ?>">
                    </form>
                    <button type="button" class="btn btn-danger-soft btn-sm"
                            onclick="confirmDeleteSimple('del-alt-<?= $alt['id'] ?>', '<?= addslashes(htmlspecialchars($alt['nama'])) ?>')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="d-flex gap-2 mt-3 fade-in-up">
      <a href="index.php?page=kriteria&kasus_id=<?= $kid ?>" class="btn btn-ghost btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kriteria
      </a>
      <a href="index.php?page=penilaian&kasus_id=<?= $kid ?>" class="btn btn-gradient btn-sm flex-grow-1">
        Lanjut ke Penilaian <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>

  <!-- Form -->
  <div class="col-lg-5">
    <div class="glass-card fade-in-up" style="position:sticky;top:2rem;">
      <div class="card-body">
        <?php $isEditForm = !empty($edit_alternatif); ?>
        <h3 style="font-size:.95rem;font-weight:700;margin-bottom:1.25rem;">
          <i class="bi bi-<?= $isEditForm ? 'pencil-square' : 'plus-circle' ?> me-2" style="color:#4ade80;"></i>
          <?= $isEditForm ? 'Edit Alternatif' : 'Tambah Alternatif' ?>
        </h3>

        <form method="POST" action="index.php">
          <input type="hidden" name="_page"    value="alternatif">
          <input type="hidden" name="_action"  value="<?= $isEditForm ? 'update' : 'store' ?>">
          <input type="hidden" name="kasus_id" value="<?= $kid ?>">
          <?php if ($isEditForm): ?>
          <input type="hidden" name="id" value="<?= $edit_alternatif['id'] ?>">
          <?php endif; ?>

          <div class="mb-3">
            <label for="nama_alt" class="form-label-dark">Nama Tempat <span style="color:#ef4444;">*</span></label>
            <input type="text"
                   id="nama_alt"
                   name="nama"
                   class="form-control form-control-dark"
                   placeholder="cth: Kopi Kenangan Sudirman"
                   value="<?= htmlspecialchars($edit_alternatif['nama'] ?? '') ?>"
                   required maxlength="255">
          </div>

          <div class="mb-3">
            <label for="alamat" class="form-label-dark">Alamat <span style="color:var(--text-faint);font-weight:400;">(opsional)</span></label>
            <input type="text"
                   id="alamat"
                   name="alamat"
                   class="form-control form-control-dark"
                   placeholder="cth: Jl. Sudirman No. 10, Jakarta"
                   value="<?= htmlspecialchars($edit_alternatif['alamat'] ?? '') ?>"
                   maxlength="500">
          </div>

          <div class="mb-4">
            <label for="keterangan" class="form-label-dark">Keterangan <span style="color:var(--text-faint);font-weight:400;">(opsional)</span></label>
            <textarea id="keterangan"
                      name="keterangan"
                      class="form-control form-control-dark"
                      rows="2"
                      placeholder="Catatan singkat tentang tempat ini..."
                      maxlength="500"><?= htmlspecialchars($edit_alternatif['keterangan'] ?? '') ?></textarea>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-gradient flex-grow-1">
              <i class="bi bi-<?= $isEditForm ? 'check-lg' : 'plus-lg' ?> me-1"></i>
              <?= $isEditForm ? 'Simpan Perubahan' : 'Tambah Tempat' ?>
            </button>
            <?php if ($isEditForm): ?>
            <a href="index.php?page=alternatif&kasus_id=<?= $kid ?>" class="btn btn-ghost">Batal</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
