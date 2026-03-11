<!-- Sales & Purchases Page -->
<div id="page-crm" class="page-content space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sales &amp; Purchases</h1>
            <p class="text-gray-600">Manage orders, track payments, record purchases, and monitor stock</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openModal('customerModal')" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>New Customer</span>
            </button>
            <button onclick="openModal('orderModal')" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>New Order</span>
            </button>
        </div>
    </div>

    <!-- CRM Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <p class="text-sm text-gray-600 mb-1">Total Outstanding</p>
            <p class="text-2xl font-bold text-red-600" id="totalOutstanding">$0.00</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <p class="text-sm text-gray-600 mb-1">Today's Cash Sales</p>
            <p class="text-2xl font-bold text-green-600" id="cashSales">$0.00</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <p class="text-sm text-gray-600 mb-1">Bank Deposits</p>
            <p class="text-2xl font-bold text-blue-600" id="bankSales">$0.00</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <p class="text-sm text-gray-600 mb-1">Pending Balance</p>
            <p class="text-2xl font-bold text-orange-600" id="pendingBalance">$0.00</p>
        </div>
    </div>

    <!-- Purchases Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Purchases Recording</h3>
            <button onclick="openModal('purchaseModal')" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Record Purchase</span>
            </button>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Quantity</th>
                            <th class="px-4 py-3">Unit Cost</th>
                            <th class="px-4 py-3">Total Cost</th>
                            <th class="px-4 py-3">Supplier</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="purchasesTableBody">
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Customers & Orders Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <div class="flex">
                <button onclick="switchCrmTab('customers')" id="tab-customers" class="px-6 py-3 text-sm font-medium border-b-2 border-green-600 text-green-600">Customers</button>
                <button onclick="switchCrmTab('orders')"    id="tab-orders"    class="px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-800">Orders &amp; Payments</button>
            </div>
        </div>
        <div class="p-6">
            <!-- Customers Tab -->
            <div id="crm-customers" class="crm-tab-content">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Contact</th>
                                <th class="px-4 py-3">Phone</th>
                                <th class="px-4 py-3">Balance Due</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="customersTableBody">
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Orders Tab -->
            <div id="crm-orders" class="crm-tab-content hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3">Order ID</th>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3">Paid</th>
                                <th class="px-4 py-3">Balance</th>
                                <th class="px-4 py-3">Method</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Order Modal -->
<div id="orderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-screen overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">New Sales Order</h3>
            <button onclick="closeModal('orderModal')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="orderForm" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <select id="orderCustomerSelect" name="customer_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                        <option value="">Select Customer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
            </div>

            <!-- Order Items -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <p class="text-sm font-medium text-gray-700">Products</p>
                    <button type="button" onclick="addOrderItem()" class="text-sm text-green-600 hover:text-green-700 flex items-center gap-1">
                        <i data-lucide="plus" class="w-4 h-4"></i> Add Line
                    </button>
                </div>
                <div id="orderItemsContainer" class="space-y-3">
                    <div class="grid grid-cols-12 gap-2 items-end order-item-row">
                        <div class="col-span-5">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Product</label>
                            <select name="product[]" class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm" onchange="calculateOrderTotal()">
                                <option value="">Select Grade</option>
                                <option value="eggs_large"       data-price="30">Large Eggs (Crate)</option>
                                <option value="eggs_medium"      data-price="28">Medium Eggs (Crate)</option>
                                <option value="eggs_small"       data-price="25">Small Eggs (Crate)</option>
                                <option value="eggs_pullet"      data-price="22">Pullet/X-Small (Crate)</option>
                                <option value="eggs_tray_large"  data-price="12">Large Eggs (Tray)</option>
                                <option value="eggs_tray_medium" data-price="11">Medium Eggs (Tray)</option>
                                <option value="eggs_tray_small"  data-price="10">Small Eggs (Tray)</option>
                                <option value="live_bird"        data-price="15">Live Bird</option>
                                <option value="manure"           data-price="5">Manure (Bag)</option>
                            </select>
                        </div>
                        <div class="col-span-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Qty</label>
                            <input type="number" name="quantity[]" min="0" value="0"
                                class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm"
                                onchange="calculateOrderTotal()">
                        </div>
                        <div class="col-span-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Unit Price ($)</label>
                            <input type="number" name="unitPrice[]" min="0" step="0.01" value="0"
                                class="unit-price-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm"
                                onchange="calculateOrderTotal()">
                        </div>
                        <div class="col-span-1">
                            <button type="button" onclick="removeOrderItem(this)" class="w-full px-2 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition text-sm">
                                <i data-lucide="trash-2" class="w-4 h-4 mx-auto"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600">Order Total:</span>
                    <span class="font-semibold" id="orderTotalDisplay">$0.00</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid ($)</label>
                    <input type="number" name="paid_amount" min="0" step="0.01" value="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"
                        onchange="calculateOrderTotal()">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="mixed">Mixed</option>
                        <option value="credit">On Credit</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-medium">Create Order</button>
                <button type="button" onclick="closeModal('orderModal')" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Record Payment</h3>
            <button onclick="closeModal('paymentModal')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="paymentForm" class="p-6 space-y-4">
            <input type="hidden" id="paymentOrderId" name="order_id">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                <input type="number" name="amount" min="0.01" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select name="method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                    <option value="cash">Cash</option>
                    <option value="bank">Bank Transfer</option>
                    <option value="mixed">Mixed</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-medium">Record Payment</button>
                <button type="button" onclick="closeModal('paymentModal')" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- New Customer Modal -->
<div id="customerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">New Customer</h3>
            <button onclick="closeModal('customerModal')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="customerForm" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-medium">Save Customer</button>
                <button type="button" onclick="closeModal('customerModal')" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Purchase Modal -->
<div id="purchaseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-screen overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Record Purchase</h3>
            <button onclick="closeModal('purchaseModal')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="purchaseForm" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                        <option value="feed">Feed / Ingredients</option>
                        <option value="medicine">Medicine / Vaccines</option>
                        <option value="equipment">Equipment</option>
                        <option value="utilities">Utilities</option>
                        <option value="labour">Labour</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                <input type="text" name="item_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" min="0" step="0.01" value="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <select name="unit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                        <option value="kg">kg</option>
                        <option value="bags">bags</option>
                        <option value="litres">litres</option>
                        <option value="units">units</option>
                        <option value="pcs">pcs</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Cost ($)</label>
                    <input type="number" name="unit_cost" min="0" step="0.01" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Cost ($)</label>
                <input type="number" name="total_cost" min="0" step="0.01" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                <input type="text" name="supplier" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-medium">Save Purchase</button>
                <button type="button" onclick="closeModal('purchaseModal')" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>
