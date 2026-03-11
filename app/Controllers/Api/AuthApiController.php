<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;

class AuthApiController extends Controller
{
    public function login(): void
    {
        $body     = $this->jsonBody();
        $username = trim((string)($body['username'] ?? ''));
        $password = (string)($body['password'] ?? '');

        if ($username === '' || $password === '') {
            $this->fail('Username and password are required');
        }

        $user = User::findByUsername($username);

        if (
            !$user
            || $user['status'] !== 'active'
            || !password_verify($password, $user['password_hash'])
        ) {
            $this->fail('Invalid credentials', 401);
        }

        Auth::login($user);
        $this->ok([
            'name'     => $user['full_name'],
            'role'     => $user['role'],
            'redirect' => $this->redirectForRole($user['role']),
        ]);
    }

    public function logout(): void
    {
        Auth::logout();
        $this->ok(['redirect' => '/login']);
    }

    private function redirectForRole(string $role): string
    {
        return match ($role) {
            'sales_manager' => '/crm',
            'farm_manager'  => '/rearing',
            default         => '/dashboard',
        };
    }
}
