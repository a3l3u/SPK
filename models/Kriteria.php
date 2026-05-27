<?php
/**
 * Model: Kriteria
 * CRUD untuk tabel kriteria
 */

function getKriteriaByKasusId(int $kasus_id): array {
    $db   = getDB();
    $stmt = $db->prepare(
        "SELECT * FROM kriteria WHERE kasus_id = ? ORDER BY id ASC"
    );
    $stmt->execute([$kasus_id]);
    return $stmt->fetchAll();
}

function getKriteriaById(int $id): array|false {
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM kriteria WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createKriteria(array $data): int {
    $db   = getDB();
    $stmt = $db->prepare(
        "INSERT INTO kriteria (kasus_id, nama, bobot, tipe) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([
        (int)$data['kasus_id'],
        trim($data['nama']),
        (float)$data['bobot'],
        in_array($data['tipe'], ['benefit', 'cost']) ? $data['tipe'] : 'benefit',
    ]);
    return (int)$db->lastInsertId();
}

function updateKriteria(int $id, array $data): bool {
    $db   = getDB();
    $stmt = $db->prepare(
        "UPDATE kriteria SET nama = ?, bobot = ?, tipe = ? WHERE id = ?"
    );
    return $stmt->execute([
        trim($data['nama']),
        (float)$data['bobot'],
        in_array($data['tipe'], ['benefit', 'cost']) ? $data['tipe'] : 'benefit',
        $id,
    ]);
}

function deleteKriteria(int $id): bool {
    $db   = getDB();
    $stmt = $db->prepare("DELETE FROM kriteria WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Menghitung total bobot untuk suatu kasus
 */
function getTotalBobot(int $kasus_id): float {
    $db   = getDB();
    $stmt = $db->prepare(
        "SELECT COALESCE(SUM(bobot), 0) FROM kriteria WHERE kasus_id = ?"
    );
    $stmt->execute([$kasus_id]);
    return (float)$stmt->fetchColumn();
}

/**
 * Mendapatkan bobot ternormalisasi (w_j = bobot_j / total_bobot)
 * Mengembalikan array kriteria dengan field 'bobot_normal' tambahan
 */
function getNormalizedBobot(int $kasus_id): array {
    $list  = getKriteriaByKasusId($kasus_id);
    $total = array_sum(array_column($list, 'bobot'));
    foreach ($list as &$k) {
        $k['bobot_normal'] = ($total > 0) ? (float)$k['bobot'] / $total : 0;
    }
    return $list;
}
