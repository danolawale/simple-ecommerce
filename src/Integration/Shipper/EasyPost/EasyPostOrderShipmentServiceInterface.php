<?php

namespace App\Integration\Shipper\EasyPost;

interface EasyPostOrderShipmentServiceInterface
{
    public function createShipment(string $orderRef): array;
    public function buyShipment(array $shipmentInfo): array;
}
