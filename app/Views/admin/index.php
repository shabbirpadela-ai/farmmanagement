<!-- Admin Portal Page -->
<div id="page-admin" class="page-content space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Admin Portal</h1>
            <p class="text-gray-600">Manage employees, system settings, and domain configuration</p>
        </div>
        <div class="flex gap-2">
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">Admin Only</span>
            <button onclick="openModal('employeeModal')" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Add Employee</span>
            </button>
        </div>
    </div>

    <!-- Admin Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <p class="text-2xl font-bold text-gray-900" id="totalEmployees">0</p>
            <p class="text-sm text-gray-600">Total Employees</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <p class="text-2xl font-bold text-green-600" id="activeEmployees">0</p>
            <p class="text-sm text-gray-600">Active</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <p class="text-2xl font-bold text-blue-600" id="adminCount">0</p>
            <p class="text-sm text-gray-600">Admins</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <p class="text-2xl font-bold text-orange-600" id="inactiveCount">0</p>
            <p class="text-sm text-gray-600">Inactive</p>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Employee Management</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Username</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="employeesTableBody">
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div id="employeeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Add Employee / User</h3>
            <button onclick="closeModal('employeeModal')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="employeeForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="full_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required autocomplete="off">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                    <option value="farm_manager">Farm Manager</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="sales_manager">Sales Manager</option>
                    <option value="employee">Employee</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required autocomplete="new-password" minlength="8">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-medium">Create User</button>
                <button type="button" onclick="closeModal('employeeModal')" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>
