<?php
declare(strict_types=1);

namespace App\ThirdParty\Ebay;

class Api implements ApiInterface
{
    public function getOrder(): array
    {
        $order = json_decode(TestOrder::testOrder(), true);

        return $order;

    }
}