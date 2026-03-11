-- Dove Haven Farms – Seed Data
-- Run AFTER schema.sql.
-- Creates the default admin user and sample inventory items.
-- Change the password hash below or use seed.php for a dynamic hash.

SET NAMES utf8mb4;

-- ─── Default admin user ───────────────────────────────────────────────────────
-- Username: admin
-- Password: Admin@2024  (change immediately after first login!)
-- Hash generated with PHP: password_hash('Admin@2024', PASSWORD_BCRYPT)
INSERT INTO `users` (`username`, `full_name`, `email`, `role`, `password_hash`, `status`)
VALUES (
    'admin',
    'Administrator',
    'admin@dovehavenfarms.com',
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password (use seed.php for custom)
    'active'
) ON DUPLICATE KEY UPDATE `id` = `id`;

-- ─── Default feed ingredients ────────────────────────────────────────────────
INSERT INTO `inventory_items` (`name`, `quantity`, `purchased`, `used`, `supplier`, `unit_cost`) VALUES
    ('Corn/Maize',    2500, 5000, 2500, 'GrainCo Ltd',      0.35),
    ('Soybean Meal',  1800, 3000, 1200, 'ProteinFeeds Inc',  0.45),
    ('Wheat',         1200, 2000,  800, 'GrainCo Ltd',       0.32),
    ('Layer Feed',    3500, 6000, 2500, 'In-House',          0.52),
    ('Limestone',      500, 1000,  500, 'MineralCo',         0.15),
    ('Salt',           100,  200,  100, 'General Store',     0.10)
ON DUPLICATE KEY UPDATE `id` = `id`;
