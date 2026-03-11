<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;

class AdminApiController extends Controller
{
    public function employees(): void
    {
        Auth::requireRole('admin');
        $this->ok(User::all());
    }

    public function createEmployee(): void
    {
        Auth::requireRole('admin');
        $this->verifyCsrf();

        $body = $this->jsonBody();
        $required = ['username', 'full_name', 'role', 'password'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                $this->fail("Field '{$field}' is required");
            }
        }

        // Check username uniqueness
        if (User::findByUsername($body['username'])) {
            $this->fail('Username already exists');
        }

        $id = User::create($body);
        $this->ok(['id' => $id]);
    }

    public function toggleStatus(string $id): void
    {
        Auth::requireRole('admin');
        $this->verifyCsrf();

        $user = User::findById((int)$id);
        if (!$user) {
            $this->fail('User not found', 404);
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        User::updateStatus((int)$id, $newStatus);
        $this->ok(['status' => $newStatus]);
    }
}
