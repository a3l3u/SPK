<?php
/**
 * View: Penilaian/Index
 * Matriks input penilaian alternatif × kriteria (skala 1-10)
 * Variabel: $kasus, $kriteria_list, $alternatif_list, $matrix, $error, $flash
 */
$kid = $kasus['id'];
?>

<div class="page-header fade-in-up">
  <div class="d-flex align-items-center gap-2 mb-1">
    <a href="index.php?page=alternatif&kasus_id=<?= $kid ?>" style="color:var(--text-muted);text-decoration:none;font-size:.875rem;">
      <i class="bi bi-arrow-left me-1"></i>Alternatif
    </a>
    <span style="color:var(--text-faint);">/</span>
    <span style="font-size:.875rem;color:var(--text-muted);">Penilaian</span>
  </div>
  <h1 class="page-title">Matriks <span class="gradient-text">Penilaian</span></h1>
  <p class="page-subtitle">Beri nilai 1–10 untuk setiap tempat pada setiap kriteria, boleh desimal seperti 1,5 (gunakan ↑↓←→ untuk navigasi)</p>
</div>

<?php if (isset($flash) && $flash): ?>
<div class="alert-glass alert-glass-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible mb-4 fade-in-up">
  <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
  <?= htmlspecialchars($flash['message']) ?>
  <button type="button" class="btn-close btn-close-white opacity-50 float-end" onclick="this.closest('.alert-glass').remove()"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert-glass alert-glass-warning mb-4 fade-in-up">
  <i class="bi bi-exclamation-triangle me-2"></i>
  <?= htmlspecialchars($error) ?>
  <div class="mt-2 d-flex gap-2">
    <?php if (count($kriteria_list) < 2): ?>
    <a href="index.php?page=kriteria&kasus_id=<?= $kid ?>" class="btn btn-gradient btn-sm">Tambah Kriteria</a>
    <?php endif; ?>
    <?php if (count($alternatif_list) < 2): ?>
    <a href="index.php?page=alternatif&kasus_id=<?= $kid ?>" class="btn btn-gradient btn-sm">Tambah Alternatif</a>
    <?php endif; ?>
  </div>
</div>
<?php else: ?>

<!-- Scale legend -->
<div class="row g-3 mb-4 fade-in-up">
  <div class="col-lg-8">
    <div class="glass-card">
      <div class="card-body" style="padding:1rem 1.25rem;">
        <div class="d-flex align-items-center gap-3 flex-wrap">
          <span style="font-size:.8rem;font-weight:600;color:var(--text-muted);">Skala Nilai:</span>
          <?php
          $scale_colors = ['#ef4444','#f97316','#f59e0b','#eab308','#84cc16','#22c55e','#10b981','#06b6d4','#6366f1','#8b5cf6'];
          for ($v = 1; $v <= 10; $v++): ?>
          <div style="text-align:center;">
            <div style="width:28px;height:28px;border-radius:6px;background:<?= $scale_colors[$v-1] ?>22;border:1px solid <?= $scale_colors[$v-1] ?>55;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;color:<?= $scale_colors[$v-1] ?>;"><?= $v ?></div>
          </div>
          <?php endfor; ?>
          <span style="font-size:.75rem;color:var(--text-faint);">1 = Sangat Buruk &nbsp;|&nbsp; 10 = Sangat Baik</span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="glass-card h-100">
      <div class="card-body" style="padding:1rem 1.25rem;display:flex;align-items:center;gap:1rem;">
        <div>
          <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;">Quick fill:</div>
          <div class="d-flex gap-1">
            <?php foreach ([5, 7, 8] as $qv): ?>
            <button type="button" class="chip-btn" onclick="fillAllMatrix(<?= $qv ?>)" style="font-size:.75rem;">
              Semua = <?= $qv ?>
            </button>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Kriteria cost info -->
<?php $has_cost = array_filter($kriteria_list, fn($k) => $k['tipe'] === 'cost');
if (!empty($has_cost)): ?>
<div class="alert-glass alert-glass-info mb-4 fade-in-up" style="font-size:.82rem;">
  <i class="bi bi-info-circle me-2"></i>
  Kriteria bertipe <strong>↓ Cost</strong>
  (<?= implode(', ', array_map(fn($k) => htmlspecialchars($k['nama']), $has_cost)) ?>):
  nilai tinggi berarti <em>buruk</em> (misal kebisingan tinggi = tidak diinginkan). Sistem TOPSIS menangani ini secara otomatis.
</div>
<?php endif; ?>

<!-- Matriks Form -->
<div class="glass-card fade-in-up">
  <div class="card-body">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 style="font-size:1rem;font-weight:700;margin:0;">
        <i class="bi bi-table me-2" style="color:#fbbf24;"></i>
        Matriks <?= count($alternatif_list) ?> × <?= count($kriteria_list) ?>
        <span style="font-size:.75rem;font-weight:400;color:var(--text-muted);margin-left:.5rem;">
          (<?= count($alternatif_list) ?> alternatif × <?= count($kriteria_list) ?> kriteria)
        </span>
      </h2>
    </div>

    <form method="POST" action="index.php" id="penilaianForm">
      <input type="hidden" name="_page"    value="penilaian">
      <input type="hidden" name="_action"  value="store">
      <input type="hidden" name="kasus_id" value="<?= $kid ?>">

      <div class="matrix-scroll">
        <table class="table table-dark-custom" style="min-width: <?= 200 + count($kriteria_list) * 90 ?>px;">
          <thead>
            <tr>
              <th style="min-width:160px;position:sticky;left:0;background:#1a1a2e;z-index:1;">
                Alternatif / Kriteria
              </th>
              <?php foreach ($kriteria_list as $kr): ?>
              <th class="text-center">
                <div><?= htmlspecialchars($kr['nama']) ?></div>
                <div class="mt-1">
                  <?php if ($kr['tipe'] === 'benefit'): ?>
                    <span class="badge-benefit" style="font-size:.65rem;">↑ B</span>
                  <?php else: ?>
                    <span class="badge-cost" style="font-size:.65rem;">↓ C</span>
                  <?php endif; ?>
                  <span class="badge-bobot ms-1" style="font-size:.65rem;"><?= number_format((float)$kr['bobot'], 0) ?></span>
                </div>
              </th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($alternatif_list as $alt): ?>
            <tr>
              <td class="alt-name-col" style="position:sticky;left:0;background:#141422;z-index:1;">
                <div style="font-weight:600;color:#ffffff;"><?= htmlspecialchars($alt['nama']) ?></div>
                <?php if ($alt['alamat']): ?>
                <div style="font-size:.72rem;color:var(--text-muted);"><?= htmlspecialchars(mb_strimwidth($alt['alamat'], 0, 30, '…')) ?></div>
                <?php endif; ?>
              </td>
              <?php foreach ($kriteria_list as $kr):
                $val = $matrix[$alt['id']][$kr['id']] ?? '';
              ?>
              <td class="text-center">
                <input type="number"
                       class="matrix-input"
                       name="nilai_<?= $alt['id'] ?>_<?= $kr['id'] ?>"
                       id="cell_<?= $alt['id'] ?>_<?= $kr['id'] ?>"
                       value="<?= htmlspecialchars((string)$val) ?>"
                       min="1" max="10" step="0.1"
                       placeholder="—"
                       style="color:#111111 !important; background:#ffffff !important; -webkit-text-fill-color:#111111 !important;"
                       required>
              </td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Actions -->
      <div class="d-flex gap-2 mt-4 flex-wrap">
        <a href="index.php?page=alternatif&kasus_id=<?= $kid ?>" class="btn btn-ghost">
          <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <button type="submit" class="btn btn-gradient flex-grow-1" style="max-width:280px;">
          <i class="bi bi-calculator me-2"></i>Simpan & Hitung TOPSIS
        </button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>
