<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class AdminController extends Controller
{
    public function index(): void
    {
        Auth::requireRole('admin');
        $this->render('admin/index', ['page' => 'admin', 'user' => Auth::user()]);
    }
}
