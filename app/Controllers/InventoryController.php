<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class InventoryController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();
        $this->render('inventory/index', ['page' => 'inventory', 'user' => Auth::user()]);
    }
}
