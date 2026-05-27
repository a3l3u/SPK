<?php
/**
 * View: Kriteria/Index
 * Kelola kriteria untuk kasus tertentu
 * Variabel: $kasus, $kriteria_list, $total_bobot, $flash, $edit_kriteria
 */
$kid = $kasus['id'];
?>

<div class="page-header fade-in-up">
  <div class="d-flex align-items-center gap-2 mb-1">
    <a href="index.php?page=kasus&action=show&id=<?= $kid ?>" style="color:var(--text-muted);text-decoration:none;font-size:.875rem;">
      <i class="bi bi-arrow-left me-1"></i><?= htmlspecialchars(mb_strimwidth($kasus['nama'], 0, 35, '…')) ?>
    </a>
    <span style="color:var(--text-faint);">/</span>
    <span style="font-size:.875rem;color:var(--text-muted);">Kriteria</span>
  </div>
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="page-title">Kelola <span class="gradient-text">Kriteria</span></h1>
      <p class="page-subtitle">Tentukan kriteria, bobot kepentingan, dan tipe (benefit/cost)</p>
    </div>
    <div class="d-flex gap-2">
      <a href="index.php?page=alternatif&kasus_id=<?= $kid ?>" class="btn btn-ghost btn-sm">
        Lanjut: Alternatif <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
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
  <!-- Tabel Kriteria -->
  <div class="col-lg-7">
    <div class="glass-card fade-in-up" style="height:fit-content;">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h2 style="font-size:1rem;font-weight:700;margin:0;">
            <i class="bi bi-list-ul me-2" style="color:#818cf8;"></i>
            Daftar Kriteria <span class="badge-bobot ms-1"><?= count($kriteria_list) ?></span>
          </h2>
          <!-- Bobot sum indicator -->
          <?php if (!empty($kriteria_list)): ?>
          <span style="font-size:.78rem;color:var(--text-muted);">
            Total bobot: <strong style="color:<?= abs($total_bobot - 100) < 0.1 ? '#4ade80' : '#fbbf24' ?>;"><?= number_format($total_bobot, 1) ?></strong>
          </span>
          <?php endif; ?>
        </div>

        <?php if (empty($kriteria_list)): ?>
        <div class="empty-state py-4">
          <i class="bi bi-sliders2 d-block" style="font-size:2.5rem;"></i>
          <p style="color:var(--text-muted);font-size:.9rem;">Belum ada kriteria. Tambahkan di form sebelah →</p>
        </div>
        <?php else: ?>
        <div class="table-wrapper">
          <table class="table table-dark-custom mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama Kriteria</th>
                <th class="text-center">Bobot</th>
                <th class="text-center">Tipe</th>
                <th class="text-end">Aksi</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($kriteria_list as $i => $kr): ?>
              <tr>
                <td style="color:var(--text-faint);font-size:.85rem;"><?= $i + 1 ?></td>
                <td style="font-weight:600;"><?= htmlspecialchars($kr['nama']) ?></td>
                <td class="text-center"><span class="badge-bobot"><?= number_format((float)$kr['bobot'], 1) ?></span></td>
                <td class="text-center">
                  <?php if ($kr['tipe'] === 'benefit'): ?>
                    <span class="badge-benefit">↑ Benefit</span>
                  <?php else: ?>
                    <span class="badge-cost">↓ Cost</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <div class="d-flex gap-1 justify-content-end">
                    <a href="index.php?page=kriteria&kasus_id=<?= $kid ?>&edit_id=<?= $kr['id'] ?>"
                       class="btn btn-ghost btn-sm" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form id="del-krit-<?= $kr['id'] ?>" method="POST" action="index.php">
                      <input type="hidden" name="_page"   value="kriteria">
                      <input type="hidden" name="_action" value="delete">
                      <input type="hidden" name="id"      value="<?= $kr['id'] ?>">
                      <input type="hidden" name="kasus_id" value="<?= $kid ?>">
                    </form>
                    <button type="button" class="btn btn-danger-soft btn-sm"
                            onclick="confirmDeleteSimple('del-krit-<?= $kr['id'] ?>', '<?= addslashes(htmlspecialchars($kr['nama'])) ?>')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Normalisasi info -->
        <?php if (!empty($kriteria_list)): ?>
        <div class="mt-3 p-3" style="background:rgba(99,102,241,.07);border-radius:8px;font-size:.78rem;color:var(--text-muted);">
          <i class="bi bi-info-circle me-1" style="color:#818cf8;"></i>
          Sistem akan normalisasi bobot secara otomatis sehingga Σw = 1.
          <?php if (abs($total_bobot - 100) > 0.1): ?>
          Saat ini total = <strong><?= number_format($total_bobot, 1) ?></strong> (opsional: ubah agar = 100).
          <?php else: ?>
          Total bobot = <strong style="color:#4ade80;">100 ✓</strong>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
      </div>
    </div>

    <!-- Nav Step -->
    <div class="d-flex gap-2 mt-3 fade-in-up">
      <a href="index.php?page=kasus&action=show&id=<?= $kid ?>" class="btn btn-ghost btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
      </a>
      <a href="index.php?page=alternatif&kasus_id=<?= $kid ?>" class="btn btn-gradient btn-sm flex-grow-1">
        Lanjut ke Alternatif <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>

  <!-- Form Tambah / Edit -->
  <div class="col-lg-5">
    <div class="glass-card fade-in-up" style="position:sticky;top:2rem;">
      <div class="card-body">
        <?php $isEditForm = !empty($edit_kriteria); ?>
        <h3 style="font-size:.95rem;font-weight:700;margin-bottom:1.25rem;">
          <i class="bi bi-<?= $isEditForm ? 'pencil-square' : 'plus-circle' ?> me-2" style="color:#818cf8;"></i>
          <?= $isEditForm ? 'Edit Kriteria' : 'Tambah Kriteria' ?>
        </h3>

        <form method="POST" action="index.php">
          <input type="hidden" name="_page"    value="kriteria">
          <input type="hidden" name="_action"  value="<?= $isEditForm ? 'update' : 'store' ?>">
          <input type="hidden" name="kasus_id" value="<?= $kid ?>">
          <?php if ($isEditForm): ?>
          <input type="hidden" name="id" value="<?= $edit_kriteria['id'] ?>">
          <?php endif; ?>

          <!-- Nama -->
          <div class="mb-3">
            <label for="nama_krit" class="form-label-dark">Nama Kriteria <span style="color:#ef4444;">*</span></label>
            <input type="text"
                   id="nama_krit"
                   name="nama"
                   class="form-control form-control-dark mb-2"
                   placeholder="cth: Kualitas WiFi"
                   value="<?= htmlspecialchars($edit_kriteria['nama'] ?? '') ?>"
                   required maxlength="255">
            <!-- Preset chips -->
            <div class="d-flex flex-wrap gap-1">
              <?php
              $presets_krit = ['📶 WiFi','💰 Harga','🛋 Kenyamanan','🔊 Kebisingan','📍 Jarak','🔌 Colokan','⏰ Jam Buka','❄️ AC'];
              foreach ($presets_krit as $pk): ?>
              <span class="chip-btn" style="font-size:.72rem;padding:.15rem .55rem;"
                    data-target="nama_krit"
                    data-value="<?= htmlspecialchars(trim(preg_replace('/[^\w\s]/u','', $pk))) ?>">
                <?= $pk ?>
              </span>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Bobot -->
          <div class="mb-3">
            <label for="bobot" class="form-label-dark">
              Bobot Kepentingan <span style="color:var(--text-faint);font-weight:400;">(angka bebas, akan dinormalisasi)</span>
            </label>
            <input type="number"
                   id="bobot"
                   name="bobot"
                   class="form-control form-control-dark bobot-input"
                   placeholder="cth: 30"
                   value="<?= htmlspecialchars((string)($edit_kriteria['bobot'] ?? '')) ?>"
                   min="0.1" max="9999" step="0.1"
                   required>
          </div>

          <!-- Total bobot indicator -->
          <?php if (!empty($kriteria_list)): ?>
          <div id="bobotSumBox" class="alert-glass alert-glass-info mb-3">
            Total Bobot: <strong><?= number_format($total_bobot, 1) ?></strong>
          </div>
          <?php endif; ?>

          <!-- Tipe -->
          <div class="mb-4">
            <label class="form-label-dark">Tipe Kriteria</label>
            <div class="tipe-toggle">
              <input type="radio" id="tipe_benefit" name="tipe" value="benefit"
                     <?= (!$isEditForm || $edit_kriteria['tipe'] === 'benefit') ? 'checked' : '' ?>>
              <label for="tipe_benefit">↑ Benefit<br><small style="font-weight:400;font-size:.7rem;opacity:.7;">Nilai tinggi = lebih baik</small></label>

              <input type="radio" id="tipe_cost" name="tipe" value="cost"
                     <?= ($isEditForm && $edit_kriteria['tipe'] === 'cost') ? 'checked' : '' ?>>
              <label for="tipe_cost">↓ Cost<br><small style="font-weight:400;font-size:.7rem;opacity:.7;">Nilai rendah = lebih baik</small></label>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-gradient flex-grow-1">
              <i class="bi bi-<?= $isEditForm ? 'check-lg' : 'plus-lg' ?> me-1"></i>
              <?= $isEditForm ? 'Simpan Perubahan' : 'Tambah Kriteria' ?>
            </button>
            <?php if ($isEditForm): ?>
            <a href="index.php?page=kriteria&kasus_id=<?= $kid ?>" class="btn btn-ghost">Batal</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
