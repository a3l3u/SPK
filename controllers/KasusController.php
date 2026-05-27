<?php
/**
 * Controller: Kasus
 * Menangani CRUD untuk kasus pemilihan
 */

class KasusController {

    public function dashboard(): void {
        $stats      = getKasusStats();
        $kasus_list = getAllKasus();
        $flash      = getFlash();

        $title       = 'Dashboard';
        $active_page = 'dashboard';
        $kasus_id    = null;

        require BASE_PATH . '/views/layouts/header.php';
        require BASE_PATH . '/views/dashboard.php';
        require BASE_PATH . '/views/layouts/footer.php';
    }

    public function index(): void {
        $kasus_list = getAllKasus();
        $flash      = getFlash();

        $title       = 'Daftar Kasus';
        $active_page = 'kasus';
        $kasus_id    = null;

        require BASE_PATH . '/views/layouts/header.php';
        require BASE_PATH . '/views/kasus/index.php';
        require BASE_PATH . '/views/layouts/footer.php';
    }

    public function create(): void {
        $kasus  = null;
        $action = 'store';
        $flash  = getFlash();

        $title       = 'Buat Kasus Baru';
        $active_page = 'kasus';
        $kasus_id    = null;

        require BASE_PATH . '/views/layouts/header.php';
        require BASE_PATH . '/views/kasus/create.php';
        require BASE_PATH . '/views/layouts/footer.php';
    }

    public function store(): void {
        $nama = trim($_POST['nama'] ?? '');
        if (empty($nama)) {
            setFlash('error', 'Nama kasus tidak boleh kosong.');
            redirect('index.php?page=kasus&action=create');
        }

        $id = createKasus($_POST);
        setFlash('success', "Kasus \"$nama\" berhasil dibuat! Silakan tambahkan kriteria.");
        redirect("index.php?page=kriteria&kasus_id=$id");
    }

    public function show(?int $id): void {
        if (!$id) { redirect('index.php?page=kasus'); }

        $kasus = getKasusById($id);
        if (!$kasus) {
            setFlash('error', 'Kasus tidak ditemukan.');
            redirect('index.php?page=kasus');
        }

        $progress = getKasusProgress($id);
        $flash    = getFlash();

        $title       = 'Detail Kasus';
        $active_page = 'kasus_show';
        $kasus_id    = $id;

        require BASE_PATH . '/views/layouts/header.php';
        require BASE_PATH . '/views/kasus/show.php';
        require BASE_PATH . '/views/layouts/footer.php';
    }

    public function edit(?int $id): void {
        if (!$id) { redirect('index.php?page=kasus'); }

        $kasus = getKasusById($id);
        if (!$kasus) {
            setFlash('error', 'Kasus tidak ditemukan.');
            redirect('index.php?page=kasus');
        }

        $action   = 'update';
        $flash    = getFlash();
        $kasus_id = $id;

        $title       = 'Edit Kasus';
        $active_page = 'kasus';

        require BASE_PATH . '/views/layouts/header.php';
        require BASE_PATH . '/views/kasus/create.php';
        require BASE_PATH . '/views/layouts/footer.php';
    }

    public function update(): void {
        $id   = (int)($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');

        if (!$id || empty($nama)) {
            setFlash('error', 'Data tidak valid.');
            redirect('index.php?page=kasus');
        }

        updateKasus($id, $_POST);
        setFlash('success', 'Kasus berhasil diperbarui.');
        redirect("index.php?page=kasus&action=show&id=$id");
    }

    public function delete(): void {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $kasus = getKasusById($id);
            deleteKasus($id);
            setFlash('success', "Kasus \"{$kasus['nama']}\" berhasil dihapus.");
        }
        redirect('index.php?page=kasus');
    }
}
