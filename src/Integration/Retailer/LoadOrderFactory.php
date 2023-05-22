<?php

declare(strict_types=1);

namespace App\Integration\Retailer;

use App\Integration\Retailer\Ebay\LoadEbayOrderService;
use App\Repository\OrderRepository;
use App\ThirdParty\Ebay\Api as EbayApi;
use InvalidArgumentException;

class LoadOrderFactory
{
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    public function __invoke(string $platform)
    {
        return match ($platform) {
            'ebay' => new LoadEbayOrderService(new EbayApi(), $this->orderRepository),
            default => throw new InvalidArgumentException("Please specify a valid platform")
        };
    }
}
