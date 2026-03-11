<!-- Feed & Ingredients Stock Page -->
<div id="page-inventory" class="page-content space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Feed &amp; Ingredients Stock</h1>
            <p class="text-gray-600">Track all ingredients for feed formulation</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openModal('inventoryModal')" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Purchase</span>
            </button>
            <button onclick="openModal('ingredientUsageModal')" class="flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                <i data-lucide="minus" class="w-4 h-4"></i>
                <span>Record Usage</span>
            </button>
        </div>
    </div>

    <!-- Inventory Alerts -->
    <div id="inventoryAlerts" class="space-y-2"></div>

    <!-- Feed Ingredients Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="ingredientsGrid">
        <div class="col-span-full text-center py-8 text-gray-400">Loading…</div>
    </div>

    <!-- Inventory Transaction History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold mb-4">Inventory Transactions</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Ingredient</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Quantity</th>
                        <th class="px-4 py-3">Supplier</th>
                        <th class="px-4 py-3">Notes</th>
                    </tr>
                </thead>
                <tbody id="inventoryHistoryTable">
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Loading…</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Purchase Modal -->
<div id="inventoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-screen overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Record Ingredient Purchase</h3>
            <button onclick="closeModal('inventoryModal')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="inventoryForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ingredient</label>
                <select id="inventoryItemSelect" name="item_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                    <option value="">Select Ingredient</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity (kg)</label>
                    <input type="number" name="quantity" min="0" step="0.1" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Cost ($/kg)</label>
                    <input type="number" name="unit_cost" min="0" step="0.01" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                <input type="text" name="supplier" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" placeholder="Supplier name">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-medium">Save Purchase</button>
                <button type="button" onclick="closeModal('inventoryModal')" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Record Usage Modal -->
<div id="ingredientUsageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Record Ingredient Usage</h3>
            <button onclick="closeModal('ingredientUsageModal')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="usageForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ingredient</label>
                <select id="usageItemSelect" name="item_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                    <option value="">Select Ingredient</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Used (kg)</label>
                <input type="number" name="quantity" min="0.1" step="0.1" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 transition font-medium">Record Usage</button>
                <button type="button" onclick="closeModal('ingredientUsageModal')" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>
