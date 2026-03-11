<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Production;
use App\Models\Crate;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Inventory;

class BootstrapApiController extends Controller
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->fail('Unauthenticated', 401);
        }

        $user         = Auth::user();
        $todayProd    = Production::todaySummary();
        $weeklyChart  = Production::weeklyChart();
        $crateStock   = Crate::getStock();
        $todayRevenue = Order::todayRevenue();

        $this->ok([
            'user'         => $user,
            'csrfToken'    => Auth::csrfToken(),
            'todayProd'    => $todayProd,
            'weeklyChart'  => $weeklyChart,
            'crateStock'   => $crateStock,
            'todayRevenue' => $todayRevenue,
        ]);
    }
}
