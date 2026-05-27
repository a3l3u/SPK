<?php
/**
 * View: Kasus/Index
 * Daftar semua kasus dalam format tabel
 */
?>

<div class="page-header fade-in-up">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="page-title">Daftar Kasus</h1>
      <p class="page-subtitle">Kelola semua kasus pemilihan tempat Anda</p>
    </div>
    <a href="index.php?page=kasus&action=create" class="btn btn-gradient">
      <i class="bi bi-plus-lg me-1"></i> Buat Kasus Baru
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

<div class="glass-card fade-in-up">
  <?php if (empty($kasus_list)): ?>
  <div class="empty-state">
    <i class="bi bi-folder2 d-block"></i>
    <p style="color:var(--text-muted);">Belum ada kasus. Buat kasus pertama Anda!</p>
    <a href="index.php?page=kasus&action=create" class="btn btn-gradient mt-2">
      <i class="bi bi-plus-lg me-1"></i> Buat Sekarang
    </a>
  </div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="table table-dark-custom table-dark-custom align-middle mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Kasus</th>
          <th>Tipe Tempat</th>
          <th class="text-center">Kriteria</th>
          <th class="text-center">Alternatif</th>
          <th>Dibuat</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($kasus_list as $i => $k): ?>
        <tr>
          <td style="color:var(--text-faint);font-size:.85rem;"><?= $i + 1 ?></td>
          <td>
            <div style="font-weight:600;"><?= htmlspecialchars($k['nama']) ?></div>
            <?php if ($k['deskripsi']): ?>
            <div style="font-size:.78rem;color:var(--text-muted);">
              <?= htmlspecialchars(mb_strimwidth($k['deskripsi'], 0, 60, '…')) ?>
            </div>
            <?php endif; ?>
          </td>
          <td>
            <span class="badge-bobot"><?= htmlspecialchars($k['tipe_tempat'] ?: 'Umum') ?></span>
          </td>
          <td class="text-center">
            <span style="font-weight:600;color:#a5b4fc;"><?= (int)$k['jumlah_kriteria'] ?></span>
          </td>
          <td class="text-center">
            <span style="font-weight:600;color:#4ade80;"><?= (int)$k['jumlah_alternatif'] ?></span>
          </td>
          <td style="font-size:.8rem;color:var(--text-muted);">
            <?= date('d M Y', strtotime($k['created_at'])) ?>
          </td>
          <td class="text-end">
            <div class="d-flex gap-1 justify-content-end">
              <a href="index.php?page=kasus&action=show&id=<?= $k['id'] ?>"
                 class="btn btn-ghost btn-sm" title="Detail">
                <i class="bi bi-eye"></i>
              </a>
              <a href="index.php?page=hasil&kasus_id=<?= $k['id'] ?>"
                 class="btn btn-ghost btn-sm" title="Hasil">
                <i class="bi bi-trophy" style="color:#fbbf24;"></i>
              </a>
              <a href="index.php?page=kasus&action=edit&id=<?= $k['id'] ?>"
                 class="btn btn-ghost btn-sm" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <!-- Delete form -->
              <form id="del-kasus-<?= $k['id'] ?>" method="POST" action="index.php" style="display:inline;">
                <input type="hidden" name="_page"   value="kasus">
                <input type="hidden" name="_action" value="delete">
                <input type="hidden" name="id"      value="<?= $k['id'] ?>">
              </form>
              <button type="button"
                      class="btn btn-danger-soft btn-sm"
                      title="Hapus"
                      onclick="confirmDelete('del-kasus-<?= $k['id'] ?>', '<?= addslashes(htmlspecialchars($k['nama'])) ?>')">
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
