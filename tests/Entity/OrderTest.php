<?php

namespace App\Tests\Entity;

use App\Entity\Enums\OrderStatus;
use App\Entity\Item;
use App\Entity\Order;
use App\Tests\AbstractUnitTestCase;

class OrderTest extends AbstractUnitTestCase
{
    public function test_create(): void
    {
        $customerName = 'Daniel Maestro';
        $shippingAddress1 = '10';
        $shippingAddress2 = 'Major Street';
        $shippingPostcode = 'M11 1BB';
        $shippingCountryCode = 'GB';
        $email = 'dan.maestro@test.com';

        $order = new Order();
        $order->setExternalRef('123');
        $order->setCustomerName($customerName);
        $order->setShippingAddress1($shippingAddress1);
        $order->setShippingAddress2($shippingAddress2);
        $order->setPostCode($shippingPostcode);
        $order->setCity('Manchester');
        $order->setCountryCode($shippingCountryCode);
        $order->setEmail($email);
        $order->setPrice(10.50);
        $order->setCurrency('GBP');
        $order->setStatus(OrderStatus::STARTED->value);

        $item = new Item();
        $item->setSku('item_123a');
        $item->setQuantity(1);
        $item->setDescription('Test Item 1');
        $item->setOrder($order);

        $order->addItem($item);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->assertEquals(2, $order->getId());

        $this->assertNotNull($this->getEntity(Order::class, [
            'customerName' => $customerName,
            'postCode' => $shippingPostcode
        ]));

        $this->assertNotNull($this->getEntity(Item::class, [
            'sku' => 'item_123a',
            'description' => 'Test Item 1'
        ]));
    }

    public function test_update(): void
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy([
            'customerName' => 'Test User'
        ]);

        $order->setShippingAddress1('20');
        $order->setShippingAddress2('Lane Mark Street');
        $order->setStatus(OrderStatus::COMPLETED->value);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->assertEquals(1, $order->getId());

        $this->assertNotNull($this->getEntity(Order::class, [
            'customerName' => 'Test User',
            'shippingAddress1' => '20',
            'shippingAddress2' => 'Lane Mark Street',
            'status' => 2
        ]));
    }

    protected function _createTestDataset(): array
    {
        return [
            Order::class => [
                [
                    'external_ref' => 'A',
                    'customer_name' => 'Test User',
                    'shipping_address1' => '1',
                    'post_code' => 'TT1 1TT',
                    'city' => 'Test City',
                    'country_code' => 'GB',
                    'email' => 'test@test.com',
                    'price' => 5.50,
                    'currency' => 'GBP',
                    'status' => OrderStatus::STARTED->value
                ]
            ]
        ];
    }
}