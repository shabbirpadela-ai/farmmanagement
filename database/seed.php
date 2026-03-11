<?php
/**
 * database/seed.php
 *
 * Creates the first admin user with a custom password from the command line
 * or from .env values. Run this ONCE after importing schema.sql.
 *
 * Usage (CLI):
 *   php database/seed.php
 *
 *   Or with env vars:
 *   ADMIN_USER=admin ADMIN_PASS=MySecret123 php database/seed.php
 */

declare(strict_types=1);

$rootDir = dirname(__DIR__);

require_once $rootDir . '/config/env.php';
require_once $rootDir . '/config/database.php';

// Credentials – read from env or use defaults
$username  = env('ADMIN_USER') ?? 'admin';
$fullName  = env('ADMIN_NAME') ?? 'Administrator';
$email     = env('ADMIN_EMAIL') ?? 'admin@dovehavenfarms.com';
$password  = env('ADMIN_PASS') ?? 'Admin@2024';

if (strlen($password) < 8) {
    echo "ERROR: ADMIN_PASS must be at least 8 characters.\n";
    exit(1);
}

// Enforce basic password complexity for admin account
if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
    echo "ERROR: ADMIN_PASS must contain at least one uppercase letter and one number.\n";
    echo "       Example: ADMIN_PASS=Admin@2024 php database/seed.php\n";
    exit(1);
}

$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $db = connectDB();

    // Insert or update admin
    $stmt = $db->prepare(
        'INSERT INTO users (username, full_name, email, role, password_hash, status)
         VALUES (?, ?, ?, \'admin\', ?, \'active\')
         ON DUPLICATE KEY UPDATE
             full_name     = VALUES(full_name),
             email         = VALUES(email),
             password_hash = VALUES(password_hash),
             status        = \'active\''
    );
    $stmt->execute([$username, $fullName, $email, $hash]);

    echo "✓ Admin user '{$username}' created/updated successfully.\n";
    echo "  Login URL : /login\n";
    echo "  Username  : {$username}\n";
    echo "  Password  : {$password}\n";
    echo "\nChange the password after first login!\n";

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
