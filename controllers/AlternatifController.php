<?php
/**
 * Controller: Alternatif
 * Menangani CRUD alternatif (tempat) per kasus
 */

class AlternatifController {

    public function index(?int $kasus_id): void {
        if (!$kasus_id) { redirect('index.php?page=kasus'); }

        $kasus = getKasusById($kasus_id);
        if (!$kasus) {
            setFlash('error', 'Kasus tidak ditemukan.');
            redirect('index.php?page=kasus');
        }

        $alternatif_list = getAlternatifByKasusId($kasus_id);
        $flash           = getFlash();

        // Alternatif untuk diedit
        $edit_alternatif = null;
        if (!empty($_GET['edit_id'])) {
            $edit_alternatif = getAlternatifById((int)$_GET['edit_id']);
        }

        $title       = 'Kelola Alternatif';
        $active_page = 'alternatif';

        require BASE_PATH . '/views/layouts/header.php';
        require BASE_PATH . '/views/alternatif/index.php';
        require BASE_PATH . '/views/layouts/footer.php';
    }

    public function store(): void {
        $kasus_id = (int)($_POST['kasus_id'] ?? 0);
        $nama     = trim($_POST['nama'] ?? '');

        if (!$kasus_id || empty($nama)) {
            setFlash('error', 'Nama tempat tidak boleh kosong.');
            redirect("index.php?page=alternatif&kasus_id=$kasus_id");
        }

        createAlternatif([
            'kasus_id'   => $kasus_id,
            'nama'       => $nama,
            'alamat'     => $_POST['alamat']     ?? '',
            'keterangan' => $_POST['keterangan'] ?? '',
        ]);
        setFlash('success', "Alternatif \"$nama\" berhasil ditambahkan.");
        redirect("index.php?page=alternatif&kasus_id=$kasus_id");
    }

    public function update(): void {
        $id       = (int)($_POST['id'] ?? 0);
        $kasus_id = (int)($_POST['kasus_id'] ?? 0);
        $nama     = trim($_POST['nama'] ?? '');

        if (!$id || !$kasus_id || empty($nama)) {
            setFlash('error', 'Data tidak valid.');
            redirect("index.php?page=alternatif&kasus_id=$kasus_id");
        }

        updateAlternatif($id, [
            'nama'       => $nama,
            'alamat'     => $_POST['alamat']     ?? '',
            'keterangan' => $_POST['keterangan'] ?? '',
        ]);
        setFlash('success', "Alternatif \"$nama\" berhasil diperbarui.");
        redirect("index.php?page=alternatif&kasus_id=$kasus_id");
    }

    public function delete(): void {
        $id       = (int)($_POST['id'] ?? 0);
        $kasus_id = (int)($_POST['kasus_id'] ?? 0);

        if ($id) {
            $a = getAlternatifById($id);
            deleteAlternatif($id);
            setFlash('success', "Alternatif \"{$a['nama']}\" berhasil dihapus.");
        }
        redirect("index.php?page=alternatif&kasus_id=$kasus_id");
    }
}
