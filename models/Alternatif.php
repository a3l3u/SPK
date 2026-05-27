<?php
/**
 * Model: Alternatif
 * CRUD untuk tabel alternatif
 */

function getAlternatifByKasusId(int $kasus_id): array {
    $db   = getDB();
    $stmt = $db->prepare(
        "SELECT * FROM alternatif WHERE kasus_id = ? ORDER BY id ASC"
    );
    $stmt->execute([$kasus_id]);
    return $stmt->fetchAll();
}

function getAlternatifById(int $id): array|false {
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM alternatif WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createAlternatif(array $data): int {
    $db   = getDB();
    $stmt = $db->prepare(
        "INSERT INTO alternatif (kasus_id, nama, alamat, keterangan) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([
        (int)$data['kasus_id'],
        trim($data['nama']),
        trim($data['alamat']     ?? ''),
        trim($data['keterangan'] ?? ''),
    ]);
    return (int)$db->lastInsertId();
}

function updateAlternatif(int $id, array $data): bool {
    $db   = getDB();
    $stmt = $db->prepare(
        "UPDATE alternatif SET nama = ?, alamat = ?, keterangan = ? WHERE id = ?"
    );
    return $stmt->execute([
        trim($data['nama']),
        trim($data['alamat']     ?? ''),
        trim($data['keterangan'] ?? ''),
        $id,
    ]);
}

function deleteAlternatif(int $id): bool {
    $db   = getDB();
    $stmt = $db->prepare("DELETE FROM alternatif WHERE id = ?");
    return $stmt->execute([$id]);
}
