<?php
/**
 * Controller: Kriteria
 * Menangani CRUD kriteria per kasus
 */

class KriteriaController {

    public function index(?int $kasus_id): void {
        if (!$kasus_id) { redirect('index.php?page=kasus'); }

        $kasus = getKasusById($kasus_id);
        if (!$kasus) {
            setFlash('error', 'Kasus tidak ditemukan.');
            redirect('index.php?page=kasus');
        }

        $kriteria_list = getKriteriaByKasusId($kasus_id);
        $total_bobot   = getTotalBobot($kasus_id);
        $flash         = getFlash();

        // Kriteria untuk diedit (via query string ?edit_id=X)
        $edit_kriteria = null;
        if (!empty($_GET['edit_id'])) {
            $edit_kriteria = getKriteriaById((int)$_GET['edit_id']);
        }

        $title       = 'Kelola Kriteria';
        $active_page = 'kriteria';

        require BASE_PATH . '/views/layouts/header.php';
        require BASE_PATH . '/views/kriteria/index.php';
        require BASE_PATH . '/views/layouts/footer.php';
    }

    public function store(): void {
        $kasus_id = (int)($_POST['kasus_id'] ?? 0);
        $nama     = trim($_POST['nama'] ?? '');
        $bobot    = (float)($_POST['bobot'] ?? 0);
        $tipe     = $_POST['tipe'] ?? 'benefit';

        if (!$kasus_id || empty($nama)) {
            setFlash('error', 'Nama kriteria tidak boleh kosong.');
            redirect("index.php?page=kriteria&kasus_id=$kasus_id");
        }

        if ($bobot <= 0) {
            setFlash('error', 'Bobot harus lebih dari 0.');
            redirect("index.php?page=kriteria&kasus_id=$kasus_id");
        }

        createKriteria(['kasus_id' => $kasus_id, 'nama' => $nama, 'bobot' => $bobot, 'tipe' => $tipe]);
        setFlash('success', "Kriteria \"$nama\" berhasil ditambahkan.");
        redirect("index.php?page=kriteria&kasus_id=$kasus_id");
    }

    public function update(): void {
        $id       = (int)($_POST['id'] ?? 0);
        $kasus_id = (int)($_POST['kasus_id'] ?? 0);
        $nama     = trim($_POST['nama'] ?? '');
        $bobot    = (float)($_POST['bobot'] ?? 0);
        $tipe     = $_POST['tipe'] ?? 'benefit';

        if (!$id || !$kasus_id || empty($nama) || $bobot <= 0) {
            setFlash('error', 'Data tidak valid.');
            redirect("index.php?page=kriteria&kasus_id=$kasus_id");
        }

        updateKriteria($id, ['nama' => $nama, 'bobot' => $bobot, 'tipe' => $tipe]);
        setFlash('success', "Kriteria \"$nama\" berhasil diperbarui.");
        redirect("index.php?page=kriteria&kasus_id=$kasus_id");
    }

    public function delete(): void {
        $id       = (int)($_POST['id'] ?? 0);
        $kasus_id = (int)($_POST['kasus_id'] ?? 0);

        if ($id) {
            $k = getKriteriaById($id);
            deleteKriteria($id);
            setFlash('success', "Kriteria \"{$k['nama']}\" berhasil dihapus.");
        }
        redirect("index.php?page=kriteria&kasus_id=$kasus_id");
    }
}
