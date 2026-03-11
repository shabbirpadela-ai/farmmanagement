<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Crate;

class CratesApiController extends Controller
{
    public function status(): void
    {
        Auth::requireAuth();
        $this->ok([
            'available'  => Crate::getStock(),
            'sold_today' => Crate::soldToday(),
            'damaged'    => Crate::damagedTotal(),
        ]);
    }

    public function movements(): void
    {
        Auth::requireAuth();
        $limit = (int)($_GET['limit'] ?? 50);
        $this->ok(Crate::movements($limit));
    }

    public function adjust(): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();

        $body = $this->jsonBody();
        $qty  = (int)($body['quantity'] ?? 0);
        $type = $body['type']   ?? 'adjustment';
        $src  = $body['source'] ?? '';

        if ($qty === 0) {
            $this->fail('Quantity must not be 0');
        }

        Crate::adjust($qty, $type, $src);
        $this->ok(['balance' => Crate::getStock()]);
    }
}
