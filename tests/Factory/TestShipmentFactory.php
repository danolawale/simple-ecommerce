<?php

namespace App\Tests\Factory;

use App\Entity\Order;
use App\Entity\Shipment;

class TestShipmentFactory
{
    public static function createShipment(Order $order, ?array $data = []): Shipment
    {
        $shipment = new Shipment();
        $shipment->setShipmentRef($data['shipmentRef'] ?? 'shipment123');
        $shipment->setLabelUrl($data['labelUrl'] ?? 'label.png');
        $shipment->setTrackingCode($data['trackingCode'] ?? 'trackingCode123');
        $shipment->setOrder($order);

        return $shipment;
    }
}