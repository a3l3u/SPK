<?php
/**
 * Konfigurasi koneksi database MySQL via PDO
 * SPK TOPSIS - Sistem Pendukung Keputusan
 */

define('DB_HOST',    'localhost');
define('DB_NAME',    'spk_topsis');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Singleton PDO connection
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST, DB_NAME, DB_CHARSET
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die('<div style="font-family:monospace;background:#1e1e2e;color:#f38ba8;padding:2rem;border-radius:8px;margin:2rem;">
                <strong>❌ Database Connection Error</strong><br><br>
                ' . htmlspecialchars($e->getMessage()) . '<br><br>
                <em>Pastikan XAMPP MySQL sudah berjalan dan database <strong>spk_topsis</strong> sudah diimport.</em>
            </div>');
        }
    }
    return $pdo;
}
