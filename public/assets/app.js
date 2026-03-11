/**
 * Dove Haven Farms – Front-end Application
 * Replaces the demo in-memory script with real AJAX calls to /api/* endpoints.
 */

// ─── Globals ─────────────────────────────────────────────────────────────────
const PAGE    = document.body?.dataset?.page ?? '';
const CSRF    = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
let productionChartInstance = null;
let houseChartInstance      = null;

// ─── Helpers ─────────────────────────────────────────────────────────────────

/** AJAX wrapper – always sends CSRF token, always expects JSON back */
async function api(method, url, body = null) {
    const opts = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF(),
        },
        credentials: 'same-origin',
    };
    if (body) opts.body = JSON.stringify(body);

    const res  = await fetch(url, opts);
    const data = await res.json();

    if (!data.ok) {
        throw new Error(data.error ?? 'Request failed');
    }
    return data.data;
}

function showAlert(message, type = 'success') {
    const container = document.getElementById('alertsContainer');
    if (!container) return;

    const colors = { success: 'bg-green-500', error: 'bg-red-500', warning: 'bg-yellow-500', info: 'bg-blue-500' };
    const icons  = { success: 'check-circle',  error: 'alert-circle', warning: 'alert-triangle', info: 'info' };

    const el = document.createElement('div');
    el.className = `${colors[type] ?? colors.info} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 alert-enter`;
    el.innerHTML = `<i data-lucide="${icons[type] ?? 'info'}" class="w-5 h-5"></i><span>${message}</span>`;
    container.appendChild(el);

    if (typeof lucide !== 'undefined') lucide.createIcons();

    setTimeout(() => {
        el.classList.replace('alert-enter', 'alert-exit');
        setTimeout(() => el.remove(), 300);
    }, 3500);
}

function openModal(id)  { document.getElementById(id)?.classList.remove('hidden'); if (typeof lucide !== 'undefined') lucide.createIcons(); }
function closeModal(id) { document.getElementById(id)?.classList.add('hidden'); }

function toggleMobileMenu() {
    document.getElementById('mobileMenu')?.classList.toggle('hidden');
}

async function doLogout() {
    try {
        await api('POST', '/api/auth/logout');
    } catch (_) { /* ignore */ }
    window.location.href = '/login';
}

function fmtDate(d) { return d ? d.split('T')[0] : ''; }
function fmtMoney(n){ return '$' + parseFloat(n ?? 0).toFixed(2); }

// ─── Dashboard ───────────────────────────────────────────────────────────────

async function loadDashboard() {
    try {
        const [bootstrap, weekly, prod] = await Promise.all([
            api('GET', '/api/bootstrap'),
            api('GET', '/api/production/weekly'),
            api('GET', '/api/production?limit=10'),
        ]);

        const summary = bootstrap.todayProd ?? {};

        setText('todayEggs',  (summary.total_eggs ?? 0).toLocaleString());
        setText('todayCrates', bootstrap.crateStock ?? 0);
        setText('todaySales',  fmtMoney(bootstrap.todayRevenue));
        setText('qsTotalCrates', summary.total_crates ?? 0);
        setText('qsLarge',       summary.large  ?? 0);
        setText('qsMedium',      summary.medium ?? 0);
        setText('qsSmall',       summary.small  ?? 0);
        setText('qsBroken',      summary.broken ?? 0);

        // Recent production table
        const tbody = document.getElementById('recentProductionTable');
        if (tbody) {
            tbody.innerHTML = prod.length === 0
                ? '<tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">No collections recorded today.</td></tr>'
                : prod.slice(0,10).map(r => `
                    <tr class="border-b border-gray-100 table-row-hover">
                        <td class="px-4 py-3">${fmtDate(r.date)}</td>
                        <td class="px-4 py-3">${r.house_id}</td>
                        <td class="px-4 py-3">${r.crates}</td>
                        <td class="px-4 py-3">${r.loose_eggs}</td>
                        <td class="px-4 py-3 font-medium">${parseInt(r.total_eggs).toLocaleString()}</td>
                        <td class="px-4 py-3">${r.employee_name ?? '—'}</td>
                    </tr>`).join('');
        }

        initProductionChart(weekly);
    } catch (e) {
        showAlert('Error loading dashboard: ' + e.message, 'error');
    }
}

function initProductionChart(data) {
    if (productionChartInstance) { productionChartInstance.destroy(); productionChartInstance = null; }
    const ctx = document.getElementById('productionChart');
    if (!ctx || !data?.length) return;

    productionChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels:   data.map(r => fmtDate(r.date)),
            datasets: [{ label: 'Total Eggs', data: data.map(r => r.total), borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,0.1)', tension: 0.4, fill: true }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
}

function refreshDashboard() { loadDashboard(); }

function exportDashboardPDF() {
    showAlert('PDF export is available from the Reports page.', 'info');
}

function setText(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
}

// ─── Rearing / Production ─────────────────────────────────────────────────────

async function loadRearing() {
    try {
        const rows = await api('GET', '/api/production?limit=100');
        renderProductionTable(rows);
        const weekly = await api('GET', '/api/production/weekly');
        initRearingCharts(weekly, rows);
    } catch (e) {
        showAlert('Error loading production data: ' + e.message, 'error');
    }
}

function renderProductionTable(rows) {
    const tbody = document.getElementById('productionTableBody');
    if (!tbody) return;
    tbody.innerHTML = rows.length === 0
        ? '<tr><td colspan="13" class="px-4 py-8 text-center text-gray-400">No production records found.</td></tr>'
        : rows.map(r => `
            <tr class="border-b border-gray-100 table-row-hover text-sm">
                <td class="px-4 py-3">${fmtDate(r.date)}</td>
                <td class="px-4 py-3">${r.house_id}</td>
                <td class="px-4 py-3">${r.crates}</td>
                <td class="px-4 py-3">${r.loose_eggs}</td>
                <td class="px-4 py-3">${r.grade_large}</td>
                <td class="px-4 py-3">${r.grade_medium}</td>
                <td class="px-4 py-3">${r.grade_small}</td>
                <td class="px-4 py-3">${r.grade_pullet}</td>
                <td class="px-4 py-3 text-red-600">${r.grade_broken}</td>
                <td class="px-4 py-3 font-semibold">${parseInt(r.total_eggs).toLocaleString()}</td>
                <td class="px-4 py-3">${parseFloat(r.feed_kg).toFixed(1)}</td>
                <td class="px-4 py-3">${r.employee_name ?? '—'}</td>
                <td class="px-4 py-3">
                    <button onclick="deleteProduction(${r.id})" class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded border border-red-300 hover:bg-red-50">Delete</button>
                </td>
            </tr>`).join('');
}

function initRearingCharts(weekly, rows) {
    // Production chart
    if (productionChartInstance) { productionChartInstance.destroy(); productionChartInstance = null; }
    const ctx1 = document.getElementById('rearingProductionChart');
    if (ctx1 && weekly?.length) {
        productionChartInstance = new Chart(ctx1, {
            type: 'bar',
            data: { labels: weekly.map(r => fmtDate(r.date)), datasets: [{ label: 'Eggs', data: weekly.map(r => r.total), backgroundColor: '#16a34a' }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    // Grade distribution (today)
    const ctx2 = document.getElementById('gradeChart');
    if (ctx2 && rows?.length) {
        const today = rows.filter(r => fmtDate(r.date) === new Date().toISOString().split('T')[0]);
        const grades = { Large: 0, Medium: 0, Small: 0, Pullet: 0, Broken: 0 };
        today.forEach(r => { grades.Large += +r.grade_large; grades.Medium += +r.grade_medium; grades.Small += +r.grade_small; grades.Pullet += +r.grade_pullet; grades.Broken += +r.grade_broken; });

        if (houseChartInstance) { houseChartInstance.destroy(); houseChartInstance = null; }
        houseChartInstance = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: Object.keys(grades),
                datasets: [{ data: Object.values(grades), backgroundColor: ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444'] }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('productionForm');
    if (form) {
        // Default date to today
        const dateInput = document.getElementById('prodDate');
        if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(form));
            // Collect grades from form
            form.querySelectorAll('[name^="grade_"]').forEach(el => { data[el.name] = el.value; });
            try {
                await api('POST', '/api/production', data);
                showAlert('Collection recorded!', 'success');
                closeModal('productionModal');
                form.reset();
                if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];
                loadRearing();
            } catch (err) {
                showAlert(err.message, 'error');
            }
        });
    }
});

async function deleteProduction(id) {
    if (!confirm('Delete this production record?')) return;
    try {
        await api('POST', `/api/production/delete/${id}`);
        showAlert('Record deleted', 'success');
        loadRearing();
    } catch (e) {
        showAlert(e.message, 'error');
    }
}

// ─── Inventory ────────────────────────────────────────────────────────────────

let inventoryItems = [];

async function loadInventory() {
    try {
        const [items, txns] = await Promise.all([
            api('GET', '/api/inventory'),
            api('GET', '/api/inventory/transactions?limit=50'),
        ]);
        inventoryItems = items;
        renderIngredientCards(items);
        renderInventoryTransactions(txns);
        populateInventorySelects(items);
    } catch (e) {
        showAlert('Error loading inventory: ' + e.message, 'error');
    }
}

function renderIngredientCards(items) {
    const grid = document.getElementById('ingredientsGrid');
    if (!grid) return;

    const alertBox = document.getElementById('inventoryAlerts');
    let alerts = '';

    grid.innerHTML = items.map(item => {
        const pct = item.purchased > 0 ? (item.quantity / item.purchased) * 100 : 100;
        const low = pct < 20;
        if (low) alerts += `<div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2"><i data-lucide="alert-triangle" class="w-4 h-4"></i>Low stock: <strong>${item.name}</strong> (${parseFloat(item.quantity).toFixed(0)} kg remaining)</div>`;
        return `
        <div class="bg-white p-5 rounded-xl shadow-sm border ${low ? 'border-yellow-400' : 'border-gray-200'} card-hover">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-semibold text-gray-900">${item.name}</h3>
                ${low ? '<span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Low Stock</span>' : '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">OK</span>'}
            </div>
            <p class="text-2xl font-bold text-gray-900 mb-1">${parseFloat(item.quantity).toFixed(0)} <span class="text-sm font-normal text-gray-500">kg</span></p>
            <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                <div class="bg-green-600 h-2 rounded-full" style="width:${Math.min(100,pct).toFixed(0)}%"></div>
            </div>
            <p class="text-xs text-gray-500">Purchased: ${parseFloat(item.purchased).toFixed(0)} kg · Used: ${parseFloat(item.used).toFixed(0)} kg</p>
            <p class="text-xs text-gray-500">Supplier: ${item.supplier || '—'} · Cost: $${parseFloat(item.unit_cost).toFixed(2)}/kg</p>
        </div>`;
    }).join('');

    if (alertBox) {
        alertBox.innerHTML = alerts;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function renderInventoryTransactions(txns) {
    const tbody = document.getElementById('inventoryHistoryTable');
    if (!tbody) return;
    tbody.innerHTML = txns.length === 0
        ? '<tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">No transactions.</td></tr>'
        : txns.map(t => `
            <tr class="border-b border-gray-100 table-row-hover text-sm">
                <td class="px-4 py-3">${t.created_at?.split('T')[0] ?? ''}</td>
                <td class="px-4 py-3">${t.item_name}</td>
                <td class="px-4 py-3"><span class="${t.type==='purchase'?'text-green-600':'text-orange-600'} font-medium capitalize">${t.type}</span></td>
                <td class="px-4 py-3">${parseFloat(t.quantity).toFixed(1)} kg</td>
                <td class="px-4 py-3">${t.supplier || '—'}</td>
                <td class="px-4 py-3">${t.notes || '—'}</td>
            </tr>`).join('');
}

function populateInventorySelects(items) {
    ['inventoryItemSelect','usageItemSelect'].forEach(id => {
        const sel = document.getElementById(id);
        if (!sel) return;
        sel.innerHTML = '<option value="">Select Ingredient</option>' +
            items.map(i => `<option value="${i.id}">${i.name}</option>`).join('');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const purchaseForm = document.getElementById('inventoryForm');
    if (purchaseForm) {
        purchaseForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(purchaseForm));
            try {
                await api('POST', '/api/inventory/purchase', data);
                showAlert('Purchase recorded!', 'success');
                closeModal('inventoryModal');
                purchaseForm.reset();
                loadInventory();
            } catch (err) {
                showAlert(err.message, 'error');
            }
        });
    }

    const usageForm = document.getElementById('usageForm');
    if (usageForm) {
        usageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(usageForm));
            try {
                await api('POST', '/api/inventory/usage', data);
                showAlert('Usage recorded!', 'success');
                closeModal('ingredientUsageModal');
                usageForm.reset();
                loadInventory();
            } catch (err) {
                showAlert(err.message, 'error');
            }
        });
    }
});

// ─── CRM ─────────────────────────────────────────────────────────────────────

let crmCustomers = [];

async function loadCRM() {
    try {
        const [customers, orders, purchases, stats] = await Promise.all([
            api('GET', '/api/crm/customers'),
            api('GET', '/api/crm/orders'),
            api('GET', '/api/crm/purchases'),
            api('GET', '/api/crm/stats'),
        ]);
        crmCustomers = customers;
        renderCustomers(customers);
        renderOrders(orders);
        renderPurchases(purchases);
        populateOrderCustomerSelect(customers);

        setText('totalOutstanding', fmtMoney(stats.outstanding));
        setText('cashSales',        fmtMoney(stats.today_cash));
        setText('bankSales',        fmtMoney(stats.today_bank));
        setText('pendingBalance',   fmtMoney(stats.outstanding));
    } catch (e) {
        showAlert('Error loading CRM: ' + e.message, 'error');
    }
}

function renderCustomers(customers) {
    const tbody = document.getElementById('customersTableBody');
    if (!tbody) return;
    tbody.innerHTML = customers.length === 0
        ? '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No customers found.</td></tr>'
        : customers.map(c => `
            <tr class="border-b border-gray-100 table-row-hover text-sm">
                <td class="px-4 py-3 font-medium">${c.name}</td>
                <td class="px-4 py-3">${c.email || '—'}</td>
                <td class="px-4 py-3">${c.phone || '—'}</td>
                <td class="px-4 py-3 ${parseFloat(c.balance)>0?'text-red-600 font-semibold':''}">${fmtMoney(c.balance)}</td>
                <td class="px-4 py-3"><span class="badge-active px-2 py-1 rounded-full text-xs font-medium">Active</span></td>
                <td class="px-4 py-3">
                    <button onclick="deleteCustomer(${c.id},'${escHtml(c.name)}')" class="text-red-500 hover:text-red-700 text-xs border border-red-200 px-2 py-1 rounded">Delete</button>
                </td>
            </tr>`).join('');
}

function renderOrders(orders) {
    const tbody = document.getElementById('ordersTableBody');
    if (!tbody) return;
    tbody.innerHTML = orders.length === 0
        ? '<tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">No orders found.</td></tr>'
        : orders.map(o => {
            const badgeCls = o.payment_status === 'paid' ? 'badge-paid' : o.payment_status === 'partial' ? 'badge-partial' : 'badge-unpaid';
            return `
            <tr class="border-b border-gray-100 table-row-hover text-sm">
                <td class="px-4 py-3 font-mono">#${o.id}</td>
                <td class="px-4 py-3">${o.customer_name}</td>
                <td class="px-4 py-3">${fmtDate(o.date)}</td>
                <td class="px-4 py-3">${fmtMoney(o.total)}</td>
                <td class="px-4 py-3">${fmtMoney(o.paid)}</td>
                <td class="px-4 py-3 ${parseFloat(o.balance)>0?'text-red-600 font-semibold':''}">${fmtMoney(o.balance)}</td>
                <td class="px-4 py-3 capitalize">${o.payment_method}</td>
                <td class="px-4 py-3"><span class="${badgeCls} px-2 py-1 rounded-full text-xs font-medium capitalize">${o.payment_status}</span></td>
                <td class="px-4 py-3">
                    ${parseFloat(o.balance)>0 ? `<button onclick="openPaymentModal(${o.id})" class="text-green-600 hover:text-green-800 text-xs border border-green-300 px-2 py-1 rounded">Pay</button>` : ''}
                </td>
            </tr>`;
        }).join('');
}

function renderPurchases(purchases) {
    const tbody = document.getElementById('purchasesTableBody');
    if (!tbody) return;
    tbody.innerHTML = purchases.length === 0
        ? '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No purchases recorded.</td></tr>'
        : purchases.map(p => `
            <tr class="border-b border-gray-100 table-row-hover text-sm">
                <td class="px-4 py-3">${fmtDate(p.date)}</td>
                <td class="px-4 py-3">${p.item_name}</td>
                <td class="px-4 py-3 capitalize">${p.category}</td>
                <td class="px-4 py-3">${parseFloat(p.quantity).toFixed(2)} ${p.unit}</td>
                <td class="px-4 py-3">${fmtMoney(p.unit_cost)}</td>
                <td class="px-4 py-3 font-semibold">${fmtMoney(p.total_cost)}</td>
                <td class="px-4 py-3">${p.supplier || '—'}</td>
                <td class="px-4 py-3">
                    <button onclick="deletePurchase(${p.id})" class="text-red-500 hover:text-red-700 text-xs border border-red-200 px-2 py-1 rounded">Delete</button>
                </td>
            </tr>`).join('');
}

function populateOrderCustomerSelect(customers) {
    const sel = document.getElementById('orderCustomerSelect');
    if (!sel) return;
    sel.innerHTML = '<option value="">Select Customer</option>' +
        customers.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
}

function switchCrmTab(tab) {
    document.querySelectorAll('.crm-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[id^="tab-"]').forEach(btn => {
        btn.classList.remove('border-green-600','text-green-600');
        btn.classList.add('border-transparent','text-gray-600');
    });
    document.getElementById(`crm-${tab}`)?.classList.remove('hidden');
    const activeBtn = document.getElementById(`tab-${tab}`);
    activeBtn?.classList.remove('border-transparent','text-gray-600');
    activeBtn?.classList.add('border-green-600','text-green-600');
}

function openPaymentModal(orderId) {
    document.getElementById('paymentOrderId').value = orderId;
    openModal('paymentModal');
}

// Order items helper
function addOrderItem() {
    const container = document.getElementById('orderItemsContainer');
    if (!container) return;
    if (container.querySelectorAll('.order-item-row').length >= 5) {
        showAlert('Maximum 5 products per order', 'warning');
        return;
    }
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 items-end order-item-row';
    row.innerHTML = `
        <div class="col-span-5">
            <select name="product[]" class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-500" onchange="calculateOrderTotal()">
                <option value="">Select Grade</option>
                <option value="eggs_large" data-price="30">Large Eggs (Crate)</option>
                <option value="eggs_medium" data-price="28">Medium Eggs (Crate)</option>
                <option value="eggs_small" data-price="25">Small Eggs (Crate)</option>
                <option value="eggs_pullet" data-price="22">Pullet/X-Small (Crate)</option>
                <option value="eggs_tray_large" data-price="12">Large Eggs (Tray)</option>
                <option value="eggs_tray_medium" data-price="11">Medium Eggs (Tray)</option>
                <option value="live_bird" data-price="15">Live Bird</option>
                <option value="manure" data-price="5">Manure (Bag)</option>
            </select>
        </div>
        <div class="col-span-3">
            <input type="number" name="quantity[]" min="0" value="0"
                class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-500"
                onchange="calculateOrderTotal()">
        </div>
        <div class="col-span-3">
            <input type="number" name="unitPrice[]" min="0" step="0.01" value="0"
                class="unit-price-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-500"
                onchange="calculateOrderTotal()">
        </div>
        <div class="col-span-1">
            <button type="button" onclick="removeOrderItem(this)" class="w-full px-2 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 text-sm">
                <i data-lucide="trash-2" class="w-4 h-4 mx-auto"></i>
            </button>
        </div>`;
    container.appendChild(row);
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function removeOrderItem(btn) {
    const container = document.getElementById('orderItemsContainer');
    const rows = container.querySelectorAll('.order-item-row');
    if (rows.length > 1) {
        btn.closest('.order-item-row').remove();
        calculateOrderTotal();
    } else {
        showAlert('At least one product line is required', 'warning');
    }
}

function calculateOrderTotal() {
    const container = document.getElementById('orderItemsContainer');
    if (!container) return;
    let total = 0;
    container.querySelectorAll('.order-item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.quantity-input')?.value  ?? 0) || 0;
        const price = parseFloat(row.querySelector('.unit-price-input')?.value ?? 0) || 0;
        total += qty * price;
    });
    setText('orderTotalDisplay', fmtMoney(total));
}

document.addEventListener('DOMContentLoaded', () => {
    // Order form
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        // Default date
        const dateInput = orderForm.querySelector('[name="date"]');
        if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];

        // Auto-fill price on product select
        orderForm.addEventListener('change', (e) => {
            if (e.target.matches('.product-select')) {
                const priceInput = e.target.closest('.order-item-row').querySelector('.unit-price-input');
                const price = e.target.selectedOptions[0]?.dataset.price ?? 0;
                if (priceInput) priceInput.value = price;
                calculateOrderTotal();
            }
        });

        orderForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd      = new FormData(orderForm);
            const products = fd.getAll('product[]');
            const qtys     = fd.getAll('quantity[]');
            const prices   = fd.getAll('unitPrice[]');
            const items    = products.map((p, i) => ({
                product: p, qty: qtys[i], unit_price: prices[i]
            })).filter(i => i.product && parseFloat(i.qty) > 0);

            const body = {
                customer_id:    fd.get('customer_id'),
                date:           fd.get('date'),
                paid_amount:    fd.get('paid_amount'),
                payment_method: fd.get('payment_method'),
                notes:          fd.get('notes'),
                items,
            };
            try {
                await api('POST', '/api/crm/orders', body);
                showAlert('Order created!', 'success');
                closeModal('orderModal');
                orderForm.reset();
                if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];
                loadCRM();
            } catch (err) {
                showAlert(err.message, 'error');
            }
        });
    }

    // Payment form
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const orderId = document.getElementById('paymentOrderId').value;
            const data    = Object.fromEntries(new FormData(paymentForm));
            try {
                await api('POST', `/api/crm/orders/${orderId}/payment`, { amount: data.amount, method: data.method });
                showAlert('Payment recorded!', 'success');
                closeModal('paymentModal');
                paymentForm.reset();
                loadCRM();
            } catch (err) {
                showAlert(err.message, 'error');
            }
        });
    }

    // Customer form
    const customerForm = document.getElementById('customerForm');
    if (customerForm) {
        customerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(customerForm));
            try {
                await api('POST', '/api/crm/customers', data);
                showAlert('Customer added!', 'success');
                closeModal('customerModal');
                customerForm.reset();
                loadCRM();
            } catch (err) {
                showAlert(err.message, 'error');
            }
        });
    }

    // Purchase form
    const purchaseForm = document.getElementById('purchaseForm');
    if (purchaseForm) {
        const dateInput = purchaseForm.querySelector('[name="date"]');
        if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];

        purchaseForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(purchaseForm));
            try {
                await api('POST', '/api/crm/purchases', data);
                showAlert('Purchase recorded!', 'success');
                closeModal('purchaseModal');
                purchaseForm.reset();
                if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];
                loadCRM();
            } catch (err) {
                showAlert(err.message, 'error');
            }
        });
    }
});

async function deleteCustomer(id, name) {
    if (!confirm(`Delete customer "${name}"? This cannot be undone.`)) return;
    try {
        await api('POST', `/api/crm/customers/delete/${id}`);
        showAlert('Customer deleted', 'success');
        loadCRM();
    } catch (e) { showAlert(e.message, 'error'); }
}

async function deletePurchase(id) {
    if (!confirm('Delete this purchase record?')) return;
    try {
        await api('POST', `/api/crm/purchases/delete/${id}`);
        showAlert('Purchase deleted', 'success');
        loadCRM();
    } catch (e) { showAlert(e.message, 'error'); }
}

// ─── Crates ───────────────────────────────────────────────────────────────────

async function loadCrates() {
    try {
        const [status, movements] = await Promise.all([
            api('GET', '/api/crates'),
            api('GET', '/api/crates/movements?limit=50'),
        ]);
        setText('availableCrates',  status.available);
        setText('soldCratesToday',  status.sold_today);
        setText('damagedCrates',    status.damaged);
        renderCrateMovements(movements);
    } catch (e) {
        showAlert('Error loading crate data: ' + e.message, 'error');
    }
}

function renderCrateMovements(movements) {
    const tbody = document.getElementById('crateMovementTable');
    if (!tbody) return;
    tbody.innerHTML = movements.length === 0
        ? '<tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No movements recorded.</td></tr>'
        : movements.map(m => `
            <tr class="border-b border-gray-100 table-row-hover text-sm">
                <td class="px-4 py-3">${m.created_at?.replace('T',' ').slice(0,16) ?? ''}</td>
                <td class="px-4 py-3 capitalize">${m.type}</td>
                <td class="px-4 py-3 ${parseInt(m.quantity)<0?'text-red-600':'text-green-600'}">${parseInt(m.quantity)>0?'+':''}${m.quantity}</td>
                <td class="px-4 py-3">${m.source || '—'}</td>
                <td class="px-4 py-3 font-semibold">${m.balance}</td>
            </tr>`).join('');
}

document.addEventListener('DOMContentLoaded', () => {
    const crateForm = document.getElementById('crateAdjustForm');
    if (crateForm) {
        crateForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd   = new FormData(crateForm);
            const type = fd.get('type');
            let qty    = parseInt(fd.get('quantity')) || 0;

            // Deduct for sale / damaged
            if (['sale','damaged'].includes(type)) qty = -qty;

            try {
                await api('POST', '/api/crates/adjust', { quantity: qty, type, source: fd.get('source') });
                showAlert('Crate stock updated!', 'success');
                closeModal('crateAdjustmentModal');
                crateForm.reset();
                loadCrates();
            } catch (err) {
                showAlert(err.message, 'error');
            }
        });
    }
});

// ─── Reports ─────────────────────────────────────────────────────────────────

let lastReportData = null;

async function generateReport() {
    const type   = document.getElementById('reportType')?.value  ?? 'daily';
    const start  = document.getElementById('reportStartDate')?.value ?? '';
    const end    = document.getElementById('reportEndDate')?.value   ?? '';
    const format = document.getElementById('reportFormat')?.value ?? 'view';

    let url = `/api/reports/generate?type=${type}`;
    if (start) url += `&date_from=${start}`;
    if (end)   url += `&date_to=${end}`;

    try {
        const data = await api('GET', url);
        lastReportData = data;

        const preview = document.getElementById('reportPreview');
        const content = document.getElementById('reportContent');
        const range   = document.getElementById('reportDateRange');

        if (range)   range.textContent = `Period: ${data.date_from} to ${data.date_to}`;
        if (preview) preview.classList.remove('hidden');
        if (content) {
            const s = data.summary;
            content.innerHTML = `
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                        <p class="text-2xl font-bold text-blue-700">${parseInt(s.total_eggs).toLocaleString()}</p>
                        <p class="text-sm text-blue-600">Total Eggs</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <p class="text-2xl font-bold text-green-700">${fmtMoney(s.total_revenue)}</p>
                        <p class="text-sm text-green-600">Total Revenue</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg text-center">
                        <p class="text-2xl font-bold text-purple-700">${fmtMoney(s.total_paid)}</p>
                        <p class="text-sm text-purple-600">Amount Collected</p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg text-center">
                        <p class="text-2xl font-bold text-orange-700">${fmtMoney(s.total_balance)}</p>
                        <p class="text-sm text-orange-600">Outstanding Balance</p>
                    </div>
                </div>
                <h4 class="font-semibold mb-2">Production Records (${data.production.length})</h4>
                <div class="overflow-x-auto mb-4">
                    <table class="w-full text-sm text-left border">
                        <thead class="bg-gray-50 text-xs uppercase"><tr>
                            <th class="px-3 py-2 border">Date</th>
                            <th class="px-3 py-2 border">House</th>
                            <th class="px-3 py-2 border">Crates</th>
                            <th class="px-3 py-2 border">Total Eggs</th>
                            <th class="px-3 py-2 border">Feed (kg)</th>
                        </tr></thead>
                        <tbody>${data.production.map(r=>`<tr class="border-b"><td class="px-3 py-2 border">${fmtDate(r.date)}</td><td class="px-3 py-2 border">${r.house_id}</td><td class="px-3 py-2 border">${r.crates}</td><td class="px-3 py-2 border">${parseInt(r.total_eggs).toLocaleString()}</td><td class="px-3 py-2 border">${parseFloat(r.feed_kg).toFixed(1)}</td></tr>`).join('') || '<tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">No production data</td></tr>'}</tbody>
                    </table>
                </div>
                <h4 class="font-semibold mb-2">Orders (${data.orders.length})</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border">
                        <thead class="bg-gray-50 text-xs uppercase"><tr>
                            <th class="px-3 py-2 border">#</th>
                            <th class="px-3 py-2 border">Customer</th>
                            <th class="px-3 py-2 border">Date</th>
                            <th class="px-3 py-2 border">Total</th>
                            <th class="px-3 py-2 border">Status</th>
                        </tr></thead>
                        <tbody>${data.orders.map(o=>`<tr class="border-b"><td class="px-3 py-2 border">${o.id}</td><td class="px-3 py-2 border">${o.customer_name}</td><td class="px-3 py-2 border">${fmtDate(o.date)}</td><td class="px-3 py-2 border">${fmtMoney(o.total)}</td><td class="px-3 py-2 border capitalize">${o.payment_status}</td></tr>`).join('') || '<tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">No orders</td></tr>'}</tbody>
                    </table>
                </div>`;
        }

        if (format === 'pdf') downloadCurrentReport();
    } catch (e) {
        showAlert('Error generating report: ' + e.message, 'error');
    }
}

function downloadCurrentReport() {
    if (!lastReportData) { showAlert('Generate a report first.', 'warning'); return; }
    if (typeof jspdf === 'undefined') { showAlert('PDF library not loaded.', 'error'); return; }

    const { jsPDF } = jspdf;
    const doc = new jsPDF();
    doc.setFontSize(16);
    doc.text('Dove Haven Farms - Report', 14, 20);
    doc.setFontSize(10);
    doc.text(`Period: ${lastReportData.date_from} to ${lastReportData.date_to}`, 14, 30);
    const s = lastReportData.summary;
    doc.text(`Total Eggs: ${parseInt(s.total_eggs).toLocaleString()}`, 14, 40);
    doc.text(`Total Revenue: ${fmtMoney(s.total_revenue)}`, 14, 48);
    doc.text(`Collected: ${fmtMoney(s.total_paid)}`, 14, 56);
    doc.text(`Outstanding: ${fmtMoney(s.total_balance)}`, 14, 64);
    doc.save(`DoveHavenFarms-Report-${lastReportData.date_from}.pdf`);
}

// ─── Admin ────────────────────────────────────────────────────────────────────

async function loadAdmin() {
    try {
        const employees = await api('GET', '/api/admin/employees');
        renderEmployees(employees);
        setText('totalEmployees', employees.length);
        setText('activeEmployees', employees.filter(e => e.status === 'active').length);
        setText('adminCount', employees.filter(e => e.role === 'admin').length);
        setText('inactiveCount', employees.filter(e => e.status !== 'active').length);
    } catch (e) {
        showAlert('Error loading employees: ' + e.message, 'error');
    }
}

function renderEmployees(employees) {
    const tbody = document.getElementById('employeesTableBody');
    if (!tbody) return;
    tbody.innerHTML = employees.length === 0
        ? '<tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">No employees found.</td></tr>'
        : employees.map(emp => `
            <tr class="border-b border-gray-100 table-row-hover text-sm">
                <td class="px-4 py-3 font-medium">${emp.full_name}</td>
                <td class="px-4 py-3 font-mono">${emp.username}</td>
                <td class="px-4 py-3">${emp.email || '—'}</td>
                <td class="px-4 py-3 capitalize">${emp.role.replace('_',' ')}</td>
                <td class="px-4 py-3"><span class="${emp.status==='active'?'badge-active':'badge-inactive'} px-2 py-1 rounded-full text-xs font-medium capitalize">${emp.status}</span></td>
                <td class="px-4 py-3">
                    <button onclick="toggleEmployeeStatus(${emp.id},'${emp.status}')" class="text-xs border ${emp.status==='active'?'border-red-300 text-red-600 hover:bg-red-50':'border-green-300 text-green-600 hover:bg-green-50'} px-2 py-1 rounded">
                        ${emp.status==='active'?'Deactivate':'Activate'}
                    </button>
                </td>
            </tr>`).join('');
}

async function toggleEmployeeStatus(id, current) {
    const action = current === 'active' ? 'deactivate' : 'activate';
    if (!confirm(`${action.charAt(0).toUpperCase()+action.slice(1)} this user?`)) return;
    try {
        await api('POST', `/api/admin/employees/${id}/toggle`);
        showAlert('Status updated', 'success');
        loadAdmin();
    } catch (e) { showAlert(e.message, 'error'); }
}

document.addEventListener('DOMContentLoaded', () => {
    const empForm = document.getElementById('employeeForm');
    if (empForm) {
        empForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(empForm));
            try {
                await api('POST', '/api/admin/employees', data);
                showAlert('User created!', 'success');
                closeModal('employeeModal');
                empForm.reset();
                loadAdmin();
            } catch (err) {
                showAlert(err.message, 'error');
            }
        });
    }
});

// ─── Escape helpers ───────────────────────────────────────────────────────────
function escHtml(str) {
    return String(str).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

// ─── Page Initialization ─────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
    switch (PAGE) {
        case 'dashboard':  loadDashboard();  break;
        case 'rearing':    loadRearing();    break;
        case 'inventory':  loadInventory();  break;
        case 'crm':        loadCRM();        break;
        case 'crates':     loadCrates();     break;
        case 'admin':      loadAdmin();      break;
    }
});
