/**
 * SPK TOPSIS — App JavaScript
 * Interaktivitas: toast, validasi, animasi, chip, konfirmasi
 */

document.addEventListener('DOMContentLoaded', function () {

  /* ── Auto-dismiss alerts ───────────────────────────────────── */
  document.querySelectorAll('.alert-dismissible').forEach(el => {
    setTimeout(() => {
      el.style.transition = 'opacity .5s ease, transform .5s ease';
      el.style.opacity    = '0';
      el.style.transform  = 'translateY(-8px)';
      setTimeout(() => el.remove(), 500);
    }, 4500);
  });

  /* ── Animate progress bars on load ────────────────────────── */
  document.querySelectorAll('.progress-fill').forEach(bar => {
    const target = bar.dataset.width || '0';
    bar.style.width = '0%';
    requestAnimationFrame(() => {
      setTimeout(() => { bar.style.width = target + '%'; }, 120);
    });
  });

  /* ── Bobot sum validation ─────────────────────────────────── */
  const bobotInputs = document.querySelectorAll('.bobot-input');
  if (bobotInputs.length) {
    updateBobotSum();
    bobotInputs.forEach(inp => inp.addEventListener('input', updateBobotSum));
  }

  /* ── Matrix input: clamp 1–10 ─────────────────────────────── */
  document.querySelectorAll('.matrix-input').forEach(inp => {
    inp.addEventListener('change', function () {
      let v = parseFloat(this.value);
      if (isNaN(v) || v < 1)  v = 1;
      if (v > 10)              v = 10;
      this.value = v;
    });
    // Arrow key navigation inside matrix
    inp.addEventListener('keydown', function (e) {
      const cell  = this.closest('td');
      const row   = cell?.parentElement;
      const tbody = row?.parentElement;
      if (!tbody) return;
      const cells = [...tbody.querySelectorAll('td')];
      const idx   = cells.indexOf(cell);
      let next = null;
      if (e.key === 'ArrowRight') next = cells[idx + 1];
      if (e.key === 'ArrowLeft')  next = cells[idx - 1];
      if (e.key === 'ArrowDown') {
        const rows   = [...tbody.querySelectorAll('tr')];
        const rowIdx = rows.indexOf(row);
        const colIdx = [...row.children].indexOf(cell);
        if (rows[rowIdx + 1]) next = rows[rowIdx + 1].children[colIdx];
      }
      if (e.key === 'ArrowUp') {
        const rows   = [...tbody.querySelectorAll('tr')];
        const rowIdx = rows.indexOf(row);
        const colIdx = [...row.children].indexOf(cell);
        if (rows[rowIdx - 1]) next = rows[rowIdx - 1].children[colIdx];
      }
      if (next) {
        e.preventDefault();
        next.querySelector('.matrix-input')?.focus();
      }
    });
  });

  /* ── Preset chip buttons ───────────────────────────────────── */
  document.querySelectorAll('.chip-btn[data-target]').forEach(chip => {
    chip.addEventListener('click', function () {
      const targetId = this.dataset.target;
      const targetEl = document.getElementById(targetId);
      if (targetEl) {
        targetEl.value = this.dataset.value || this.textContent.replace(/[^\w\s]/g, '').trim();
        targetEl.focus();
        targetEl.dispatchEvent(new Event('input'));
      }
      // visual feedback
      this.style.transform = 'scale(.92)';
      setTimeout(() => (this.style.transform = ''), 150);
    });
  });

  /* ── Tipe toggle visual sync ───────────────────────────────── */
  document.querySelectorAll('.tipe-toggle input[type=radio]').forEach(radio => {
    radio.addEventListener('change', syncTipeDisplay);
  });

  /* ── Chart.js defaults ─────────────────────────────────────── */
  if (typeof Chart !== 'undefined') {
    Chart.defaults.color          = '#94a3b8';
    Chart.defaults.font.family    = "'Inter', system-ui, sans-serif";
    Chart.defaults.plugins.legend.display = false;
  }
});

/* ── Bobot sum calculator ─────────────────────────────────── */
function updateBobotSum() {
  let sum = 0;
  document.querySelectorAll('.bobot-input').forEach(i => {
    sum += parseFloat(i.value) || 0;
  });
  const box = document.getElementById('bobotSumBox');
  if (!box) return;

  const rounded = Math.round(sum * 10) / 10;
  box.innerHTML = `Total Bobot: <strong>${rounded}</strong>`;
  if (rounded === 100) {
    box.className = 'alert-glass alert-glass-success mb-0';
    box.innerHTML = `✅ Total Bobot: <strong>${rounded}</strong> — Sempurna!`;
  } else if (rounded > 100) {
    box.className = 'alert-glass alert-glass-danger mb-0';
    box.innerHTML = `⚠️ Total Bobot: <strong>${rounded}</strong> — Melebihi 100`;
  } else {
    box.className = 'alert-glass alert-glass-warning mb-0';
    box.innerHTML = `ℹ️ Total Bobot: <strong>${rounded}</strong> — Sistem akan normalisasi otomatis`;
  }
}

/* ── Delete confirmation ───────────────────────────────────── */
function confirmDelete(formId, itemName) {
  if (confirm(`Yakin hapus "${itemName}"?\n\nSemua data terkait (kriteria, alternatif, penilaian) juga akan ikut terhapus.`)) {
    document.getElementById(formId)?.submit();
  }
}

/* ── Simple delete (tanpa cascade warning) ─────────────────── */
function confirmDeleteSimple(formId, itemName) {
  if (confirm(`Hapus "${itemName}"?`)) {
    document.getElementById(formId)?.submit();
  }
}

/* ── Tipe display sync ─────────────────────────────────────── */
function syncTipeDisplay() {
  // handled via CSS :checked selectors
}

/* ── Fill matrix preset (semua cell = nilai) ─────────────── */
function fillAllMatrix(nilai) {
  document.querySelectorAll('.matrix-input').forEach(inp => {
    inp.value = nilai;
    inp.classList.add('border-primary');
    setTimeout(() => inp.classList.remove('border-primary'), 800);
  });
}

/* ── Print to PDF ──────────────────────────────────────────── */
function printHasil() {
  const kasusEl   = document.querySelector('.page-subtitle');
  const kasusName = kasusEl ? kasusEl.textContent.trim() : 'Hasil TOPSIS';

  // Ambil konten utama saja (tanpa sidebar)
  const mainEl = document.querySelector('.main-content');
  if (!mainEl) { window.print(); return; }

  // Clone konten agar tidak merusak halaman asli
  const clone = mainEl.cloneNode(true);

  // Hapus breadcrumb & tombol aksi di clone
  clone.querySelectorAll(
    '.page-header .d-flex > a, .page-header .d-flex > button, .page-header .btn, .no-print'
  ).forEach(el => el.remove());

  // Buka collapse Detail Perhitungan di clone
  clone.querySelectorAll('.collapse').forEach(el => {
    el.style.display = 'block';
    el.classList.add('show');
  });
  // Sembunyikan chevron toggle button di clone
  clone.querySelectorAll('[data-bs-toggle="collapse"]').forEach(el => el.style.display = 'none');

  const css = `
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', Arial, sans-serif;
      background: #fff;
      color: #111;
      font-size: 13px;
      line-height: 1.6;
      padding: 1.5rem 2rem;
    }
    /* Semua teks hitam */
    *, p, span, div, td, th, li, h1, h2, h3, h4, strong, small {
      color: #111 !important;
      -webkit-text-fill-color: #111 !important;
    }
    /* Page header */
    .page-header { border-bottom: 2px solid #333; padding-bottom: .75rem; margin-bottom: 1.5rem; }
    .page-title { font-size: 1.5rem; font-weight: 800; }
    .page-subtitle { color: #444; font-size: .9rem; margin-top: 3px; }
    /* Gradient text */
    .gradient-text {
      background: none !important;
      -webkit-text-fill-color: #3333aa !important;
      color: #3333aa !important;
    }
    /* Cards */
    .glass-card, .winner-card, .stat-card {
      background: #f4f4f8;
      border: 1px solid #aaa;
      border-radius: 10px;
      margin-bottom: 1rem;
      padding: 1.25rem;
      page-break-inside: avoid;
    }
    .winner-card { background: #eef0ff; border-color: #6666bb; }
    .card-body { padding: 0; }
    /* Tabel */
    table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; page-break-inside: avoid; }
    thead th {
      background: #d8d8e8;
      color: #111;
      font-weight: 700;
      font-size: .78rem;
      text-transform: uppercase;
      letter-spacing: .05em;
      padding: .6rem .75rem;
      border: 1px solid #999;
      text-align: left;
    }
    td { padding: .6rem .75rem; border: 1px solid #ddd; font-size: .82rem; vertical-align: middle; }
    tbody tr:nth-child(even) { background: #f5f5f8; }
    .text-center { text-align: center; }
    /* Rank badges */
    .rank-badge {
      width: 28px; height: 28px;
      border-radius: 50%;
      display: inline-flex; align-items: center; justify-content: center;
      font-weight: 800; font-size: .8rem;
      border: 2px solid #666;
    }
    .rank-1 { background: #f59e0b; color: #000 !important; -webkit-text-fill-color: #000 !important; }
    .rank-2 { background: #9ca3af; color: #000 !important; -webkit-text-fill-color: #000 !important; }
    .rank-3 { background: #b45309; color: #fff !important; -webkit-text-fill-color: #fff !important; }
    .rank-other { background: #e5e7eb; color: #333 !important; }
    /* Badges */
    .badge-benefit { background: #dcfce7; color: #166534 !important; -webkit-text-fill-color: #166534 !important; border: 1px solid #86efac; font-size:.72rem; font-weight:600; padding:.15rem .5rem; border-radius:20px; }
    .badge-cost    { background: #fee2e2; color: #991b1b !important; -webkit-text-fill-color: #991b1b !important; border: 1px solid #fca5a5; font-size:.72rem; font-weight:600; padding:.15rem .5rem; border-radius:20px; }
    .badge-bobot   { background: #ede9fe; color: #4c1d95 !important; -webkit-text-fill-color: #4c1d95 !important; border: 1px solid #a78bfa; font-size:.72rem; font-weight:600; padding:.15rem .5rem; border-radius:20px; }
    /* Alert / info box */
    .alert-glass { border-radius: 8px; padding: .6rem .9rem; font-size: .82rem; font-weight: 500; margin-bottom: .75rem; }
    .alert-glass-info    { background: #eff6ff; border: 1px solid #93c5fd; color: #1e40af !important; -webkit-text-fill-color: #1e40af !important; }
    .alert-glass-warning { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e !important; -webkit-text-fill-color: #92400e !important; }
    /* TOPSIS step */
    .topsis-step { background: #f0f0f8; border: 1px solid #9999cc; border-radius: 8px; padding: .875rem 1rem; margin-bottom: .75rem; page-break-inside: avoid; }
    .topsis-step-num {
      width: 24px; height: 24px; border-radius: 50%;
      background: #4444bb; color: #fff !important; -webkit-text-fill-color: #fff !important;
      display: inline-flex; align-items: center; justify-content: center;
      font-size: .72rem; font-weight: 700; margin-right: .4rem;
    }
    .formula-box { background: #f0f0f8; border: 1px solid #9999cc; border-radius: 6px; padding: .3rem .75rem; font-family: monospace; font-size: .78rem; color: #1e1e7a !important; -webkit-text-fill-color: #1e1e7a !important; }
    /* Progress / steps */
    .step-card { background: #f4f4f8; border: 1px solid #ccc; border-radius: 8px; padding: .875rem 1rem; margin-bottom: .5rem; }
    .progress-bar-custom { height: 6px; background: #ddd; border-radius: 3px; overflow: hidden; margin: .3rem 0; }
    .progress-fill { height: 100%; background: #5555cc; border-radius: 3px; }
    /* Matrix scroll */
    .matrix-scroll { overflow-x: visible; }
    /* Winner card layout */
    .winner-card [style*="2.5rem"] { font-size: 2rem; font-weight: 800; color: #b45309 !important; -webkit-text-fill-color: #b45309 !important; }
    /* Collapse sudah dibuka via JS */
    .collapse { display: block !important; }
    /* Grid / flex helpers */
    .row { display: flex; flex-wrap: wrap; gap: .75rem; margin-bottom: .75rem; }
    .col-lg-6 { flex: 1 1 45%; min-width: 280px; }
    .d-flex { display: flex; }
    .align-items-center { align-items: center; }
    .justify-content-between { justify-content: space-between; }
    .gap-2 { gap: .5rem; }
    .mb-4 { margin-bottom: 1rem; }
    .mb-3 { margin-bottom: .75rem; }
    .mt-auto { margin-top: auto; }
    .ms-auto { margin-left: auto; }
    .me-1 { margin-right: .25rem; }
    .me-2 { margin-right: .5rem; }
    .mx-auto { margin: 0 auto; }
    h2 { font-size: 1rem; font-weight: 700; margin-bottom: .75rem; page-break-after: avoid; }
    small { font-size: .78rem; }
    @media print {
      body { padding: 0; }
      .glass-card, .topsis-step, table { page-break-inside: avoid; }
    }
  `;

  const html = `<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hasil Ranking TOPSIS - ${kasusName}</title>
  <style>${css}</style>
</head>
<body>
  ${clone.innerHTML}
  <script>
    // Hapus canvas (Chart.js tidak render di popup) — sudah ada tabel ranking
    document.querySelectorAll('canvas').forEach(c => {
      c.closest('.col-lg-6') && (c.closest('.col-lg-6').style.display = 'none');
    });
    window.onload = function() { window.focus(); window.print(); };
  <\/script>
</body>
</html>`;

  const win = window.open('', '_blank', 'width=900,height=700');
  win.document.open();
  win.document.write(html);
  win.document.close();
}

