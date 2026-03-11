<?php
use App\Core\Auth;
$user      = $user ?? Auth::user();
$pageKey   = $page ?? 'dashboard';
$csrfToken = Auth::csrfToken();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dove Haven Farms - Management Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken) ?>">
</head>
<body class="bg-gray-50 font-['Inter'] text-gray-800" data-page="<?= htmlspecialchars($pageKey) ?>"
      data-user='<?= htmlspecialchars(json_encode($user), ENT_QUOTES) ?>'>

    <!-- Main Application -->
    <div id="app" class="min-h-screen flex flex-col md:flex-row">
        <?php require __DIR__ . '/sidebar.php'; ?>

        <!-- Mobile Header -->
        <div class="md:hidden bg-gray-900 text-white p-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="egg" class="w-6 h-6 text-green-500"></i>
                <span class="font-bold">Dove Haven</span>
            </div>
            <button onclick="toggleMobileMenu()" class="p-2">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-gray-800 text-white p-4 space-y-2">
            <a href="/dashboard" class="block w-full text-left px-4 py-3 rounded-lg hover:bg-gray-700">Dashboard</a>
            <a href="/rearing"   class="block w-full text-left px-4 py-3 rounded-lg hover:bg-gray-700">Rearing &amp; Production</a>
            <a href="/inventory" class="block w-full text-left px-4 py-3 rounded-lg hover:bg-gray-700">Stock &amp; Feed</a>
            <a href="/crm"       class="block w-full text-left px-4 py-3 rounded-lg hover:bg-gray-700">Sales &amp; Purchases</a>
            <a href="/crates"    class="block w-full text-left px-4 py-3 rounded-lg hover:bg-gray-700">Crate Inventory</a>
            <a href="/reports"   class="block w-full text-left px-4 py-3 rounded-lg hover:bg-gray-700">Reports</a>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
            <a href="/admin"     class="block w-full text-left px-4 py-3 rounded-lg hover:bg-gray-700">Admin Portal</a>
            <?php endif; ?>
            <button onclick="doLogout()" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-700 text-red-400">Logout</button>
        </div>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8">
            <!-- Alerts Container -->
            <div id="alertsContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>
