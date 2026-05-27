-- ============================================================
-- SPK TOPSIS - Database Schema
-- Sistem Pendukung Keputusan berbasis Metode TOPSIS
-- ============================================================

CREATE DATABASE IF NOT EXISTS spk_topsis
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE spk_topsis;

-- ------------------------------------------------------------
-- Tabel: kasus
-- Menyimpan setiap kasus pemilihan tempat
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS kasus (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nama        VARCHAR(255) NOT NULL,
  deskripsi   TEXT,
  tipe_tempat VARCHAR(100),
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: kriteria
-- Kriteria penilaian untuk setiap kasus
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS kriteria (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  kasus_id  INT NOT NULL,
  nama      VARCHAR(255) NOT NULL,
  bobot     DECIMAL(10,4) NOT NULL DEFAULT 1,
  tipe      ENUM('benefit','cost') NOT NULL DEFAULT 'benefit',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (kasus_id) REFERENCES kasus(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: alternatif
-- Pilihan tempat untuk setiap kasus
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS alternatif (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  kasus_id   INT NOT NULL,
  nama       VARCHAR(255) NOT NULL,
  alamat     VARCHAR(500),
  keterangan TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (kasus_id) REFERENCES kasus(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: penilaian
-- Nilai setiap alternatif terhadap setiap kriteria (skala 1-10)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS penilaian (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  alternatif_id INT NOT NULL,
  kriteria_id   INT NOT NULL,
  nilai         DECIMAL(5,2) NOT NULL DEFAULT 5,
  UNIQUE KEY unique_penilaian (alternatif_id, kriteria_id),
  FOREIGN KEY (alternatif_id) REFERENCES alternatif(id) ON DELETE CASCADE,
  FOREIGN KEY (kriteria_id)   REFERENCES kriteria(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATA CONTOH: Kasus pemilihan tempat nugas
-- ============================================================
INSERT INTO kasus (nama, deskripsi, tipe_tempat) VALUES
('Pilih Tempat Nugas Terbaik',
 'Mencari tempat yang nyaman untuk mengerjakan tugas kuliah dengan fasilitas lengkap.',
 'Cafe / Coworking Space');

-- Kriteria
INSERT INTO kriteria (kasus_id, nama, bobot, tipe) VALUES
(1, 'Kualitas WiFi',       30, 'benefit'),
(1, 'Keterjangkauan Harga',25, 'benefit'),
(1, 'Kenyamanan',          20, 'benefit'),
(1, 'Tingkat Kebisingan',  15, 'cost'),
(1, 'Jarak dari Kampus',   10, 'cost');

-- Alternatif
INSERT INTO alternatif (kasus_id, nama, alamat, keterangan) VALUES
(1, 'Kopi Kenangan',          'Jl. Sudirman No. 10',      'Cafe populer dengan WiFi cepat, ramai sore hari'),
(1, 'Perpustakaan Kota',      'Jl. Merdeka No. 5',        'Tenang, ber-AC, WiFi gratis, buka sampai jam 8 malam'),
(1, 'Co-Space Hub',           'Jl. Gatot Subroto No. 22', 'Coworking modern, fasilitas lengkap, ada ruang meeting'),
(1, 'Warung Kopi Pak Budi',   'Jl. Pahlawan No. 3',       'Murah meriah, dekat kampus, suasana santai');

-- Penilaian (alt_id, krit_id, nilai)
-- Kopi Kenangan:        WiFi=8, Harga=5, Kenyamanan=7, Kebisingan=6, Jarak=6
-- Perpustakaan Kota:    WiFi=5, Harga=10,Kenyamanan=8, Kebisingan=9, Jarak=7
-- Co-Space Hub:         WiFi=9, Harga=4, Kenyamanan=9, Kebisingan=3, Jarak=5
-- Warung Kopi Pak Budi: WiFi=6, Harga=9, Kenyamanan=5, Kebisingan=5, Jarak=9
INSERT INTO penilaian (alternatif_id, kriteria_id, nilai) VALUES
(1,1,8),(1,2,5),(1,3,7),(1,4,6),(1,5,6),
(2,1,5),(2,2,10),(2,3,8),(2,4,9),(2,5,7),
(3,1,9),(3,2,4),(3,3,9),(3,4,3),(3,5,5),
(4,1,6),(4,2,9),(4,3,5),(4,4,5),(4,5,9);
