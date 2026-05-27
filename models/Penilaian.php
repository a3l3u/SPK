<?php
/**
 * Model: Penilaian
 * Operasi pada tabel penilaian (matriks keputusan)
 */

/**
 * Ambil semua penilaian untuk sebuah kasus
 * Return: array 2D [ alternatif_id ][ kriteria_id ] = nilai
 */
function getPenilaianMatrix(int $kasus_id): array {
    $db   = getDB();
    $stmt = $db->prepare(
        "SELECT p.alternatif_id, p.kriteria_id, p.nilai
         FROM penilaian p
         INNER JOIN alternatif a ON a.id = p.alternatif_id
         INNER JOIN kriteria   k ON k.id = p.kriteria_id
         WHERE a.kasus_id = ? AND k.kasus_id = ?"
    );
    $stmt->execute([$kasus_id, $kasus_id]);
    $rows = $stmt->fetchAll();

    $matrix = [];
    foreach ($rows as $row) {
        $matrix[(int)$row['alternatif_id']][(int)$row['kriteria_id']] = (float)$row['nilai'];
    }
    return $matrix;
}

/**
 * Simpan satu nilai penilaian (INSERT or UPDATE)
 */
function savePenilaian(int $alternatif_id, int $kriteria_id, float $nilai): bool {
    // Clamp nilai ke range 1-10
    $nilai = max(1, min(10, $nilai));
    $db    = getDB();
    $stmt  = $db->prepare(
        "INSERT INTO penilaian (alternatif_id, kriteria_id, nilai)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)"
    );
    return $stmt->execute([$alternatif_id, $kriteria_id, $nilai]);
}

/**
 * Simpan semua penilaian dari form matriks sekaligus
 * $data = array dari POST: key = "nilai_{alt_id}_{krit_id}", value = nilai
 */
function saveAllPenilaian(int $kasus_id, array $data): int {
    $saved = 0;
    foreach ($data as $key => $nilai) {
        // Key format: nilai_{alternatif_id}_{kriteria_id}
        if (preg_match('/^nilai_(\d+)_(\d+)$/', $key, $m)) {
            $alt_id  = (int)$m[1];
            $krit_id = (int)$m[2];
            $val     = (float)$nilai;
            if (savePenilaian($alt_id, $krit_id, $val)) {
                $saved++;
            }
        }
    }
    return $saved;
}

/**
 * Cek apakah matriks sudah lengkap
 */
function isPenilaianComplete(int $kasus_id): bool {
    $db     = getDB();
    $stmt   = $db->prepare(
        "SELECT COUNT(DISTINCT kr.id) AS jml_kriteria,
                COUNT(DISTINCT al.id) AS jml_alternatif
         FROM kriteria kr, alternatif al
         WHERE kr.kasus_id = ? AND al.kasus_id = ?"
    );
    $stmt->execute([$kasus_id, $kasus_id]);
    $row = $stmt->fetch();
    if (!$row || $row['jml_kriteria'] < 2 || $row['jml_alternatif'] < 2) return false;

    $required = (int)$row['jml_kriteria'] * (int)$row['jml_alternatif'];

    $stmt2 = $db->prepare(
        "SELECT COUNT(*) FROM penilaian p
         INNER JOIN alternatif a ON a.id = p.alternatif_id
         INNER JOIN kriteria   k ON k.id = p.kriteria_id
         WHERE a.kasus_id = ? AND k.kasus_id = ?"
    );
    $stmt2->execute([$kasus_id, $kasus_id]);
    $filled = (int)$stmt2->fetchColumn();

    return $filled >= $required;
}
