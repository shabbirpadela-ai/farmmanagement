<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dove Haven Farms - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body class="bg-gradient-to-br from-green-600 to-teal-700 min-h-screen flex items-center justify-center font-['Inter']">
    <div id="alertsContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md mx-4">
        <div class="text-center mb-8">
            <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="egg" class="w-8 h-8 text-green-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Dove Haven Farms</h1>
            <p class="text-gray-600 mt-2">Farm Management Portal</p>
        </div>
        <form id="loginForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="username" autocomplete="username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none"
                    placeholder="Enter username" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" autocomplete="current-password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none"
                    placeholder="Enter password" required>
            </div>
            <button type="submit" id="loginBtn"
                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition duration-200 font-medium">
                Sign In
            </button>
            <p id="loginError" class="text-red-600 text-sm text-center hidden"></p>
        </form>
    </div>

    <script src="/assets/app.js"></script>
    <script>
        if (typeof lucide !== 'undefined') { lucide.createIcons(); }
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('loginBtn');
            const err = document.getElementById('loginError');
            err.classList.add('hidden');
            btn.disabled = true;
            btn.textContent = 'Signing in…';
            try {
                const res = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({
                        username: document.getElementById('username').value,
                        password: document.getElementById('password').value
                    })
                });
                const data = await res.json();
                if (data.ok) {
                    window.location.href = data.data.redirect || '/dashboard';
                } else {
                    err.textContent = data.error || 'Invalid credentials';
                    err.classList.remove('hidden');
                }
            } catch (_) {
                err.textContent = 'Server error. Please try again.';
                err.classList.remove('hidden');
            }
            btn.disabled = false;
            btn.textContent = 'Sign In';
        });
    </script>
</body>
</html>
