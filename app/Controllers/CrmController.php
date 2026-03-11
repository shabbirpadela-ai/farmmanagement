<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class CrmController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();
        $this->render('crm/index', ['page' => 'crm', 'user' => Auth::user()]);
    }
}
