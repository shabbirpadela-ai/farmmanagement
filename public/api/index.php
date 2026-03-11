<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__, 2));

// Always return JSON
header('Content-Type: application/json; charset=utf-8');

// Load environment
require_once BASE_PATH . '/config/env.php';

// Autoload app classes
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
use App\Controllers\Api\AuthApiController;
use App\Controllers\Api\BootstrapApiController;
use App\Controllers\Api\ProductionApiController;
use App\Controllers\Api\InventoryApiController;
use App\Controllers\Api\CrmApiController;
use App\Controllers\Api\CratesApiController;
use App\Controllers\Api\ReportsApiController;
use App\Controllers\Api\AdminApiController;

// Start session (needed for auth checks)
Auth::startSession();

$router = new Router();

// ── Auth ─────────────────────────────────────────────────────────────────────
$router->post('/api/auth/login',  [AuthApiController::class, 'login']);
$router->post('/api/auth/logout', [AuthApiController::class, 'logout']);

// ── Bootstrap ────────────────────────────────────────────────────────────────
$router->get('/api/bootstrap', [BootstrapApiController::class, 'index']);

// ── Production ───────────────────────────────────────────────────────────────
$router->get('/api/production',         [ProductionApiController::class, 'index']);
$router->post('/api/production',        [ProductionApiController::class, 'store']);
$router->post('/api/production/delete/{id}', [ProductionApiController::class, 'destroy']);
$router->get('/api/production/summary', [ProductionApiController::class, 'summary']);
$router->get('/api/production/weekly',  [ProductionApiController::class, 'weekly']);

// ── Inventory ────────────────────────────────────────────────────────────────
$router->get('/api/inventory',              [InventoryApiController::class, 'index']);
$router->get('/api/inventory/transactions', [InventoryApiController::class, 'transactions']);
$router->post('/api/inventory/purchase',    [InventoryApiController::class, 'purchase']);
$router->post('/api/inventory/usage',       [InventoryApiController::class, 'usage']);
$router->post('/api/inventory/item',        [InventoryApiController::class, 'createItem']);

// ── CRM ──────────────────────────────────────────────────────────────────────
$router->get('/api/crm/customers',              [CrmApiController::class, 'customers']);
$router->post('/api/crm/customers',             [CrmApiController::class, 'createCustomer']);
$router->post('/api/crm/customers/update/{id}', [CrmApiController::class, 'updateCustomer']);
$router->post('/api/crm/customers/delete/{id}', [CrmApiController::class, 'deleteCustomer']);
$router->get('/api/crm/orders',                 [CrmApiController::class, 'orders']);
$router->post('/api/crm/orders',                [CrmApiController::class, 'createOrder']);
$router->get('/api/crm/orders/{id}/items',      [CrmApiController::class, 'orderItems']);
$router->post('/api/crm/orders/{id}/payment',   [CrmApiController::class, 'recordPayment']);
$router->get('/api/crm/purchases',              [CrmApiController::class, 'purchases']);
$router->post('/api/crm/purchases',             [CrmApiController::class, 'createPurchase']);
$router->post('/api/crm/purchases/delete/{id}', [CrmApiController::class, 'deletePurchase']);
$router->get('/api/crm/stats',                  [CrmApiController::class, 'stats']);

// ── Crates ───────────────────────────────────────────────────────────────────
$router->get('/api/crates',          [CratesApiController::class, 'status']);
$router->get('/api/crates/movements',[CratesApiController::class, 'movements']);
$router->post('/api/crates/adjust',  [CratesApiController::class, 'adjust']);

// ── Reports ──────────────────────────────────────────────────────────────────
$router->get('/api/reports/generate', [ReportsApiController::class, 'generate']);

// ── Admin ────────────────────────────────────────────────────────────────────
$router->get('/api/admin/employees',              [AdminApiController::class, 'employees']);
$router->post('/api/admin/employees',             [AdminApiController::class, 'createEmployee']);
$router->post('/api/admin/employees/{id}/toggle', [AdminApiController::class, 'toggleStatus']);

// Dispatch
try {
    $router->dispatch();
} catch (\Throwable $e) {
    http_response_code(500);
    $debug = env('APP_ENV') === 'development';
    echo json_encode([
        'ok'    => false,
        'error' => $debug ? $e->getMessage() : 'Internal server error',
    ]);
}
