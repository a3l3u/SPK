<?php
/**
 * Controller: TOPSIS
 * Algoritma TOPSIS 7 langkah + render halaman hasil
 */

class TopsisController {

    public function index(?int $kasus_id): void {
        if (!$kasus_id) { redirect('index.php?page=kasus'); }

        $kasus = getKasusById($kasus_id);
        if (!$kasus) {
            setFlash('error', 'Kasus tidak ditemukan.');
            redirect('index.php?page=kasus');
        }

        $flash        = getFlash();
        $topsis_result = null;
        $error        = null;

        if (!isPenilaianComplete($kasus_id)) {
            $error = 'Data belum lengkap. Pastikan sudah ada minimal 2 kriteria, 2 alternatif, dan semua penilaian telah diisi.';
        } else {
            $topsis_result = self::calculate($kasus_id);
            if (!$topsis_result) {
                $error = 'Gagal menghitung TOPSIS. Periksa kembali data penilaian.';
            }
        }

        $title       = 'Hasil Ranking TOPSIS';
        $active_page = 'hasil';

        require BASE_PATH . '/views/layouts/header.php';
        require BASE_PATH . '/views/hasil/index.php';
        require BASE_PATH . '/views/layouts/footer.php';
    }

    /**
     * Hitung TOPSIS — mengembalikan array hasil lengkap
     * 7 Langkah: Normalisasi → Bobot → A+ A- → D+ D- → Preferensi → Ranking
     */
    public static function calculate(int $kasus_id): ?array {
        $kriteria   = getKriteriaByKasusId($kasus_id);
        $alternatif = getAlternatifByKasusId($kasus_id);
        $matrix     = getPenilaianMatrix($kasus_id);

        if (count($kriteria) < 2 || count($alternatif) < 2 || empty($matrix)) {
            return null;
        }

        $n = count($alternatif); // jumlah alternatif
        $m = count($kriteria);   // jumlah kriteria

        // Normalisasi bobot
        $totalBobot = array_sum(array_column($kriteria, 'bobot'));
        $bobot_norm = [];
        foreach ($kriteria as $j => $krit) {
            $bobot_norm[$j] = ($totalBobot > 0) ? (float)$krit['bobot'] / $totalBobot : 0;
        }

        // ── STEP 1: Matriks Keputusan X ──────────────────────────────
        $X = [];
        foreach ($alternatif as $i => $alt) {
            foreach ($kriteria as $j => $krit) {
                $X[$i][$j] = isset($matrix[$alt['id']][$krit['id']])
                    ? (float)$matrix[$alt['id']][$krit['id']]
                    : 0.0;
            }
        }

        // ── STEP 2: Normalisasi Matriks R (Euclidean) ────────────────
        // r_ij = x_ij / sqrt( Σ x_ij² )
        $R = [];
        for ($j = 0; $j < $m; $j++) {
            $sumSq = 0.0;
            for ($i = 0; $i < $n; $i++) {
                $sumSq += $X[$i][$j] ** 2;
            }
            $sqrtSum = ($sumSq > 0) ? sqrt($sumSq) : 1.0;
            for ($i = 0; $i < $n; $i++) {
                $R[$i][$j] = $X[$i][$j] / $sqrtSum;
            }
        }

        // ── STEP 3: Matriks Ternormalisasi Terbobot V ────────────────
        // v_ij = w_j × r_ij
        $V = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $m; $j++) {
                $V[$i][$j] = $bobot_norm[$j] * $R[$i][$j];
            }
        }

        // ── STEP 4: Solusi Ideal Positif A+ dan Negatif A- ───────────
        $Aplus  = [];
        $Aminus = [];
        for ($j = 0; $j < $m; $j++) {
            $col = array_column($V, $j);
            if ($kriteria[$j]['tipe'] === 'benefit') {
                $Aplus[$j]  = max($col);  // benefit → max = ideal +
                $Aminus[$j] = min($col);  // benefit → min = ideal -
            } else {
                $Aplus[$j]  = min($col);  // cost → min = ideal +
                $Aminus[$j] = max($col);  // cost → max = ideal -
            }
        }

        // ── STEP 5 & 6: Jarak ke A+ (D+) dan A- (D-) ─────────────────
        // D+_i = sqrt( Σ (v_ij - A+_j)² )
        // D-_i = sqrt( Σ (v_ij - A-_j)² )
        $Dplus  = [];
        $Dminus = [];
        for ($i = 0; $i < $n; $i++) {
            $sumPlus = $sumMinus = 0.0;
            for ($j = 0; $j < $m; $j++) {
                $sumPlus  += ($V[$i][$j] - $Aplus[$j])  ** 2;
                $sumMinus += ($V[$i][$j] - $Aminus[$j]) ** 2;
            }
            $Dplus[$i]  = sqrt($sumPlus);
            $Dminus[$i] = sqrt($sumMinus);
        }

        // ── STEP 7: Nilai Preferensi C ────────────────────────────────
        // C_i = D-_i / (D+_i + D-_i)  →  semakin tinggi = semakin baik
        $C = [];
        for ($i = 0; $i < $n; $i++) {
            $denom = $Dplus[$i] + $Dminus[$i];
            $C[$i] = ($denom > 0) ? $Dminus[$i] / $denom : 0.0;
        }

        // ── Susun hasil dengan ranking ─────────────────────────────────
        $results = [];
        for ($i = 0; $i < $n; $i++) {
            $results[] = [
                'alternatif' => $alternatif[$i],
                'nilai_x'    => array_map(fn($v) => round($v, 4), $X[$i]),
                'nilai_r'    => array_map(fn($v) => round($v, 6), $R[$i]),
                'nilai_v'    => array_map(fn($v) => round($v, 6), $V[$i]),
                'D_plus'     => round($Dplus[$i],  6),
                'D_minus'    => round($Dminus[$i], 6),
                'preferensi' => round($C[$i],      6),
            ];
        }

        // Sort descending berdasarkan nilai preferensi
        usort($results, fn($a, $b) => $b['preferensi'] <=> $a['preferensi']);

        // Tambahkan rank
        foreach ($results as $idx => &$res) {
            $res['rank'] = $idx + 1;
        }
        unset($res);

        return [
            'kriteria'    => $kriteria,
            'alternatif'  => $alternatif,
            'bobot_norm'  => $bobot_norm,
            'matrix_x'   => $X,
            'matrix_r'   => $R,
            'matrix_v'   => $V,
            'A_plus'      => array_map(fn($v) => round($v, 6), $Aplus),
            'A_minus'     => array_map(fn($v) => round($v, 6), $Aminus),
            'D_plus'      => array_map(fn($v) => round($v, 6), $Dplus),
            'D_minus'     => array_map(fn($v) => round($v, 6), $Dminus),
            'results'     => $results,
            'total_bobot' => $totalBobot,
        ];
    }
}
