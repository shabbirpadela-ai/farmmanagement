<?php
use App\Core\Auth;
$user    = $user ?? Auth::user();
$pageKey = $page ?? 'dashboard';
$role    = $user['role'] ?? '';

$navItems = [
    ['page' => 'dashboard', 'icon' => 'layout-dashboard',  'label' => 'Dashboard',           'url' => '/dashboard'],
    ['page' => 'rearing',   'icon' => 'clipboard-list',    'label' => 'Rearing &amp; Production', 'url' => '/rearing'],
    ['page' => 'inventory', 'icon' => 'package',           'label' => 'Stock &amp; Feed',         'url' => '/inventory'],
    ['page' => 'crm',       'icon' => 'users',             'label' => 'Sales &amp; Purchases',    'url' => '/crm'],
    ['page' => 'crates',    'icon' => 'layers',            'label' => 'Crate Inventory',      'url' => '/crates'],
    ['page' => 'reports',   'icon' => 'file-text',         'label' => 'Reports',              'url' => '/reports'],
];
?>
<!-- Sidebar -->
<aside id="sidebar" class="bg-gray-900 text-white w-full md:w-64 flex-shrink-0 hidden md:flex flex-col transition-all duration-300">
    <div class="p-6 border-b border-gray-800">
        <div class="flex items-center gap-3">
            <div class="bg-green-500 p-2 rounded-lg">
                <i data-lucide="egg" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h2 class="font-bold text-lg">Dove Haven</h2>
                <p class="text-xs text-gray-400">Farm Portal</p>
            </div>
        </div>
    </div>
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        <?php foreach ($navItems as $item): ?>
        <a href="<?= htmlspecialchars($item['url']) ?>"
           class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition text-left <?= $pageKey === $item['page'] ? 'bg-gray-800' : '' ?>"
           data-page="<?= htmlspecialchars($item['page']) ?>">
            <i data-lucide="<?= htmlspecialchars($item['icon']) ?>" class="w-5 h-5"></i>
            <span><?= $item['label'] ?></span>
        </a>
        <?php endforeach; ?>

        <?php if ($role === 'admin'): ?>
        <div class="pt-4 border-t border-gray-800 mt-4">
            <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Administration</p>
            <a href="/admin"
               class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition text-left <?= $pageKey === 'admin' ? 'bg-gray-800' : '' ?>"
               data-page="admin">
                <i data-lucide="shield" class="w-5 h-5"></i>
                <span>Admin Portal</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>
    <div class="p-4 border-t border-gray-800">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-lg">
                <?= htmlspecialchars(strtoupper(substr($user['name'] ?? 'U', 0, 1))) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate"><?= htmlspecialchars($user['name'] ?? '') ?></p>
                <p class="text-xs text-gray-400 truncate"><?= htmlspecialchars(str_replace('_', ' ', ucwords($user['role'] ?? '', '_'))) ?></p>
            </div>
        </div>
        <button onclick="doLogout()" class="w-full flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-800 transition text-red-400">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            <span>Logout</span>
        </button>
    </div>
</aside>
