<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class RearingController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();
        $this->render('rearing/index', ['page' => 'rearing', 'user' => Auth::user()]);
    }
}
