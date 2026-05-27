<?php
/**
 * Controller: Penilaian
 * Menangani input matriks penilaian (alternatif × kriteria)
 */

class PenilaianController {

    public function index(?int $kasus_id): void {
        if (!$kasus_id) { redirect('index.php?page=kasus'); }

        $kasus = getKasusById($kasus_id);
        if (!$kasus) {
            setFlash('error', 'Kasus tidak ditemukan.');
            redirect('index.php?page=kasus');
        }

        $kriteria_list   = getKriteriaByKasusId($kasus_id);
        $alternatif_list = getAlternatifByKasusId($kasus_id);
        $matrix          = getPenilaianMatrix($kasus_id);
        $flash           = getFlash();

        // Validasi: perlu minimal 2 kriteria dan 2 alternatif
        $error = null;
        if (count($kriteria_list) < 2) {
            $error = 'Minimal 2 kriteria diperlukan sebelum mengisi penilaian.';
        } elseif (count($alternatif_list) < 2) {
            $error = 'Minimal 2 alternatif diperlukan sebelum mengisi penilaian.';
        }

        $title       = 'Matriks Penilaian';
        $active_page = 'penilaian';

        require BASE_PATH . '/views/layouts/header.php';
        require BASE_PATH . '/views/penilaian/index.php';
        require BASE_PATH . '/views/layouts/footer.php';
    }

    public function store(): void {
        $kasus_id = (int)($_POST['kasus_id'] ?? 0);

        if (!$kasus_id) {
            redirect('index.php?page=kasus');
        }

        $saved = saveAllPenilaian($kasus_id, $_POST);

        if ($saved > 0) {
            setFlash('success', "Penilaian berhasil disimpan ($saved nilai). Klik \"Hitung TOPSIS\" untuk melihat hasil.");
            redirect("index.php?page=hasil&kasus_id=$kasus_id");
        } else {
            setFlash('error', 'Gagal menyimpan penilaian. Pastikan form sudah diisi dengan benar.');
            redirect("index.php?page=penilaian&kasus_id=$kasus_id");
        }
    }
}
