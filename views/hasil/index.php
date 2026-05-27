<?php
/**
 * View: Hasil/Index
 * Halaman hasil ranking TOPSIS — champion card, tabel, chart, detail kalkulasi
 * Variabel: $kasus, $topsis_result, $error, $flash
 */
$kid = $kasus['id'];
?>

<div class="page-header fade-in-up">
  <div class="d-flex align-items-center gap-2 mb-1">
    <a href="index.php?page=penilaian&kasus_id=<?= $kid ?>" style="color:var(--text-muted);text-decoration:none;font-size:.875rem;">
      <i class="bi bi-arrow-left me-1"></i>Penilaian
    </a>
    <span style="color:var(--text-faint);">/</span>
    <span style="font-size:.875rem;color:var(--text-muted);">Hasil</span>
  </div>
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="page-title">Hasil <span class="gradient-text">Ranking TOPSIS</span></h1>
      <p class="page-subtitle"><?= htmlspecialchars($kasus['nama']) ?></p>
    </div>
    <div class="d-flex gap-2">
      <a href="index.php?page=penilaian&kasus_id=<?= $kid ?>" class="btn btn-ghost btn-sm">
        <i class="bi bi-pencil me-1"></i>Ubah Penilaian
      </a>
      <button onclick="printHasil()" class="btn btn-ghost btn-sm">
        <i class="bi bi-file-earmark-pdf me-1" style="color:#f87171;"></i>Cetak PDF
      </button>
    </div>
  </div>
</div>

<?php if (isset($flash) && $flash): ?>
<div class="alert-glass alert-glass-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible mb-4 fade-in-up">
  <i class="bi bi-check-circle me-2"></i>
  <?= htmlspecialchars($flash['message']) ?>
  <button type="button" class="btn-close btn-close-white opacity-50 float-end" onclick="this.closest('.alert-glass').remove()"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<!-- Error / Data Belum Lengkap -->
<div class="glass-card fade-in-up">
  <div class="empty-state">
    <i class="bi bi-exclamation-triangle-fill d-block" style="color:#f59e0b;"></i>
    <p style="color:var(--text-muted);font-size:1rem;margin-bottom:.5rem;"><?= htmlspecialchars($error) ?></p>
    <div class="d-flex gap-2 justify-content-center flex-wrap mt-3">
      <a href="index.php?page=kriteria&kasus_id=<?= $kid ?>"   class="btn btn-ghost btn-sm"><i class="bi bi-sliders2 me-1"></i>Kriteria</a>
      <a href="index.php?page=alternatif&kasus_id=<?= $kid ?>" class="btn btn-ghost btn-sm"><i class="bi bi-geo-alt me-1"></i>Alternatif</a>
      <a href="index.php?page=penilaian&kasus_id=<?= $kid ?>"  class="btn btn-gradient btn-sm"><i class="bi bi-table me-1"></i>Isi Penilaian</a>
    </div>
  </div>
</div>

<?php else:
  $results  = $topsis_result['results'];
  $kriteria = $topsis_result['kriteria'];
  $winner   = $results[0];
?>

<!-- ══════════════════════════════════════════════
     CHAMPION CARD
══════════════════════════════════════════════ -->
<div class="winner-card mb-4 fade-in-up winner-pulse">
  <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
    <div class="rank-badge rank-1" style="width:48px;height:48px;font-size:1.3rem;flex-shrink:0;">1</div>
    <div style="flex:1;">
      <div style="font-size:.75rem;text-transform:uppercase;letter-spacing:.1em;color:#a5b4fc;font-weight:600;margin-bottom:.2rem;">
        🏆 Rekomendasi Terbaik
      </div>
      <div style="font-size:1.5rem;font-weight:800;letter-spacing:-.5px;margin-bottom:.2rem;">
        <?= htmlspecialchars($winner['alternatif']['nama']) ?>
      </div>
      <?php if ($winner['alternatif']['alamat']): ?>
      <div style="font-size:.85rem;color:rgba(255,255,255,.55);">
        <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($winner['alternatif']['alamat']) ?>
      </div>
      <?php endif; ?>
    </div>
    <div style="text-align:right;flex-shrink:0;">
      <div style="font-size:2.5rem;font-weight:800;color:#fbbf24;letter-spacing:-1px;line-height:1;">
        <?= number_format($winner['preferensi'] * 100, 1) ?>%
      </div>
      <div style="font-size:.75rem;color:rgba(255,255,255,.45);">Nilai Preferensi</div>
      <div style="font-size:.78rem;color:rgba(255,255,255,.5);margin-top:.2rem;">
        C = <?= number_format($winner['preferensi'], 4) ?>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════
     RANKING TABLE + CHART
══════════════════════════════════════════════ -->
<div class="row g-4 mb-4">
  <!-- Ranking Table -->
  <div class="col-lg-6">
    <div class="glass-card h-100 fade-in-up delay-1">
      <div class="card-body">
        <h2 style="font-size:1rem;font-weight:700;margin-bottom:1.25rem;">
          <i class="bi bi-list-ol me-2" style="color:#fbbf24;"></i>Tabel Ranking
        </h2>
        <div class="table-wrapper">
          <table class="table table-dark-custom mb-0">
            <thead>
              <tr>
                <th class="text-center" style="width:50px;">Rank</th>
                <th>Alternatif</th>
                <th class="text-center">D+</th>
                <th class="text-center">D−</th>
                <th class="text-center">Skor C</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $res): ?>
              <tr <?= $res['rank'] === 1 ? 'style="background:rgba(251,191,36,.07);"' : '' ?>>
                <td class="text-center">
                  <div class="rank-badge rank-<?= $res['rank'] <= 3 ? $res['rank'] : 'other' ?> mx-auto">
                    <?= $res['rank'] ?>
                  </div>
                </td>
                <td>
                  <div style="font-weight:<?= $res['rank'] === 1 ? '700' : '500' ?>;">
                    <?= htmlspecialchars($res['alternatif']['nama']) ?>
                  </div>
                  <?php if ($res['alternatif']['alamat']): ?>
                  <div style="font-size:.73rem;color:var(--text-muted);">
                    <?= htmlspecialchars(mb_strimwidth($res['alternatif']['alamat'], 0, 35, '…')) ?>
                  </div>
                  <?php endif; ?>
                </td>
                <td class="text-center" style="font-size:.82rem;color:var(--text-muted);">
                  <?= number_format($res['D_plus'], 4) ?>
                </td>
                <td class="text-center" style="font-size:.82rem;color:var(--text-muted);">
                  <?= number_format($res['D_minus'], 4) ?>
                </td>
                <td class="text-center">
                  <div style="font-weight:700;color:<?= $res['rank'] === 1 ? '#fbbf24' : ($res['rank'] === 2 ? '#d1d5db' : 'var(--text)') ?>;">
                    <?= number_format($res['preferensi'], 4) ?>
                  </div>
                  <!-- Mini bar -->
                  <div class="progress-bar-custom mt-1" style="height:4px;">
                    <div class="progress-fill" data-width="<?= round($res['preferensi'] * 100) ?>"></div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Bar Chart -->
  <div class="col-lg-6">
    <div class="glass-card h-100 fade-in-up delay-2">
      <div class="card-body">
        <h2 style="font-size:1rem;font-weight:700;margin-bottom:1.25rem;">
          <i class="bi bi-bar-chart-horizontal me-2" style="color:#818cf8;"></i>Grafik Perbandingan
        </h2>
        <div class="chart-container" style="height:<?= min(300, max(160, count($results) * 52)) ?>px;">
          <canvas id="rankingChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════
     DETAIL PERHITUNGAN TOPSIS (Collapsible)
══════════════════════════════════════════════ -->
<div class="glass-card fade-in-up delay-3">
  <div class="card-body">
    <button class="btn btn-ghost w-100 d-flex align-items-center justify-content-between"
            type="button" data-bs-toggle="collapse" data-bs-target="#topsisDetail" aria-expanded="false">
      <span style="font-weight:700;font-size:1rem;">
        <i class="bi bi-calculator me-2" style="color:#818cf8;"></i>Detail Perhitungan TOPSIS (7 Langkah)
      </span>
      <i class="bi bi-chevron-down"></i>
    </button>

    <div class="collapse" id="topsisDetail">
      <div class="divider"></div>

      <!-- Kriteria Info -->
      <div class="mb-4">
        <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:.75rem;font-weight:600;">
          Kriteria & Bobot Ternormalisasi (w<sub>j</sub>)
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <?php
          $totalB = $topsis_result['total_bobot'];
          foreach ($kriteria as $j => $kr):
            $wn = ($totalB > 0) ? $kr['bobot'] / $totalB : 0;
          ?>
          <div style="background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.2);border-radius:8px;padding:.5rem .875rem;font-size:.8rem;">
            <strong style="color:#a5b4fc;"><?= htmlspecialchars($kr['nama']) ?></strong><br>
            Bobot: <?= number_format((float)$kr['bobot'], 1) ?> →
            w<sub><?= $j+1 ?></sub> = <strong><?= number_format($wn, 4) ?></strong><br>
            <span class="<?= $kr['tipe'] === 'benefit' ? 'badge-benefit' : 'badge-cost' ?>" style="font-size:.68rem;">
              <?= $kr['tipe'] === 'benefit' ? '↑ Benefit' : '↓ Cost' ?>
            </span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <?php
      // Rebuild original index-based arrays for display
      $alts      = $topsis_result['alternatif'];
      $matX      = $topsis_result['matrix_x'];
      $matR      = $topsis_result['matrix_r'];
      $matV      = $topsis_result['matrix_v'];
      $aPlus     = $topsis_result['A_plus'];
      $aMinus    = $topsis_result['A_minus'];
      $dPlus     = $topsis_result['D_plus'];
      $dMinus    = $topsis_result['D_minus'];
      $n = count($alts);
      $m = count($kriteria);

      // Helper: render a matrix table
      function renderMatrix(array $alts, array $kriteria, array $mat, string $symbol, int $decimals = 4): void {
        echo '<div class="matrix-scroll"><table class="table table-dark-custom mb-0" style="font-size:.78rem;">';
        echo '<thead><tr><th>Alternatif</th>';
        foreach ($kriteria as $j => $kr) {
          echo '<th class="text-center">' . $symbol . '<sub>' . ($j+1) . '</sub><br><small>' . htmlspecialchars($kr['nama']) . '</small></th>';
        }
        echo '</tr></thead><tbody>';
        foreach ($alts as $i => $alt) {
          echo '<tr><td style="font-weight:600;">' . htmlspecialchars($alt['nama']) . '</td>';
          foreach ($kriteria as $j => $kr) {
            $v = $mat[$i][$j] ?? 0;
            echo '<td class="text-center">' . number_format($v, $decimals) . '</td>';
          }
          echo '</tr>';
        }
        echo '</tbody></table></div>';
      }
      ?>

      <!-- Step 1 -->
      <div class="topsis-step">
        <div class="d-flex align-items-center mb-2">
          <span class="topsis-step-num">1</span>
          <strong>Matriks Keputusan X (nilai asli)</strong>
          <span class="ms-auto formula-box">x<sub>ij</sub> ∈ [1, 10]</span>
        </div>
        <?php renderMatrix($alts, $kriteria, $matX, 'x', 2); ?>
      </div>

      <!-- Step 2 -->
      <div class="topsis-step">
        <div class="d-flex align-items-center mb-2">
          <span class="topsis-step-num">2</span>
          <strong>Normalisasi Matriks R (Euclidean)</strong>
          <span class="ms-auto formula-box">r<sub>ij</sub> = x<sub>ij</sub> / √(Σx<sub>ij</sub>²)</span>
        </div>
        <?php renderMatrix($alts, $kriteria, $matR, 'r', 6); ?>
      </div>

      <!-- Step 3 -->
      <div class="topsis-step">
        <div class="d-flex align-items-center mb-2">
          <span class="topsis-step-num">3</span>
          <strong>Matriks Ternormalisasi Terbobot V</strong>
          <span class="ms-auto formula-box">v<sub>ij</sub> = w<sub>j</sub> × r<sub>ij</sub></span>
        </div>
        <?php renderMatrix($alts, $kriteria, $matV, 'v', 6); ?>
      </div>

      <!-- Step 4 -->
      <div class="topsis-step">
        <div class="d-flex align-items-center mb-2">
          <span class="topsis-step-num">4</span>
          <strong>Solusi Ideal Positif (A+) dan Negatif (A−)</strong>
        </div>
        <div class="matrix-scroll">
          <table class="table table-dark-custom mb-0" style="font-size:.78rem;">
            <thead>
              <tr>
                <th>Solusi</th>
                <?php foreach ($kriteria as $j => $kr): ?>
                <th class="text-center"><?= htmlspecialchars($kr['nama']) ?></th>
                <?php endforeach; ?>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="font-weight:700;color:#4ade80;">A+ (Ideal +)</td>
                <?php foreach ($kriteria as $j => $kr): ?>
                <td class="text-center" style="color:#4ade80;"><?= number_format($aPlus[$j], 6) ?></td>
                <?php endforeach; ?>
              </tr>
              <tr>
                <td style="font-weight:700;color:#f87171;">A− (Ideal −)</td>
                <?php foreach ($kriteria as $j => $kr): ?>
                <td class="text-center" style="color:#f87171;"><?= number_format($aMinus[$j], 6) ?></td>
                <?php endforeach; ?>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="font-size:.73rem;color:var(--text-muted);margin-top:.5rem;">
          Benefit: A+ = max, A− = min &nbsp;|&nbsp; Cost: A+ = min, A− = max
        </div>
      </div>

      <!-- Step 5 & 6 -->
      <div class="topsis-step">
        <div class="d-flex align-items-center mb-2">
          <span class="topsis-step-num">5-6</span>
          <strong>Jarak ke A+ (D+) dan A− (D−)</strong>
          <span class="ms-auto formula-box">D<sub>i</sub> = √(Σ(v<sub>ij</sub>−A±<sub>j</sub>)²)</span>
        </div>
        <div class="matrix-scroll">
          <table class="table table-dark-custom mb-0" style="font-size:.78rem;">
            <thead>
              <tr><th>Alternatif</th><th class="text-center">D+ (jarak ke ideal+)</th><th class="text-center">D− (jarak ke ideal−)</th></tr>
            </thead>
            <tbody>
            <?php foreach ($alts as $i => $alt): ?>
              <tr>
                <td style="font-weight:600;"><?= htmlspecialchars($alt['nama']) ?></td>
                <td class="text-center" style="color:#f87171;"><?= number_format($dPlus[$i], 6) ?></td>
                <td class="text-center" style="color:#4ade80;"><?= number_format($dMinus[$i], 6) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Step 7 -->
      <div class="topsis-step">
        <div class="d-flex align-items-center mb-2">
          <span class="topsis-step-num">7</span>
          <strong>Nilai Preferensi C &amp; Ranking Akhir</strong>
          <span class="ms-auto formula-box">C<sub>i</sub> = D−<sub>i</sub> / (D+<sub>i</sub> + D−<sub>i</sub>)</span>
        </div>
        <div class="matrix-scroll">
          <table class="table table-dark-custom mb-0" style="font-size:.78rem;">
            <thead>
              <tr><th>Rank</th><th>Alternatif</th><th class="text-center">D+</th><th class="text-center">D−</th><th class="text-center">C (Preferensi)</th></tr>
            </thead>
            <tbody>
            <?php foreach ($results as $res): ?>
              <tr <?= $res['rank'] === 1 ? 'style="background:rgba(251,191,36,.07);"' : '' ?>>
                <td class="text-center">
                  <span class="rank-badge rank-<?= $res['rank'] <= 3 ? $res['rank'] : 'other' ?>">
                    <?= $res['rank'] ?>
                  </span>
                </td>
                <td style="font-weight:600;"><?= htmlspecialchars($res['alternatif']['nama']) ?></td>
                <td class="text-center"><?= number_format($res['D_plus'], 6) ?></td>
                <td class="text-center"><?= number_format($res['D_minus'], 6) ?></td>
                <td class="text-center" style="font-weight:700;color:<?= $res['rank'] === 1 ? '#fbbf24' : 'var(--text)' ?>;">
                  <?= number_format($res['preferensi'], 6) ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="alert-glass alert-glass-info mt-3" style="font-size:.78rem;">
          <i class="bi bi-info-circle me-1"></i>
          Semakin besar nilai C<sub>i</sub> (mendekati 1), semakin dekat alternatif ke solusi ideal positif dan semakin jauh dari solusi ideal negatif → <strong>alternatif terbaik.</strong>
        </div>
      </div>

    </div><!-- /.collapse -->
  </div>
</div>

<?php endif; ?>

<?php
// Inject Chart.js script
$chartLabels  = [];
$chartData    = [];
$chartColors  = [];
if (!empty($results)) {
  foreach ($results as $res) {
    $chartLabels[] = $res['alternatif']['nama'];
    $chartData[]   = round($res['preferensi'], 4);
    $chartColors[] = $res['rank'] === 1 ? 'rgba(251,191,36,0.8)' : ($res['rank'] === 2 ? 'rgba(209,213,219,0.7)' : ($res['rank'] === 3 ? 'rgba(205,127,50,0.7)' : 'rgba(99,102,241,0.6)'));
  }
}
$labelsJson = json_encode($chartLabels);
$dataJson   = json_encode($chartData);
$colorsJson = json_encode($chartColors);

$extra_js = <<<JS
<script>
(function(){
  const ctx = document.getElementById('rankingChart');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: $labelsJson,
      datasets: [{
        label: 'Nilai Preferensi',
        data: $dataJson,
        backgroundColor: $colorsJson,
        borderRadius: 8,
        borderSkipped: false,
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => ' C = ' + ctx.raw.toFixed(4) + ' (' + (ctx.raw * 100).toFixed(1) + '%)'
          }
        }
      },
      scales: {
        x: {
          min: 0, max: 1,
          grid: { color: 'rgba(255,255,255,0.05)' },
          ticks: { color: '#94a3b8', callback: v => (v*100).toFixed(0)+'%' }
        },
        y: {
          grid: { display: false },
          ticks: { color: '#e2e8f0', font: { weight: '600' } }
        }
      }
    }
  });
})();
</script>
JS;
?>
