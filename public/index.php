<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// Load environment and session
require_once BASE_PATH . '/config/env.php';

// Autoload app classes (simple PSR-4 compatible)
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $base   = BASE_PATH . '/app/';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file     = $base . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Auth;
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\RearingController;
use App\Controllers\InventoryController;
use App\Controllers\CrmController;
use App\Controllers\CratesController;
use App\Controllers\ReportsController;
use App\Controllers\AdminController;

// Start session
Auth::startSession();

$router = new Router();

// ── Page routes ──────────────────────────────────────────────────────────────
$router->get('/',          [DashboardController::class, 'index']);
$router->get('/login',     [AuthController::class, 'showLogin']);
$router->get('/logout',    [AuthController::class, 'logout']);
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/rearing',   [RearingController::class,   'index']);
$router->get('/inventory', [InventoryController::class,  'index']);
$router->get('/crm',       [CrmController::class,        'index']);
$router->get('/crates',    [CratesController::class,     'index']);
$router->get('/reports',   [ReportsController::class,    'index']);
$router->get('/admin',     [AdminController::class,      'index']);

$router->dispatch();