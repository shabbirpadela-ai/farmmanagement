// Dove Haven Farms - Management Portal JavaScript

// Global State
let currentUser = null;
let currentPage = 'dashboard';
let productionData = [];
let inventory = [];
let customers = [];
let orders = [];
let employees = [];
let crateStock = 150;
let crateMovements = [];
let excessData = [];
let growthData = [];
let mortalityData = [];
let inventoryTransactions = [];
let purchases = [];
let productionChartInstance = null;
let houseChartInstance = null;
let growthChartInstance = null;
let mortalityChartInstance = null;
let layingChartInstance = null;
const EGGS_PER_CRATE = 30;

// Demo Data Initialization
function initializeDemoData() {
    const today = new Date();
    
    employees = [
        { id: 1, name: 'Administrator', email: 'admin@dovehaven.com', role: 'admin', accessLevel: 'Full Access', lastLogin: '2024-01-15 08:30', status: 'active' },
        { id: 2, name: 'Farm Manager 1', email: 'manager1@dovehaven.com', role: 'farm_manager', accessLevel: 'Houses 1-5', lastLogin: '2024-01-15 07:15', status: 'active' },
        { id: 3, name: 'Farm Manager 2', email: 'manager2@dovehaven.com', role: 'farm_manager', accessLevel: 'Houses 6-10', lastLogin: '2024-01-15 06:45', status: 'active' },
        { id: 4, name: 'Supervisor', email: 'supervisor@dovehaven.com', role: 'supervisor', accessLevel: 'All Houses - Read Only', lastLogin: '2024-01-14 16:45', status: 'active' },
        { id: 5, name: 'Sales Manager', email: 'sales@dovehaven.com', role: 'sales_manager', accessLevel: 'Sales & Purchases Only', lastLogin: '2024-01-15 09:00', status: 'active' }
    ];

    productionData = [];
    for (let i = 6; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(date.getDate() - i);
        const dateStr = date.toISOString().split('T')[0];
        
        productionData.push({
            id: Date.now() + Math.random(),
            date: dateStr,
            houseId: 'house1f',
            crates: 12 + Math.floor(Math.random() * 8),
            looseEggs: Math.floor(Math.random() * 30),
            totalEggs: 380 + Math.floor(Math.random() * 120),
            grades: {
                large: 200 + Math.floor(Math.random() * 100),
                medium: 100 + Math.floor(Math.random() * 50),
                small: 50 + Math.floor(Math.random() * 30),
                pullet: 20 + Math.floor(Math.random() * 20),
                broken: 5 + Math.floor(Math.random() * 10)
            },
            feed: parseFloat((120 + Math.random() * 40).toFixed(1)),
            feedType: 'layer',
            comments: i === 0 ? 'Normal collection' : '',
            employee: employees[0].name
        });
    }

    inventory = [
        { id: 'corn', name: 'Corn/Maize', quantity: 2500, purchased: 5000, used: 2500, supplier: 'GrainCo Ltd', cost: 0.35 },
        { id: 'soybean', name: 'Soybean Meal', quantity: 1800, purchased: 3000, used: 1200, supplier: 'ProteinFeeds Inc', cost: 0.45 },
        { id: 'wheat', name: 'Wheat', quantity: 1200, purchased: 2000, used: 800, supplier: 'GrainCo Ltd', cost: 0.32 },
        { id: 'layer', name: 'Layer Feed', quantity: 3500, purchased: 6000, used: 2500, supplier: 'In-House', cost: 0.52 }
    ];

    customers = [
        { id: 1, name: 'Green Grocers Ltd', email: 'orders@greengrocers.com', phone: '+1 234-567-8900', address: '123 Market St', balance: 1250.00, totalOrders: 15 },
        { id: 2, name: 'Fresh Farms Market', email: 'buy@freshfarms.com', phone: '+1 234-567-8901', address: '456 Farm Rd', balance: 450.00, totalOrders: 8 },
        { id: 3, name: 'City Supermarket', email: 'procurement@citysuper.com', phone: '+1 234-567-8902', address: '789 Main Ave', balance: 0, totalOrders: 23 }
    ];

    orders = [
        { id: 'ORD-001', customerId: 1, date: '2024-01-15', items: '15 Crates Large', quantity: 15, product: 'eggs_large', total: 450.00, paid: 200.00, balance: 250.00, paymentMethod: 'mixed', paymentStatus: 'partial' },
        { id: 'ORD-002', customerId: 2, date: '2024-01-14', items: '8 Crates Medium', quantity: 8, product: 'eggs_medium', total: 224.00, paid: 224.00, balance: 0, paymentMethod: 'cash', paymentStatus: 'paid' }
    ];

    crateMovements = [
        { date: '2024-01-15 08:00', type: 'Production', quantity: 85, source: 'All Houses', balance: 150 },
        { date: '2024-01-15 10:30', type: 'Sale', quantity: -15, source: 'Green Grocers', balance: 135 }
    ];

    inventoryTransactions = [];
    growthData = [];
    mortalityData = [];
    purchases = [];
}

// Alert System
function showAlert(message, type = 'success') {
    const container = document.getElementById('alertsContainer');
    if (!container) return;
    
    const alert = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    const icons = {
        success: 'check-circle',
        error: 'alert-circle',
        warning: 'alert-triangle',
        info: 'info'
    };
    
    alert.className = `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 alert-enter`;
    alert.innerHTML = `
        <i data-lucide="${icons[type]}" class="w-5 h-5"></i>
        <span>${message}</span>
    `;
    container.appendChild(alert);
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    setTimeout(() => {
        alert.classList.remove('alert-enter');
        alert.classList.add('alert-exit');
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}

// Authentication
function initLogin() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (username === 'admin' && password === 'admin123') {
                currentUser = { name: 'Administrator', role: 'admin' };
                loginSuccess();
            } else if (username === 'manager1' && password === 'manager123') {
                currentUser = { name: 'Farm Manager 1', role: 'farm_manager' };
                loginSuccess();
            } else if (username === 'manager2' && password === 'manager123') {
                currentUser = { name: 'Farm Manager 2', role: 'farm_manager' };
                loginSuccess();
            } else if (username === 'supervisor' && password === 'super123') {
                currentUser = { name: 'Supervisor', role: 'supervisor' };
                loginSuccess();
            } else if (username === 'sales' && password === 'sales123') {
                currentUser = { name: 'Sales Manager', role: 'sales_manager' };
                loginSuccess();
            } else if (username === 'employee' && password === 'emp123') {
                currentUser = { name: 'John Worker', role: 'employee' };
                loginSuccess();
            } else {
                showAlert('Invalid credentials. Try the demo accounts shown below.', 'error');
            }
        });
    }
}

function loginSuccess() {
    document.getElementById('loginScreen').classList.add('hidden');
    document.getElementById('app').classList.remove('hidden');
    document.getElementById('userNameDisplay').textContent = currentUser.name;
    document.getElementById('userRoleDisplay').textContent = currentUser.role.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    
    if (currentUser.role === 'admin') {
        const adminMenu = document.getElementById('adminMenu');
        const mobileAdminBtn = document.getElementById('mobileAdminBtn');
        if (adminMenu) adminMenu.classList.remove('hidden');
        if (mobileAdminBtn) mobileAdminBtn.classList.remove('hidden');
    }
    
    initializeDemoData();
    
    if (currentUser.role === 'sales_manager') {
        navigateTo('crm');
    } else if (currentUser.role === 'farm_manager') {
        navigateTo('rearing');
    } else {
        navigateTo('dashboard');
    }
    
    showAlert(`Welcome back, ${currentUser.name}!`, 'success');
}

function logout() {
    currentUser = null;
    location.reload();
}

// Navigation
function navigateTo(page) {
    document.querySelectorAll('.page-content').forEach(el => el.classList.add('hidden'));
    const pageEl = document.getElementById(`page-${page}`);
    if (pageEl) {
        pageEl.classList.remove('hidden');
    }
    
    document.querySelectorAll('.nav-btn').forEach(btn => {
        if (btn.dataset.page === page) {
            btn.classList.add('bg-gray-800');
        } else {
            btn.classList.remove('bg-gray-800');
        }
    });
    
    currentPage = page;
    
    switch(page) {
        case 'dashboard':
            loadDashboard();
            break;
        case 'rearing':
            loadRearingPage();
            break;
        case 'inventory':
            loadInventory();
            break;
        case 'crm':
            loadCRM();
            break;
        case 'crates':
            loadCrates();
            break;
        case 'admin':
            loadAdmin();
            break;
    }
    
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileMenu) mobileMenu.classList.add('hidden');
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    if (menu) menu.classList.toggle('hidden');
}

// Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('hidden');
}

// Dashboard Functions
function loadDashboard() {
    const today = new Date().toISOString().split('T')[0];
    const todayData = productionData.filter(p => p.date === today);
    const totalEggs = todayData.reduce((sum, p) => sum + p.totalEggs, 0);
    
    const todayEl = document.getElementById('todayEggs');
    if (todayEl) todayEl.textContent = totalEggs.toLocaleString();
    
    const cratesEl = document.getElementById('todayCrates');
    if (cratesEl) cratesEl.textContent = crateStock;
    
    const salesEl = document.getElementById('todaySales');
    if (salesEl) {
        const todaySales = orders.filter(o => o.date === today).reduce((sum, o) => sum + o.paid, 0);
        salesEl.textContent = '$' + todaySales.toFixed(0);
    }
    
    initCharts();
}

function initCharts() {
    if (productionChartInstance) productionChartInstance.destroy();
    
    const ctx1 = document.getElementById('productionChart');
    if (ctx1) {
        const dates = [...new Set(productionData.map(p => p.date))];
        const dailyTotals = dates.map(date => {
            return productionData.filter(p => p.date === date).reduce((sum, p) => sum + p.totalEggs, 0);
        });
        
        productionChartInstance = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Total Eggs',
                    data: dailyTotals,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
}

function refreshDashboard() {
    showAlert('Dashboard refreshed!', 'success');
    loadDashboard();
}

// Toggle functions
function toggleMedicationFields() {
    const waterType = document.getElementById('waterTypeSelect')?.value;
    const medFields = document.getElementById('medicationFields');
    if (medFields) {
        medFields.classList.toggle('hidden', waterType !== 'medication');
    }
}

function toggleGrowthMedicationFields() {
    const waterType = document.getElementById('growthWaterTypeSelect')?.value;
    const medFields = document.getElementById('growthMedicationFields');
    if (medFields) {
        medFields.classList.toggle('hidden', waterType !== 'medication');
    }
}

function toggleBagSizeField() {
    const unit = document.getElementById('inventoryUnitSelect')?.value;
    const bagField = document.getElementById('bagSizeField');
    if (bagField) {
        bagField.classList.toggle('hidden', unit !== 'bags');
    }
}

// Order functions
function addOrderItem() {
    const container = document.getElementById('orderItemsContainer');
    if (!container) return;
    
    const rowCount = container.querySelectorAll('.order-item-row').length;
    if (rowCount >= 5) {
        showAlert('Maximum 5 products per order', 'warning');
        return;
    }
    
    const newRow = document.createElement('div');
    newRow.className = 'grid grid-cols-12 gap-2 items-end order-item-row';
    newRow.innerHTML = `
        <div class="col-span-4">
            <label class="block text-xs font-medium text-gray-600 mb-1">Product</label>
            <select name="product[]" class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm" onchange="calculateOrderTotal()">
                <option value="">Select Grade</option>
                <option value="eggs_large" data-price="30">Large Eggs (Crate)</option>
                <option value="eggs_medium" data-price="28">Medium Eggs (Crate)</option>
                <option value="eggs_small" data-price="25">Small Eggs (Crate)</option>
                <option value="eggs_pullet" data-price="22">Pullet/X-Small Eggs (Crate)</option>
                <option value="eggs_tray_large" data-price="12">Large Eggs (Tray)</option>
                <option value="eggs_tray_medium" data-price="11">Medium Eggs (Tray)</option>
                <option value="eggs_tray_small" data-price="10">Small Eggs (Tray)</option>
                <option value="live_bird" data-price="15">Live Bird</option>
                <option value="manure" data-price="5">Manure (Bag)</option>
            </select>
        </div>
        <div class="col-span-3">
            <label class="block text-xs font-medium text-gray-600 mb-1">Qty</label>
            <input type="number" name="quantity[]" min="0" value="0" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm" onchange="calculateOrderTotal()">
        </div>
        <div class="col-span-3">
            <label class="block text-xs font-medium text-gray-600 mb-1">Unit Price ($)</label>
            <input type="number" name="unitPrice[]" min="0" step="0.01" value="0" class="unit-price-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm" onchange="calculateOrderTotal()">
        </div>
        <div class="col-span-2">
            <button type="button" onclick="removeOrderItem(this)" class="w-full px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition text-sm">
                <i data-lucide="trash-2" class="w-4 h-4 mx-auto"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function removeOrderItem(btn) {
    const container = document.getElementById('orderItemsContainer');
    const rows = container.querySelectorAll('.order-item-row');
    if (rows.length > 1) {
        btn.closest('.order-item-row').remove();
        calculateOrderTotal();
    } else {
        showAlert('You must have at least one product line', 'warning');
    }
}

function calculateOrderTotal() {
    let subtotal = 0;
    let totalCrates = 0;
    const rows = document.querySelectorAll('.order-item-row');
    
    rows.forEach(row => {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const unitPriceInput = row.querySelector('.unit-price-input');
        
        if (productSelect && quantityInput && unitPriceInput) {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.price && parseFloat(unitPriceInput.value) === 0) {
                unitPriceInput.value = selectedOption.dataset.price;
            }
            
            const qty = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(unitPriceInput.value) || 0;
            subtotal += qty * price;
            
            if (productSelect.value && productSelect.value.startsWith('eggs_') && productSelect.value.includes('crate')) {
                totalCrates += qty;
            }
        }
    });
    
    const subtotalEl = document.getElementById('orderSubtotal');
    const totalItemsEl = document.getElementById('orderTotalItems');
    const displayTotalEl = document.getElementById('displayOrderTotal');
    
    if (subtotalEl) subtotalEl.textContent = '$' + subtotal.toFixed(2);
    if (totalItemsEl) totalItemsEl.textContent = totalCrates + ' crates';
    if (displayTotalEl) displayTotalEl.textContent = '$' + subtotal.toFixed(2);
    
    updateOrderBalance();
}

function updateOrderBalance() {
    const subtotalEl = document.getElementById('orderSubtotal');
    const paidInput = document.getElementById('orderAmountPaid');
    const displayPaidEl = document.getElementById('displayOrderPaid');
    const displayBalanceEl = document.getElementById('displayOrderBalance');
    
    const subtotalText = subtotalEl?.textContent || '$0.00';
    const subtotal = parseFloat(subtotalText.replace('$', '')) || 0;
    const paid = parseFloat(paidInput?.value) || 0;
    const balance = Math.max(0, subtotal - paid);
    
    if (displayPaidEl) displayPaidEl.textContent = '$' + paid.toFixed(2);
    if (displayBalanceEl) displayBalanceEl.textContent = '$' + balance.toFixed(2);
}

// Inventory Functions
function loadInventory() {
    const grid = document.getElementById('ingredientsGrid');
    const alerts = document.getElementById('inventoryAlerts');
    
    if (grid) {
        grid.innerHTML = '';
        inventory.forEach(item => {
            const div = document.createElement('div');
            div.className = 'bg-white p-6 rounded-xl shadow-sm border border-gray-200';
            const percentRemaining = (item.quantity / item.purchased) * 100;
            const statusColor = percentRemaining < 20 ? 'text-red-600' : (percentRemaining < 40 ? 'text-yellow-600' : 'text-green-600');
            
            div.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-semibold text-lg">${item.name}</h4>
                    <span class="text-xs px-2 py-1 rounded-full ${percentRemaining < 20 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
                        ${percentRemaining.toFixed(0)}% left
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Current Stock:</span>
                        <span class="font-semibold ${statusColor}">${item.quantity.toLocaleString()} kg</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                        <div class="bg-green-600 h-2 rounded-full" style="width: ${percentRemaining}%"></div>
                    </div>
                </div>
            `;
            grid.appendChild(div);
        });
    }
    
    if (alerts) {
        alerts.innerHTML = '';
        const lowStock = inventory.filter(item => (item.quantity / item.purchased) < 0.2);
        if (lowStock.length > 0) {
            lowStock.forEach(item => {
                const alert = document.createElement('div');
                alert.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded flex items-center gap-2';
                alert.innerHTML = `
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    <span><strong>Low Stock:</strong> ${item.name} - ${item.quantity} kg remaining</span>
                `;
                alerts.appendChild(alert);
            });
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }
}

function openInventoryModal() {
    const dateInput = document.querySelector('#inventoryForm [name="purchaseDate"]');
    if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];
    openModal('inventoryModal');
}

function openIngredientUsageModal() {
    const dateInput = document.getElementById('usageDateInput');
    if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];
    
    const ingredientSelect = document.getElementById('usageIngredientSelect');
    if (ingredientSelect) {
        ingredientSelect.innerHTML = '<option value="">Select Ingredient</option>';
        inventory.forEach(item => {
            ingredientSelect.innerHTML += `<option value="${item.id}">${item.name} (${item.quantity} kg available)</option>`;
        });
    }
    openModal('ingredientUsageModal');
}

// Rearing Page
function loadRearingPage() {
    const grid = document.getElementById('houseUnitsGrid');
    if (!grid) return;
    
    grid.innerHTML = '';
    const houseUnits = ['house1a', 'house1b', 'house1c', 'house2a', 'house2b', 'house2c'];
    
    houseUnits.forEach(house => {
        const houseData = productionData.filter(p => p.houseId === house);
        const latestData = houseData[houseData.length - 1];
        
        const div = document.createElement('div');
        div.className = 'bg-white p-4 rounded-xl shadow-sm border border-gray-200 cursor-pointer hover:shadow-md transition';
        
        div.innerHTML = `
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-bold text-lg text-gray-900">${house.toUpperCase()}</h3>
                <span class="text-xs px-2 py-1 rounded-full ${latestData ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'}">
                    ${latestData ? 'Active' : 'No Data'}
                </span>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Latest Production:</span>
                    <span class="font-semibold">${latestData ? latestData.totalEggs + ' eggs' : 'N/A'}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Crates:</span>
                    <span class="font-semibold text-green-600">${latestData ? latestData.crates : '0'}</span>
                </div>
            </div>
        `;
        grid.appendChild(div);
    });
}

function openGrowthModal() {
    const dateInput = document.querySelector('#growthForm [name="growthDate"]');
    if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];
    
    const houseSelect = document.getElementById('growthHouseSelect');
    if (houseSelect) {
        houseSelect.innerHTML = '<option value="">Select House Unit</option>';
        const houseUnits = ['house1a', 'house1b', 'house1c', 'house2a', 'house2b', 'house2c'];
        houseUnits.forEach(h => {
            houseSelect.innerHTML += `<option value="${h}">${h.toUpperCase()}</option>`;
        });
    }
    openModal('growthModal');
}

function openMortalityModal() {
    const dateInput = document.querySelector('#mortalityForm [name="mortalityDate"]');
    if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];
    
    const houseSelect = document.getElementById('mortalityHouseSelect');
    if (houseSelect) {
        houseSelect.innerHTML = '<option value="">Select House Unit</option>';
        const houseUnits = ['house1a', 'house1b', 'house1c', 'house2a', 'house2b', 'house2c'];
        houseUnits.forEach(h => {
            houseSelect.innerHTML += `<option value="${h}">${h.toUpperCase()}</option>`;
        });
    }
    openModal('mortalityModal');
}

function openProductionModal() {
    const dateInput = document.querySelector('#productionForm [name="date"]');
    if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];
    
    const houseSelect = document.getElementById('productionHouseSelect');
    if (houseSelect) {
        houseSelect.innerHTML = '<option value="">Select House Unit</option>';
        const houseUnits = ['house1a', 'house1b', 'house1c', 'house2a', 'house2b', 'house2c'];
        houseUnits.forEach(h => {
            houseSelect.innerHTML += `<option value="${h}">${h.toUpperCase()}</option>`;
        });
    }
    openModal('productionModal');
}

function calculateGradesTotal() {
    const large = parseInt(document.getElementById('largeEggsInput')?.value) || 0;
    const medium = parseInt(document.getElementById('mediumEggsInput')?.value) || 0;
    const small = parseInt(document.getElementById('smallEggsInput')?.value) || 0;
    const pullet = parseInt(document.getElementById('pulletEggsInput')?.value) || 0;
    const broken = parseInt(document.getElementById('brokenEggsInput')?.value) || 0;
    
    const total = large + medium + small + pullet + broken;
    const crates = Math.floor(total / 30);
    const loose = total % 30;
    
    const looseInput = document.getElementById('looseEggInput');
    const crateInput = document.getElementById('crateInput');
    const totalDisplay = document.getElementById('totalEggDisplay');
    
    if (looseInput) looseInput.value = loose;
    if (crateInput) crateInput.value = crates;
    if (totalDisplay) totalDisplay.value = total;
}

// Crate Functions
function loadCrates() {
    const availableEl = document.getElementById('availableCrates');
    const soldEl = document.getElementById('soldCratesToday');
    
    if (availableEl) availableEl.textContent = crateStock;
    if (soldEl) soldEl.textContent = 0;
}

function openCrateAdjustmentModal() {
    openModal('crateAdjustmentModal');
}

// CRM Functions
function loadCRM() {
    const totalOutstanding = orders.reduce((sum, o) => sum + o.balance, 0);
    const today = new Date().toISOString().split('T')[0];
    const todayOrders = orders.filter(o => o.date === today);
    const cashSales = todayOrders.filter(o => o.paymentMethod === 'cash').reduce((sum, o) => sum + o.paid, 0);
    const bankSales = todayOrders.filter(o => o.paymentMethod === 'bank').reduce((sum, o) => sum + o.paid, 0);
    
    const outstandingEl = document.getElementById('totalOutstanding');
    const cashEl = document.getElementById('cashSales');
    const bankEl = document.getElementById('bankSales');
    
    if (outstandingEl) outstandingEl.textContent = '$' + totalOutstanding.toFixed(2);
    if (cashEl) cashEl.textContent = '$' + cashSales.toFixed(2);
    if (bankEl) bankEl.textContent = '$' + bankSales.toFixed(2);
    
    const customersBody = document.getElementById('customersTableBody');
    if (customersBody) {
        customersBody.innerHTML = '';
        customers.forEach(customer => {
            const row = document.createElement('tr');
            row.className = 'bg-white border-b hover:bg-gray-50';
            const statusClass = customer.balance > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
            const status = customer.balance > 0 ? 'Has Balance' : 'Paid Up';
            
            row.innerHTML = `
                <td class="px-4 py-3 font-medium">${customer.name}</td>
                <td class="px-4 py-3">${customer.phone}</td>
                <td class="px-4 py-3">${customer.totalOrders}</td>
                <td class="px-4 py-3 font-semibold ${customer.balance > 0 ? 'text-red-600' : 'text-green-600'}">
                    $${customer.balance.toFixed(2)}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs ${statusClass}">${status}</span>
                </td>
                <td class="px-4 py-3">
                    <button onclick="viewCustomer(${customer.id})" class="text-blue-600 hover:text-blue-800">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </td>
            `;
            customersBody.appendChild(row);
        });
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
    
    const ordersBody = document.getElementById('ordersTableBody');
    if (ordersBody) {
        ordersBody.innerHTML = '';
        orders.forEach(order => {
            const customer = customers.find(c => c.id === order.customerId);
            const row = document.createElement('tr');
            row.className = 'bg-white border-b hover:bg-gray-50';
            
            row.innerHTML = `
                <td class="px-4 py-3 font-medium">${order.id}</td>
                <td class="px-4 py-3">${customer?.name || 'Unknown'}</td>
                <td class="px-4 py-3">${order.date}</td>
                <td class="px-4 py-3">${order.quantity || 0} crates</td>
                <td class="px-4 py-3 font-semibold">$${order.total.toFixed(2)}</td>
                <td class="px-4 py-3 text-green-600">$${order.paid.toFixed(2)}</td>
                <td class="px-4 py-3 ${order.balance > 0 ? 'text-red-600' : 'text-green-600'} font-medium">$${order.balance.toFixed(2)}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-xs ${order.paymentMethod === 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                        ${order.paymentMethod}
                    </span>
                </td>
                <td class="px-4 py-3">
                    ${order.balance > 0 ? `<button onclick="openPaymentModal('${order.id}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Pay</button>` : '-'}
                </td>
            `;
            ordersBody.appendChild(row);
        });
    }
    
    const customerSelect = document.getElementById('orderCustomerSelect');
    if (customerSelect) {
        customerSelect.innerHTML = '<option value="">Select Customer</option>';
        customers.forEach(c => {
            customerSelect.innerHTML += `<option value="${c.id}">${c.name}</option>`;
        });
    }
}

function switchCrmTab(tab) {
    document.querySelectorAll('.crm-tab-content').forEach(el => el.classList.add('hidden'));
    const tabEl = document.getElementById(`crm-${tab}`);
    if (tabEl) tabEl.classList.remove('hidden');
    
    const customersTab = document.getElementById('tab-customers');
    const ordersTab = document.getElementById('tab-orders');
    
    if (customersTab) {
        customersTab.className = `px-6 py-3 text-sm font-medium border-b-2 ${tab === 'customers' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-600'}`;
    }
    if (ordersTab) {
        ordersTab.className = `px-6 py-3 text-sm font-medium border-b-2 ${tab === 'orders' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-600'}`;
    }
}

function openCustomerModal() {
    openModal('customerModal');
}

function openOrderModal() {
    // Reset order items to single row
    const container = document.getElementById('orderItemsContainer');
    if (container) {
        const firstRow = container.querySelector('.order-item-row');
        container.innerHTML = '';
        if (firstRow) {
            container.appendChild(firstRow);
            // Reset values
            const selects = firstRow.querySelectorAll('select');
            const inputs = firstRow.querySelectorAll('input');
            selects.forEach(s => s.value = '');
            inputs.forEach(i => i.value = i.type === 'number' ? '0' : '');
        }
    }
    calculateOrderTotal();
    openModal('orderModal');
}

function openPaymentModal(orderId) {
    const order = orders.find(o => o.id === orderId);
    if (!order) return;
    
    const orderIdInput = document.getElementById('paymentOrderId');
    const balanceDisplay = document.getElementById('currentBalanceDisplay');
    
    if (orderIdInput) orderIdInput.value = orderId;
    if (balanceDisplay) balanceDisplay.value = '$' + order.balance.toFixed(2);
    openModal('paymentModal');
}

function viewCustomer(id) {
    showAlert('Customer detail view coming soon!', 'info');
}

// Admin Functions
function loadAdmin() {
    const totalEl = document.getElementById('totalEmployees');
    const activeEl = document.getElementById('activeEmployees');
    const dataEl = document.getElementById('dataEntries');
    
    if (totalEl) totalEl.textContent = employees.length;
    if (activeEl) activeEl.textContent = employees.filter(e => e.status === 'active').length;
    if (dataEl) dataEl.textContent = productionData.length;
    
    const tbody = document.getElementById('employeesTableBody');
    if (tbody) {
        tbody.innerHTML = '';
        employees.forEach(emp => {
            const row = document.createElement('tr');
            row.className = 'bg-white border-b hover:bg-gray-50';
            row.innerHTML = `
                <td class="px-6 py-4 font-medium">${emp.name}</td>
                <td class="px-6 py-4 capitalize">${emp.role}</td>
                <td class="px-6 py-4">${emp.accessLevel}</td>
                <td class="px-6 py-4 text-sm">${emp.lastLogin}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-full text-xs ${emp.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                        ${emp.status}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <button onclick="toggleEmployeeStatus(${emp.id})" class="text-gray-600 hover:text-gray-800">
                        <i data-lucide="power" class="w-4 h-4"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

function openEmployeeModal() {
    openModal('employeeModal');
}

function toggleEmployeeStatus(id) {
    const emp = employees.find(e => e.id === id);
    if (emp) {
        emp.status = emp.status === 'active' ? 'inactive' : 'active';
        loadAdmin();
        showAlert(`Employee ${emp.status === 'active' ? 'activated' : 'deactivated'}`, 'success');
    }
}

function openPurchaseModal() {
    const dateInput = document.querySelector('#purchaseForm [name="purchaseDate"]');
    if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];
    openModal('purchaseModal');
}

// Form Handlers
function initFormHandlers() {
    // Production Form
    const productionForm = document.getElementById('productionForm');
    if (productionForm) {
        productionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            const large = parseInt(formData.get('largeEggs')) || 0;
            const medium = parseInt(formData.get('mediumEggs')) || 0;
            const small = parseInt(formData.get('smallEggs')) || 0;
            const pullet = parseInt(formData.get('pulletEggs')) || 0;
            const broken = parseInt(formData.get('brokenEggs')) || 0;
            const totalEggs = large + medium + small + pullet + broken;
            const crates = Math.floor(totalEggs / 30);
            const loose = totalEggs % 30;
            
            const newEntry = {
                id: Date.now(),
                date: formData.get('date'),
                houseId: formData.get('houseId'),
                crates: crates,
                looseEggs: loose,
                totalEggs: totalEggs,
                grades: { large, medium, small, pullet, broken },
                feed: parseFloat(formData.get('feed')) || 0,
                feedType: formData.get('feedType'),
                water: {
                    type: formData.get('waterType'),
                    amount: parseFloat(formData.get('waterAmount')) || 0,
                    medicationName: formData.get('medicationName') || null,
                    medicationDosage: formData.get('medicationDosage') || null,
                    nextDoseDate: formData.get('nextDoseDate') || null
                },
                comments: formData.get('comments') || '',
                employee: currentUser?.name || 'Unknown'
            };
            
            productionData.push(newEntry);
            crateStock += crates;
            
            closeModal('productionModal');
            e.target.reset();
            if (document.getElementById('medicationFields')) {
                document.getElementById('medicationFields').classList.add('hidden');
            }
            showAlert('Collection recorded successfully!', 'success');
            if (currentPage === 'dashboard') loadDashboard();
        });
    }
    
    // Customer Form
    const customerForm = document.getElementById('customerForm');
    if (customerForm) {
        customerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const newCustomer = {
                id: Date.now(),
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                address: formData.get('address'),
                balance: 0,
                totalOrders: 0
            };
            customers.push(newCustomer);
            closeModal('customerModal');
            e.target.reset();
            loadCRM();
            showAlert('Customer added successfully!', 'success');
        });
    }
    
    // Order Form
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const customerId = parseInt(formData.get('customerId'));
            const paid = parseFloat(formData.get('amountPaid')) || 0;
            
            // Collect all order items
            const products = formData.getAll('product[]');
            const quantities = formData.getAll('quantity[]');
            const unitPrices = formData.getAll('unitPrice[]');
            
            let total = 0;
            let totalCrates = 0;
            const itemDescriptions = [];
            
            for (let i = 0; i < products.length; i++) {
                if (products[i] && parseFloat(quantities[i]) > 0) {
                    const qty = parseFloat(quantities[i]) || 0;
                    const price = parseFloat(unitPrices[i]) || 0;
                    const lineTotal = qty * price;
                    total += lineTotal;
                    
                    const productSelect = document.querySelectorAll('.product-select')[i];
                    const productName = productSelect?.options[productSelect.selectedIndex]?.textContent || products[i];
                    itemDescriptions.push(`${qty} x ${productName}`);
                    
                    if (products[i].includes('crate')) {
                        totalCrates += qty;
                    }
                }
            }
            
            const newOrder = {
                id: `ORD-${String(orders.length + 1).padStart(3, '0')}`,
                customerId: customerId,
                date: new Date().toISOString().split('T')[0],
                items: itemDescriptions.join(', '),
                quantity: totalCrates,
                products: products.filter((p, i) => p && parseFloat(quantities[i]) > 0).map((p, i) => ({
                    product: p,
                    quantity: parseFloat(quantities[i]) || 0,
                    unitPrice: parseFloat(unitPrices[i]) || 0
                })),
                total: total,
                paid: paid,
                balance: total - paid,
                paymentMethod: formData.get('paymentMethod'),
                paymentStatus: paid >= total ? 'paid' : (paid > 0 ? 'partial' : 'pending')
            };
            
            if (totalCrates > 0) {
                crateStock -= totalCrates;
                crateMovements.unshift({
                    date: new Date().toLocaleString(),
                    type: 'Sale',
                    quantity: -totalCrates,
                    source: customers.find(c => c.id === customerId)?.name || 'Unknown',
                    balance: crateStock
                });
            }
            
            const customer = customers.find(c => c.id === customerId);
            if (customer) {
                customer.balance += (total - paid);
                customer.totalOrders += 1;
            }
            
            orders.push(newOrder);
            closeModal('orderModal');
            e.target.reset();
            
            // Reset to single empty row
            const container = document.getElementById('orderItemsContainer');
            if (container) {
                const rows = container.querySelectorAll('.order-item-row');
                for (let i = rows.length - 1; i > 0; i--) {
                    rows[i].remove();
                }
                const firstRow = container.querySelector('.order-item-row');
                if (firstRow) {
                    const selects = firstRow.querySelectorAll('select');
                    const inputs = firstRow.querySelectorAll('input');
                    selects.forEach(s => s.value = '');
                    inputs.forEach(i => i.value = i.classList.contains('quantity-input') || i.classList.contains('unit-price-input') ? '0' : '');
                }
            }
            
            loadCRM();
            showAlert('Order created successfully!', 'success');
        });
    }
    
    // Payment Form
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const orderId = document.getElementById('paymentOrderId').value;
            const amount = parseFloat(formData.get('paymentAmount')) || 0;
            
            const order = orders.find(o => o.id === orderId);
            if (order) {
                order.paid += amount;
                order.balance -= amount;
                order.paymentStatus = order.balance <= 0 ? 'paid' : (order.paid > 0 ? 'partial' : 'pending');
                
                const customer = customers.find(c => c.id === order.customerId);
                if (customer) {
                    customer.balance -= amount;
                }
            }
            
            closeModal('paymentModal');
            e.target.reset();
            loadCRM();
            showAlert('Payment recorded successfully!', 'success');
        });
    }
    
    // Growth Form
    const growthForm = document.getElementById('growthForm');
    if (growthForm) {
        growthForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const newGrowth = {
                id: Date.now(),
                date: formData.get('growthDate'),
                houseId: formData.get('growthHouse'),
                flockId: formData.get('flockId'),
                avgWeight: parseInt(formData.get('avgWeight')) || 0,
                birdCount: parseInt(formData.get('birdCount')) || 0,
                flockAgeWeeks: parseInt(formData.get('flockAge')) || 0,
                water: {
                    type: formData.get('growthWaterType'),
                    amount: parseFloat(formData.get('growthWaterAmount')) || 0,
                    medicationName: formData.get('growthMedicationName') || null,
                    medicationDosage: formData.get('growthMedicationDosage') || null,
                    nextDoseDate: formData.get('growthNextDoseDate') || null
                }
            };
            growthData.push(newGrowth);
            closeModal('growthModal');
            e.target.reset();
            if (document.getElementById('growthMedicationFields')) {
                document.getElementById('growthMedicationFields').classList.add('hidden');
            }
            showAlert('Growth recorded successfully!', 'success');
        });
    }
    
    // Mortality Form
    const mortalityForm = document.getElementById('mortalityForm');
    if (mortalityForm) {
        mortalityForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const newMortality = {
                id: Date.now(),
                date: formData.get('mortalityDate'),
                houseId: formData.get('mortalityHouse'),
                deaths: parseInt(formData.get('deaths')) || 0,
                cause: formData.get('cause'),
                notes: formData.get('mortalityNotes')
            };
            mortalityData.push(newMortality);
            closeModal('mortalityModal');
            e.target.reset();
            showAlert('Mortality recorded', 'warning');
        });
    }
    
    // Employee Form
    const employeeForm = document.getElementById('employeeForm');
    if (employeeForm) {
        employeeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const selectedHouses = Array.from(document.querySelectorAll('#employeeForm [name="houses"]:checked')).map(cb => cb.value);
            
            const newEmployee = {
                id: Date.now(),
                name: formData.get('name'),
                email: formData.get('email'),
                role: formData.get('role'),
                accessLevel: selectedHouses.length > 0 ? selectedHouses.join(', ') : 'None',
                lastLogin: 'Never',
                status: 'active',
                houses: selectedHouses
            };
            employees.push(newEmployee);
            closeModal('employeeModal');
            e.target.reset();
            loadAdmin();
            showAlert('Employee added successfully!', 'success');
        });
    }
    
    // Inventory Form
    const inventoryForm = document.getElementById('inventoryForm');
    if (inventoryForm) {
        inventoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            const ingredientType = formData.get('ingredientType');
            const quantity = parseFloat(formData.get('quantity')) || 0;
            const unit = formData.get('unit');
            const bagSize = parseFloat(formData.get('bagSize')) || 50;
            const cost = parseFloat(formData.get('cost')) || 0;
            const supplier = formData.get('supplier');
            
            // Calculate total kg
            let totalKg = quantity;
            if (unit === 'bags') {
                totalKg = quantity * bagSize;
            }
            
            // Add to inventory
            const existingItem = inventory.find(i => i.id === ingredientType);
            if (existingItem) {
                existingItem.quantity += totalKg;
                existingItem.purchased += totalKg;
                existingItem.lastUpdated = new Date().toISOString().split('T')[0];
            } else {
                const ingredientNames = {
                    'corn': 'Corn/Maize',
                    'soybean': 'Soybean Meal',
                    'wheat': 'Wheat',
                    'fishmeal': 'Fish Meal',
                    'limestone': 'Limestone',
                    'dcp': 'DCP',
                    'lysine': 'Lysine',
                    'methionine': 'Methionine',
                    'premix': 'Vitamin Premix',
                    'salt': 'Salt',
                    'oil': 'Vegetable Oil',
                    'starter': 'Starter Feed',
                    'grower': 'Grower Feed',
                    'developer': 'Developer Feed',
                    'layer': 'Layer Feed',
                    'galdus': 'Galdus Feed'
                };
                
                inventory.push({
                    id: ingredientType,
                    name: ingredientNames[ingredientType] || ingredientType,
                    quantity: totalKg,
                    purchased: totalKg,
                    used: 0,
                    supplier: supplier,
                    cost: cost,
                    lastUpdated: new Date().toISOString().split('T')[0]
                });
            }
            
            // Add transaction
            inventoryTransactions.push({
                id: Date.now(),
                date: formData.get('purchaseDate'),
                ingredient: ingredientType,
                type: 'purchase',
                quantity: totalKg,
                balance: existingItem ? existingItem.quantity : totalKg,
                notes: `Purchased from ${supplier}`
            });
            
            closeModal('inventoryModal');
            e.target.reset();
            if (document.getElementById('bagSizeField')) {
                document.getElementById('bagSizeField').classList.add('hidden');
            }
            loadInventory();
            showAlert('Purchase recorded successfully!', 'success');
        });
    }
    
    // Ingredient Usage Form
    const ingredientUsageForm = document.getElementById('ingredientUsageForm');
    if (ingredientUsageForm) {
        ingredientUsageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            const ingredientId = formData.get('usageIngredient');
            const quantity = parseFloat(formData.get('usageQuantity')) || 0;
            const purpose = formData.get('usagePurpose');
            const notes = formData.get('usageNotes');
            
            const item = inventory.find(i => i.id === ingredientId);
            if (item) {
                item.quantity -= quantity;
                item.used += quantity;
                
                inventoryTransactions.push({
                    id: Date.now(),
                    date: formData.get('usageDate'),
                    ingredient: ingredientId,
                    type: 'usage',
                    quantity: -quantity,
                    balance: item.quantity,
                    notes: purpose + (notes ? ' - ' + notes : '')
                });
                
                closeModal('ingredientUsageModal');
                e.target.reset();
                loadInventory();
                showAlert('Usage recorded successfully!', 'success');
            }
        });
    }
    
    // Purchase Form
    const purchaseForm = document.getElementById('purchaseForm');
    if (purchaseForm) {
        purchaseForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            const newPurchase = {
                id: Date.now(),
                date: formData.get('purchaseDate'),
                category: formData.get('purchaseCategory'),
                item: formData.get('purchaseItem'),
                quantity: parseFloat(formData.get('purchaseQty')) || 0,
                unit: formData.get('purchaseUnit'),
                unitCost: parseFloat(formData.get('unitCost')) || 0,
                totalCost: parseFloat(formData.get('totalCost')) || 0,
                supplier: formData.get('purchaseSupplier')
            };
            
            purchases.push(newPurchase);
            closeModal('purchaseModal');
            e.target.reset();
            showAlert('Purchase recorded!', 'success');
        });
    }
    
    // Crate Adjustment Form
    const crateForm = document.getElementById('crateAdjustmentForm');
    if (crateForm) {
        crateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const type = formData.get('adjustmentType');
            const qty = parseInt(formData.get('crateQty')) || 0;
            const actualQty = type === 'add' ? qty : -qty;
            
            crateStock += actualQty;
            crateMovements.unshift({
                date: new Date().toLocaleString(),
                type: type === 'add' ? 'Purchase/Return' : (type === 'remove' ? 'Damage/Loss' : 'Correction'),
                quantity: actualQty,
                source: formData.get('reason'),
                balance: crateStock
            });
            
            closeModal('crateAdjustmentModal');
            e.target.reset();
            loadCrates();
            showAlert('Crate stock adjusted!', 'success');
        });
    }
    
    // Excess Form
    const excessForm = document.getElementById('excessForm');
    if (excessForm) {
        excessForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            const newExcess = {
                id: Date.now(),
                date: formData.get('date'),
                houseId: formData.get('houseId'),
                excessCount: parseInt(formData.get('excessCount')) || 0,
                reason: formData.get('reason')
            };
            
            excessData.push(newExcess);
            closeModal('excessModal');
            e.target.reset();
            showAlert('Excess quantity recorded!', 'success');
        });
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initLogin();
    initFormHandlers();
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});