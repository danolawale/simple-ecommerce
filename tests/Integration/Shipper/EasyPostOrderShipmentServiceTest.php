<?php

namespace App\Tests\Integration\Shipper;

use App\Integration\Shipper\EasyPost\EasyPostOrderShipmentService;
use App\Integration\Shipper\EasyPost\EasyPostOrderShipmentServiceInterface;
use App\Repository\OrderRepository;
use App\Tests\Factory\TestOrderFactory;
use App\ThirdParty\EasyPost\ApiInterface;
use PHPUnit\Framework\TestCase;

class EasyPostOrderShipmentServiceTest extends TestCase
{
    private ApiInterface $api;
    private OrderRepository $orderRepository;
    private EasyPostOrderShipmentServiceInterface $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->api = $this->createMock(ApiInterface::class);
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->service = new EasyPostOrderShipmentService($this->api, $this->orderRepository);
    }

    public function testCreateShipment(): void
    {
        $order = TestOrderFactory::createOrderWithNoItem(externalRef: 'test123');

        $this->orderRepository
            ->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(['externalRef' => 'test123']))
            ->willReturn([$order]);

        $this->api
            ->expects($this->once())
            ->method('createShipment')
            ->with($this->callback(function(array $shipment) {
                $this->assertArrayHasKey('shipment', $shipment);
                $this->assertArrayHasKey('reference', $shipment['shipment']);
                $this->assertArrayHasKey('to_address', $shipment['shipment']);
                $this->assertArrayHasKey('from_address', $shipment['shipment']);

                $this->assertEquals('test123', $shipment['shipment']['reference']);

                return true;
            }))
            ->willReturn([
                'orderRef' => 'test123',
                'rateId' => 'rate123',
                'shipmentId' => 'shipment123'
            ]);

        $result = $this->service->createShipment('test123');

        $this->assertEquals([
            'orderRef' => 'test123',
            'rateId' => 'rate123',
            'shipmentId' => 'shipment123'
        ], $result);
    }

    public function testBuyShipment(): void
    {
        $order = TestOrderFactory::createOrderWithNoItem(externalRef: 'test123');

        $buyPayload = [  'rate' => ['mode' => 'test', 'id' => 'rate123' ] ];

        $this->api
            ->expects($this->once())
            ->method('buyShipment')
            ->with($buyPayload, 'shipment123')
            ->willReturn([
                'shipmentId' => 'shipment123',
                'labelUrl' => 'label.png',
                'trackingCode' =>'trackingCode123'
            ]);

        $this->orderRepository
            ->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(['externalRef' => 'test123']))
            ->willReturn([$order]);

        $this->orderRepository
            ->expects($this->once())
            ->method('save')
            ->with($order, true);

        $result = $this->service->buyShipment([
            'rateId' => 'rate123',
            'shipmentId' => 'shipment123',
            'orderRef' => $order->getExternalRef()
        ]);

        $this->assertEquals('label.png', $result['label']);
    }
}