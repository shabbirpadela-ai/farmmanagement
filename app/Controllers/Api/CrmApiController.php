<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Purchase;

class CrmApiController extends Controller
{
    // ─── Customers ────────────────────────────────────────────────────────────

    public function customers(): void
    {
        Auth::requireAuth();
        $this->ok(Customer::all());
    }

    public function createCustomer(): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();

        $body = $this->jsonBody();
        if (empty($body['name'])) {
            $this->fail("Customer 'name' is required");
        }
        $id = Customer::create($body);
        $this->ok(['id' => $id]);
    }

    public function updateCustomer(string $id): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();

        $body = $this->jsonBody();
        if (empty($body['name'])) {
            $this->fail("Customer 'name' is required");
        }
        Customer::update((int)$id, $body);
        $this->ok(['updated' => true]);
    }

    public function deleteCustomer(string $id): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();
        Customer::delete((int)$id);
        $this->ok(['deleted' => true]);
    }

    // ─── Orders ───────────────────────────────────────────────────────────────

    public function orders(): void
    {
        Auth::requireAuth();
        $filters = [
            'date_from' => $_GET['date_from'] ?? null,
            'date_to'   => $_GET['date_to']   ?? null,
        ];
        $this->ok(Order::all($filters));
    }

    public function createOrder(): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();

        $body  = $this->jsonBody();
        $items = $body['items'] ?? [];

        if (empty($body['customer_id'])) {
            $this->fail("'customer_id' is required");
        }
        if (empty($items)) {
            $this->fail('At least one order item is required');
        }

        $id = Order::create($body, $items);
        $this->ok(['id' => $id]);
    }

    public function orderItems(string $orderId): void
    {
        Auth::requireAuth();
        $this->ok(Order::items((int)$orderId));
    }

    public function recordPayment(string $orderId): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();

        $body   = $this->jsonBody();
        $amount = (float)($body['amount'] ?? 0);
        $method = $body['method'] ?? 'cash';

        if ($amount <= 0) {
            $this->fail('Payment amount must be greater than 0');
        }

        Order::recordPayment((int)$orderId, $amount, $method);
        $this->ok(['recorded' => true]);
    }

    // ─── Purchases ────────────────────────────────────────────────────────────

    public function purchases(): void
    {
        Auth::requireAuth();
        $filters = [
            'date_from' => $_GET['date_from'] ?? null,
            'date_to'   => $_GET['date_to']   ?? null,
        ];
        $this->ok(Purchase::all($filters));
    }

    public function createPurchase(): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();

        $body = $this->jsonBody();
        if (empty($body['item_name'])) {
            $this->fail("'item_name' is required");
        }
        $id = Purchase::create($body);
        $this->ok(['id' => $id]);
    }

    public function deletePurchase(string $id): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();
        Purchase::delete((int)$id);
        $this->ok(['deleted' => true]);
    }

    // ─── Stats ────────────────────────────────────────────────────────────────

    public function stats(): void
    {
        Auth::requireAuth();
        $this->ok([
            'outstanding'    => Customer::totalOutstanding(),
            'today_cash'     => Order::todayCash(),
            'today_bank'     => Order::todayBank(),
            'today_revenue'  => Order::todayRevenue(),
        ]);
    }
}
