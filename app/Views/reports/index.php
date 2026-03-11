<!-- Reports & Analytics Page -->
<div id="page-reports" class="page-content space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reports &amp; Analytics</h1>
            <p class="text-gray-600">Generate production, sales, and inventory reports</p>
        </div>
    </div>

    <!-- Report Generator -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold mb-4">Generate Report</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <select id="reportType" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                <option value="daily">Daily Report</option>
                <option value="weekly">Weekly Report</option>
                <option value="monthly">Monthly Report</option>
                <option value="custom">Custom Range</option>
            </select>
            <input type="date" id="reportStartDate" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
            <input type="date" id="reportEndDate"   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
            <select id="reportFormat" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                <option value="view">View Online</option>
                <option value="pdf">PDF Export</option>
            </select>
        </div>
        <button onclick="generateReport()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            Generate Report
        </button>
    </div>

    <!-- Report Preview -->
    <div id="reportPreview" class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hidden">
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <div>
                <h2 class="text-xl font-bold">Dove Haven Farms Report</h2>
                <p class="text-gray-600" id="reportDateRange">Date Range: --</p>
            </div>
            <button onclick="downloadCurrentReport()" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i data-lucide="download" class="w-4 h-4"></i>
                Download PDF
            </button>
        </div>
        <div id="reportContent" class="space-y-4"></div>
    </div>
</div>
