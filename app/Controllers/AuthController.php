<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        $this->render('auth/login', [], false);
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}
