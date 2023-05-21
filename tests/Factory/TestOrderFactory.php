<?php

namespace App\Tests\Factory;

use App\Entity\Order;

class TestOrderFactory
{
    public static function createOrderWithNoItem(?string $externalRef = null): Order
    {
        $order = new Order();
        $order->setExternalRef($externalRef ?? 'test123');
        $order->setShippingAddress1('1');
        $order->setShippingAddress2('Test Street');
        $order->setShippingAddress3('TS');
        $order->setCity('Test City');
        $order->setPostCode('234567');
        $order->setCountryCode('US');
        $order->setEmail('test@test.com');

        return $order;
    }
}