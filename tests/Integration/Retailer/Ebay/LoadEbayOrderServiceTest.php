<?php

namespace App\Tests\Integration\Retailer\Ebay;

use App\Entity\Item;
use App\Entity\Order;
use App\Integration\Retailer\Ebay\LoadEbayOrderService;
use App\Repository\OrderRepository;
use App\ThirdParty\Ebay\ApiInterface;
use PHPUnit\Framework\TestCase;

class LoadEbayOrderServiceTest extends TestCase
{
    public function testLoad(): void
    {
        $api = $this->createMock(ApiInterface::class);
        $orderRepository = $this->createMock(OrderRepository::class);

        $api
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn([
                'orderId' => 'test_ref123',
                'buyer' => [
                    'buyerRegistrationAddress' => [
                        'fullName' => 'customer name',
                        'contactAddress' => [
                            'addressLine1' => 1,
                            'city' => 'Test City',
                            'postalCode' => 'TT1 1TT',
                            'countryCode' => 'UK'
                        ],
                        'email' => 'test@test.com'
                    ]
                ],
                'pricingSummary' => [
                    'total' => [
                        'value' => 72.98,
                        'currency' => 'GBP'
                    ]
                ],
                'fulfillmentStartInstructions' => [
                    [
                        'shippingStep' => [
                            'shipTo' => [
                                'fullName' => 'customer name',
                                'contactAddress' => [
                                    'addressLine1' => 1,
                                    'city' => 'Test City',
                                    'postalCode' => 'TT1 1TT',
                                    'countryCode' => 'UK'
                                ],
                                'email' => 'test@test.com'
                            ]
                        ]
                    ]
                ],
                'lineItems' => [
                    [
                        'lineItemId' => 'item_1',
                        'title' => 'Item 1',
                        'lineItemCost' => [
                            'value' => '50.43',
                            'currency' => 'GBP'
                        ],
                        'quantity' => 2
                    ],
                    [
                        'lineItemId' => 'item_2',
                        'title' => 'Item 2',
                        'lineItemCost' => [
                            'value' => '22.55',
                            'currency' => 'GBP'
                        ],
                        'quantity' => 1
                    ]
                ]
            ]);

        $orderRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function(Order $order) {
                $this->assertInstanceOf(Order::class, $order);
                $this->assertEquals('test_ref123', $order->getExternalRef());
                $this->assertEquals('customer name', $order->getCustomerName());

                $items = $order->getItems();
                $this->assertCount(2, $items);

                $this->assertInstanceOf(Item::class, $items[0]);
                $this->assertEquals('item_1', $items[0]->getSku());

                $this->assertInstanceOf(Item::class, $items[1]);
                $this->assertEquals('item_2', $items[1]->getSku());

                return true;
            }), $this->equalTo(true));

        $result = (new LoadEbayOrderService($api, $orderRepository))->load();

        $this->assertEquals('test_ref123', $result);
    }
}