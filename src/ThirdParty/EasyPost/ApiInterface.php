<?php

namespace App\ThirdParty\EasyPost;

interface ApiInterface
{
    public function createShipment(array $shipment): array;
    public function buyShipment(array $buyShipmentPayload, string $shipmentId): array;
}