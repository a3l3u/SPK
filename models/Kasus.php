<?php
/**
 * Model: Kasus
 * CRUD untuk tabel kasus
 */

function getAllKasus(): array {
    $db = getDB();
    $sql = "SELECT k.*,
                COUNT(DISTINCT kr.id) AS jumlah_kriteria,
                COUNT(DISTINCT a.id)  AS jumlah_alternatif
            FROM kasus k
            LEFT JOIN kriteria   kr ON kr.kasus_id = k.id
            LEFT JOIN alternatif a  ON a.kasus_id  = k.id
            GROUP BY k.id
            ORDER BY k.created_at DESC";
    return $db->query($sql)->fetchAll();
}

function getKasusById(int $id): array|false {
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM kasus WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createKasus(array $data): int {
    $db   = getDB();
    $stmt = $db->prepare(
        "INSERT INTO kasus (nama, deskripsi, tipe_tempat) VALUES (?, ?, ?)"
    );
    $stmt->execute([
        trim($data['nama']),
        trim($data['deskripsi'] ?? ''),
        trim($data['tipe_tempat'] ?? ''),
    ]);
    return (int)$db->lastInsertId();
}

function updateKasus(int $id, array $data): bool {
    $db   = getDB();
    $stmt = $db->prepare(
        "UPDATE kasus SET nama = ?, deskripsi = ?, tipe_tempat = ? WHERE id = ?"
    );
    return $stmt->execute([
        trim($data['nama']),
        trim($data['deskripsi'] ?? ''),
        trim($data['tipe_tempat'] ?? ''),
        $id,
    ]);
}

function deleteKasus(int $id): bool {
    $db   = getDB();
    $stmt = $db->prepare("DELETE FROM kasus WHERE id = ?");
    return $stmt->execute([$id]);
}

function getKasusStats(): array {
    $db  = getDB();
    $row = $db->query(
        "SELECT
            (SELECT COUNT(*) FROM kasus)      AS total_kasus,
            (SELECT COUNT(*) FROM kriteria)   AS total_kriteria,
            (SELECT COUNT(*) FROM alternatif) AS total_alternatif,
            (SELECT COUNT(*) FROM penilaian)  AS total_penilaian"
    )->fetch();
    return $row ?: ['total_kasus' => 0, 'total_kriteria' => 0, 'total_alternatif' => 0, 'total_penilaian' => 0];
}

/**
 * Cek apakah kasus sudah siap dihitung TOPSIS
 * Mengembalikan array status setiap langkah
 */
function getKasusProgress(int $kasus_id): array {
    $db = getDB();

    $jmlKriteria   = (int)$db->query("SELECT COUNT(*) FROM kriteria   WHERE kasus_id = $kasus_id")->fetchColumn();
    $jmlAlternatif = (int)$db->query("SELECT COUNT(*) FROM alternatif WHERE kasus_id = $kasus_id")->fetchColumn();

    $jmlPenilaian  = 0;
    $totalRequired = $jmlKriteria * $jmlAlternatif;
    if ($totalRequired > 0) {
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM penilaian p
             INNER JOIN alternatif a ON a.id = p.alternatif_id
             INNER JOIN kriteria   k ON k.id = p.kriteria_id
             WHERE a.kasus_id = ? AND k.kasus_id = ?"
        );
        $stmt->execute([$kasus_id, $kasus_id]);
        $jmlPenilaian = (int)$stmt->fetchColumn();
    }

    return [
        'kriteria'   => $jmlKriteria   >= 2,
        'alternatif' => $jmlAlternatif >= 2,
        'penilaian'  => $totalRequired > 0 && $jmlPenilaian >= $totalRequired,
        'jml_kriteria'   => $jmlKriteria,
        'jml_alternatif' => $jmlAlternatif,
        'jml_penilaian'  => $jmlPenilaian,
        'total_required' => $totalRequired,
    ];
}
