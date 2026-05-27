<?php
/**
 * View: Kasus/Create + Edit
 * Form buat/edit kasus pemilihan
 * $kasus  = null (create) | array (edit)
 * $action = 'store' | 'update'
 */
$isEdit   = !empty($kasus);
$formId   = $kasus['id'] ?? null;
?>

<div class="page-header fade-in-up">
  <div class="d-flex align-items-center gap-2 mb-1">
    <a href="index.php?page=kasus" style="color:var(--text-muted);text-decoration:none;font-size:.875rem;">
      <i class="bi bi-arrow-left me-1"></i>Kasus
    </a>
    <span style="color:var(--text-faint);">/</span>
    <span style="font-size:.875rem;color:var(--text-muted);"><?= $isEdit ? 'Edit Kasus' : 'Buat Kasus Baru' ?></span>
  </div>
  <h1 class="page-title"><?= $isEdit ? 'Edit Kasus' : 'Buat <span class="gradient-text">Kasus Baru</span>' ?></h1>
  <p class="page-subtitle"><?= $isEdit ? 'Perbarui informasi kasus pemilihan.' : 'Tentukan konteks & nama kasus pemilihan tempat Anda.' ?></p>
</div>

<?php if (isset($flash) && $flash): ?>
<div class="alert-glass alert-glass-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible mb-4">
  <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">
    <div class="glass-card fade-in-up">
      <div class="card-body">
        <form method="POST" action="index.php">
          <input type="hidden" name="_page"   value="kasus">
          <input type="hidden" name="_action" value="<?= $isEdit ? 'update' : 'store' ?>">
          <?php if ($isEdit): ?>
          <input type="hidden" name="id" value="<?= $formId ?>">
          <?php endif; ?>

          <!-- Nama Kasus -->
          <div class="mb-4">
            <label for="nama" class="form-label-dark">
              <i class="bi bi-type me-1"></i>Nama Kasus <span style="color:#ef4444;">*</span>
            </label>
            <input type="text"
                   id="nama"
                   name="nama"
                   class="form-control form-control-dark"
                   placeholder="cth: Pilih Cafe untuk Nugas Semester 6"
                   value="<?= htmlspecialchars($kasus['nama'] ?? '') ?>"
                   required
                   maxlength="255">
          </div>

          <!-- Tipe Tempat -->
          <div class="mb-3">
            <label for="tipe_tempat" class="form-label-dark">
              <i class="bi bi-tag me-1"></i>Tipe Tempat
            </label>
            <input type="text"
                   id="tipe_tempat"
                   name="tipe_tempat"
                   class="form-control form-control-dark mb-2"
                   placeholder="cth: Cafe, Coworking Space, Perpustakaan..."
                   value="<?= htmlspecialchars($kasus['tipe_tempat'] ?? '') ?>"
                   maxlength="100">
            <!-- Preset chips -->
            <div class="d-flex flex-wrap gap-2">
              <?php
              $presets = ['☕ Cafe','💻 Coworking Space','📚 Perpustakaan','🏢 Kantor','🍕 Restoran','🏨 Hotel','🏪 Toko'];
              foreach ($presets as $p): ?>
              <span class="chip-btn"
                    data-target="tipe_tempat"
                    data-value="<?= htmlspecialchars(preg_replace('/[^\w\s\/]/u', '', $p)) ?>">
                <?= $p ?>
              </span>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Deskripsi -->
          <div class="mb-4">
            <label for="deskripsi" class="form-label-dark">
              <i class="bi bi-card-text me-1"></i>Deskripsi <span style="color:var(--text-faint);font-weight:400;">(opsional)</span>
            </label>
            <textarea id="deskripsi"
                      name="deskripsi"
                      class="form-control form-control-dark"
                      rows="3"
                      placeholder="Jelaskan tujuan kasus pemilihan ini..."
                      maxlength="1000"><?= htmlspecialchars($kasus['deskripsi'] ?? '') ?></textarea>
          </div>

          <!-- Info box -->
          <div class="alert-glass alert-glass-info mb-4" style="font-size:.82rem;">
            <i class="bi bi-lightbulb me-2"></i>
            Setelah membuat kasus, Anda akan diarahkan untuk menambahkan <strong>kriteria penilaian</strong>.
          </div>

          <!-- Actions -->
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-gradient flex-grow-1">
              <i class="bi bi-<?= $isEdit ? 'check-lg' : 'plus-lg' ?> me-1"></i>
              <?= $isEdit ? 'Simpan Perubahan' : 'Buat Kasus &amp; Lanjut →' ?>
            </button>
            <a href="index.php?page=kasus" class="btn btn-ghost">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
