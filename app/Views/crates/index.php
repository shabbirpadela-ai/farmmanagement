<!-- Crate Inventory Page -->
<div id="page-crates" class="page-content space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Crate Inventory Management</h1>
            <p class="text-gray-600">Real-time crate tracking from production to sales</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openModal('crateAdjustmentModal')" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i data-lucide="settings" class="w-4 h-4"></i>
                <span>Adjust Stock</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold mb-2">Available Crates</h3>
            <p class="text-4xl font-bold text-green-600" id="availableCrates">0</p>
            <p class="text-sm text-gray-600 mt-2">Ready for sale</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold mb-2">Sold Today</h3>
            <p class="text-4xl font-bold text-blue-600" id="soldCratesToday">0</p>
            <p class="text-sm text-gray-600 mt-2">From today's orders</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold mb-2">Damaged/Lost</h3>
            <p class="text-4xl font-bold text-red-600" id="damagedCrates">0</p>
            <p class="text-sm text-gray-600 mt-2">Needs replacement</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold mb-4">Crate Movement Log</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Date/Time</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Quantity</th>
                        <th class="px-4 py-3">Source/Destination</th>
                        <th class="px-4 py-3">Balance</th>
                    </tr>
                </thead>
                <tbody id="crateMovementTable">
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Loading…</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Crate Adjustment Modal -->
<div id="crateAdjustmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Adjust Crate Stock</h3>
            <button onclick="closeModal('crateAdjustmentModal')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="crateAdjustForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment Type</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                    <option value="production">Production (add)</option>
                    <option value="purchase">Purchase (add)</option>
                    <option value="sale">Sale (deduct)</option>
                    <option value="damaged">Damaged/Lost (deduct)</option>
                    <option value="adjustment">Manual Adjustment</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                <input type="number" name="quantity" min="1" value="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                <p class="text-xs text-gray-500 mt-1">Use positive number. System auto-deducts for sale/damaged types.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Source / Destination</label>
                <input type="text" name="source" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" placeholder="e.g. Green Grocers, House 2A">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-medium">Apply Adjustment</button>
                <button type="button" onclick="closeModal('crateAdjustmentModal')" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>
