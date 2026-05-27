<?php
/**
 * SPK TOPSIS - Front Controller (Router)
 * Sistem Pendukung Keputusan berbasis Metode TOPSIS
 */

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session & base config
session_start();

define('BASE_PATH', __DIR__);
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']));

// Autoload config and models
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/models/Kasus.php';
require_once BASE_PATH . '/models/Kriteria.php';
require_once BASE_PATH . '/models/Alternatif.php';
require_once BASE_PATH . '/models/Penilaian.php';

// Load controllers
require_once BASE_PATH . '/controllers/KasusController.php';
require_once BASE_PATH . '/controllers/KriteriaController.php';
require_once BASE_PATH . '/controllers/AlternatifController.php';
require_once BASE_PATH . '/controllers/PenilaianController.php';
require_once BASE_PATH . '/controllers/TopsisController.php';

// Helper: redirect
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Helper: flash message
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ----- ROUTING -----
$page   = $_GET['page']   ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id     = isset($_GET['id'])       ? (int)$_GET['id']       : null;
$kasus_id = isset($_GET['kasus_id']) ? (int)$_GET['kasus_id'] : null;

// POST routing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postPage   = $_POST['_page']   ?? $page;
    $postAction = $_POST['_action'] ?? $action;

    switch ($postPage) {
        case 'kasus':
            $ctrl = new KasusController();
            if ($postAction === 'store')  { $ctrl->store();  exit; }
            if ($postAction === 'update') { $ctrl->update(); exit; }
            if ($postAction === 'delete') { $ctrl->delete(); exit; }
            break;

        case 'kriteria':
            $ctrl = new KriteriaController();
            if ($postAction === 'store')  { $ctrl->store();  exit; }
            if ($postAction === 'update') { $ctrl->update(); exit; }
            if ($postAction === 'delete') { $ctrl->delete(); exit; }
            break;

        case 'alternatif':
            $ctrl = new AlternatifController();
            if ($postAction === 'store')  { $ctrl->store();  exit; }
            if ($postAction === 'update') { $ctrl->update(); exit; }
            if ($postAction === 'delete') { $ctrl->delete(); exit; }
            break;

        case 'penilaian':
            $ctrl = new PenilaianController();
            if ($postAction === 'store')  { $ctrl->store();  exit; }
            break;
    }
}

// GET routing
switch ($page) {
    case 'dashboard':
    default:
        $ctrl = new KasusController();
        $ctrl->dashboard();
        break;

    case 'kasus':
        $ctrl = new KasusController();
        switch ($action) {
            case 'create': $ctrl->create(); break;
            case 'show':   $ctrl->show($id); break;
            case 'edit':   $ctrl->edit($id); break;
            default:       $ctrl->index(); break;
        }
        break;

    case 'kriteria':
        $ctrl = new KriteriaController();
        $ctrl->index($kasus_id);
        break;

    case 'alternatif':
        $ctrl = new AlternatifController();
        $ctrl->index($kasus_id);
        break;

    case 'penilaian':
        $ctrl = new PenilaianController();
        $ctrl->index($kasus_id);
        break;

    case 'hasil':
        $ctrl = new TopsisController();
        $ctrl->index($kasus_id);
        break;
}
