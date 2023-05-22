<?php

declare(strict_types=1);

namespace App\ThirdParty\Ebay;

final class Api implements ApiInterface
{
    public function getOrder(): array
    {
        $order = json_decode(MockResponse::getOrder(), true);
        return $order;
    }
}
