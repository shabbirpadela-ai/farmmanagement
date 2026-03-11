<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Db;
use App\Models\Production;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\Customer;

class ReportsApiController extends Controller
{
    public function generate(): void
    {
        Auth::requireAuth();

        $type      = $_GET['type']       ?? 'daily';
        $dateFrom  = $_GET['date_from']  ?? date('Y-m-d');
        $dateTo    = $_GET['date_to']    ?? date('Y-m-d');

        // For weekly/monthly presets override the dates
        if ($type === 'daily') {
            $dateFrom = $dateTo = date('Y-m-d');
        } elseif ($type === 'weekly') {
            $dateFrom = date('Y-m-d', strtotime('-7 days'));
            $dateTo   = date('Y-m-d');
        } elseif ($type === 'monthly') {
            $dateFrom = date('Y-m-01');
            $dateTo   = date('Y-m-d');
        }

        $production = Production::all(['date_from' => $dateFrom, 'date_to' => $dateTo]);
        $orders     = Order::all(['date_from' => $dateFrom, 'date_to' => $dateTo]);

        $totalEggs    = array_sum(array_column($production, 'total_eggs'));
        $totalRevenue = array_sum(array_column($orders, 'total'));
        $totalPaid    = array_sum(array_column($orders, 'paid'));
        $totalBalance = array_sum(array_column($orders, 'balance'));

        $this->ok([
            'date_from'     => $dateFrom,
            'date_to'       => $dateTo,
            'type'          => $type,
            'production'    => $production,
            'orders'        => $orders,
            'summary'       => [
                'total_eggs'    => $totalEggs,
                'total_revenue' => $totalRevenue,
                'total_paid'    => $totalPaid,
                'total_balance' => $totalBalance,
                'total_orders'  => count($orders),
            ],
        ]);
    }
}
