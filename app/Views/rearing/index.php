<!-- Rearing & Production Page -->
<div id="page-rearing" class="page-content space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rearing &amp; Production</h1>
            <p class="text-gray-600">Monitor flock growth, mortality, and egg production by house unit</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openModal('productionModal')" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Collection</span>
            </button>
        </div>
    </div>

    <!-- Production Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold mb-4">Production Records</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">House</th>
                        <th class="px-4 py-3">Crates</th>
                        <th class="px-4 py-3">Loose Eggs</th>
                        <th class="px-4 py-3">Large</th>
                        <th class="px-4 py-3">Medium</th>
                        <th class="px-4 py-3">Small</th>
                        <th class="px-4 py-3">Pullet</th>
                        <th class="px-4 py-3">Broken</th>
                        <th class="px-4 py-3">Total Eggs</th>
                        <th class="px-4 py-3">Feed (kg)</th>
                        <th class="px-4 py-3">Recorded By</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody id="productionTableBody">
                    <tr><td colspan="13" class="px-4 py-8 text-center text-gray-400">Loading…</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Weekly Egg Production</h3>
            <canvas id="rearingProductionChart" height="250"></canvas>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Grade Distribution (Today)</h3>
            <canvas id="gradeChart" height="250"></canvas>
        </div>
    </div>
</div>

<!-- Add Collection Modal -->
<div id="productionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-screen overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Record Egg Collection</h3>
            <button onclick="closeModal('productionModal')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="productionForm" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" id="prodDate" name="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">House ID</label>
                    <select id="prodHouse" name="house_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                        <option value="">Select House</option>
                        <?php for ($h = 1; $h <= 10; $h++): ?>
                            <?php foreach (['A','B','C','D','E','F'] as $u): ?>
                            <option value="house<?= $h . strtolower($u) ?>">House <?= $h . $u ?></option>
                            <?php endforeach; ?>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Crates</label>
                    <input type="number" id="prodCrates" name="crates" min="0" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loose Eggs</label>
                    <input type="number" id="prodLoose" name="loose_eggs" min="0" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Eggs</label>
                    <input type="number" id="prodTotal" name="total_eggs" min="0" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Egg Grades</p>
                <div class="grid grid-cols-5 gap-2">
                    <div><label class="block text-xs text-gray-600 mb-1">Large</label>
                        <input type="number" name="grade_large"  min="0" value="0" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-1 focus:ring-green-500"></div>
                    <div><label class="block text-xs text-gray-600 mb-1">Medium</label>
                        <input type="number" name="grade_medium" min="0" value="0" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-1 focus:ring-green-500"></div>
                    <div><label class="block text-xs text-gray-600 mb-1">Small</label>
                        <input type="number" name="grade_small"  min="0" value="0" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-1 focus:ring-green-500"></div>
                    <div><label class="block text-xs text-gray-600 mb-1">Pullet</label>
                        <input type="number" name="grade_pullet" min="0" value="0" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-1 focus:ring-green-500"></div>
                    <div><label class="block text-xs text-gray-600 mb-1">Broken</label>
                        <input type="number" name="grade_broken" min="0" value="0" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-1 focus:ring-green-500"></div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Feed (kg)</label>
                    <input type="number" name="feed_kg" min="0" step="0.1" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Feed Type</label>
                    <select name="feed_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                        <option value="layer">Layer Feed</option>
                        <option value="grower">Grower Feed</option>
                        <option value="starter">Starter Feed</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                <textarea name="comments" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-medium">Save Collection</button>
                <button type="button" onclick="closeModal('productionModal')" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>
