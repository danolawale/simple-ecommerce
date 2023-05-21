<?php

namespace App\Tests\Entity;

use App\Entity\Enums\OrderStatus;
use App\Entity\Item;
use App\Entity\Order;
use App\Tests\AbstractUnitTestCase;

class ItemTest extends AbstractUnitTestCase
{
    public function test_create(): void
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy([
            'externalRef' => 'A'
        ]);

        $item = new Item();
        $item->setSku('item_b');
        $item->setQuantity(1);
        $item->setDescription("Nike's Tennis Rackets");

        $item->setOrder($order);
        $this->entityManager->persist($item);
        $this->entityManager->flush();

        $items = $this->entityManager->getRepository(Item::class)->findAll();
        $this->assertCount(2, $items);

        $this->assertEquals('item_a', $items[0]->getSku());
        $this->assertEquals('item_b', $items[1]->getSku());
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
            ],
            Item::class => [
                [
                    'order_id' => 1,
                    'quantity' => 1,
                    'sku' => 'item_a',
                    'description' => "Test Item1",

                ]
            ]
        ];
    }

}