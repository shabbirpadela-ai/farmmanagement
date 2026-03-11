-- Dove Haven Farms – Seed Data
-- Run AFTER schema.sql.
-- Creates the default admin user and sample inventory items.
--
-- ⚠️  IMPORTANT: Do NOT use this file as-is in production.
--     Use seed.php instead to set a strong custom password:
--
--     ADMIN_USER=admin ADMIN_PASS=YourStrongPassword123 php database/seed.php
--
-- The INSERT below uses a placeholder hash that will NOT allow login.
-- You MUST run seed.php or manually update the password_hash column.

SET NAMES utf8mb4;

-- ─── Default admin user (PLACEHOLDER – password login disabled until seed.php is run) ─
INSERT INTO `users` (`username`, `full_name`, `email`, `role`, `password_hash`, `status`)
VALUES (
    'admin',
    'Administrator',
    'admin@dovehavenfarms.com',
    'admin',
    '$2y$10$PLACEHOLDER_RUN_seed.php_TO_SET_REAL_PASSWORD_HASH_xxxxx',
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
