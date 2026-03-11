<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class CratesController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();
        $this->render('crates/index', ['page' => 'crates', 'user' => Auth::user()]);
    }
}
