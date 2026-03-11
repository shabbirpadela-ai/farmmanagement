<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();
        $this->render('dashboard/index', ['page' => 'dashboard', 'user' => Auth::user()]);
    }
}
