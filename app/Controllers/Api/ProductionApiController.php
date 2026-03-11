<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Production;

class ProductionApiController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();
        $filters = [
            'house_id'  => $_GET['house_id']  ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to'   => $_GET['date_to']   ?? null,
            'limit'     => $_GET['limit']     ?? 100,
        ];
        $this->ok(Production::all($filters));
    }

    public function store(): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();

        $body = $this->jsonBody();

        $required = ['date', 'house_id', 'crates', 'total_eggs'];
        foreach ($required as $field) {
            if (empty($body[$field]) && $body[$field] !== 0) {
                $this->fail("Field '{$field}' is required");
            }
        }

        $body['user_id'] = Auth::user()['id'];
        $id = Production::create($body);
        $this->ok(['id' => $id]);
    }

    public function destroy(string $id): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();

        Production::delete((int)$id);
        $this->ok(['deleted' => true]);
    }

    public function summary(): void
    {
        Auth::requireAuth();
        $this->ok(Production::todaySummary());
    }

    public function weekly(): void
    {
        Auth::requireAuth();
        $this->ok(Production::weeklyChart());
    }
}
