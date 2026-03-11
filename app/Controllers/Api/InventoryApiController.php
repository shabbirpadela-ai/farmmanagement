<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Inventory;

class InventoryApiController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();
        $this->ok(Inventory::all());
    }

    public function transactions(): void
    {
        Auth::requireAuth();
        $filters = [
            'item_id' => $_GET['item_id'] ?? null,
            'limit'   => $_GET['limit']   ?? 100,
        ];
        $this->ok(Inventory::transactions($filters));
    }

    public function purchase(): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();

        $body     = $this->jsonBody();
        $required = ['item_id', 'quantity'];
        foreach ($required as $field) {
            if (empty($body[$field]) && $body[$field] !== 0) {
                $this->fail("Field '{$field}' is required");
            }
        }
        $id = Inventory::addPurchase($body);
        $this->ok(['id' => $id]);
    }

    public function usage(): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();

        $body     = $this->jsonBody();
        $required = ['item_id', 'quantity'];
        foreach ($required as $field) {
            if (empty($body[$field]) && $body[$field] !== 0) {
                $this->fail("Field '{$field}' is required");
            }
        }
        $id = Inventory::recordUsage($body);
        $this->ok(['id' => $id]);
    }

    public function createItem(): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();

        $body = $this->jsonBody();
        if (empty($body['name'])) {
            $this->fail("Field 'name' is required");
        }
        $id = Inventory::createItem($body);
        $this->ok(['id' => $id]);
    }
}
