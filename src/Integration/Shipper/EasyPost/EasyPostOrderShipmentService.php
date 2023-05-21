<?php

namespace App\Integration\Shipper\EasyPost;

use App\Entity\Enums\OrderStatus;
use App\Repository\OrderRepository;
use App\ThirdParty\EasyPost\ApiInterface;

class EasyPostOrderShipmentService implements EasyPostOrderShipmentServiceInterface
{
    public function __construct(
        private readonly ApiInterface $api,
        private readonly OrderRepository $orderRepository
    ) {
    }

    public function createShipment(string $orderRef): array
    {
        $order = $this->orderRepository->findBy(['externalRef' => $orderRef])[0];

        $shipmentDetails = [
            'shipment' => [
                'reference' => $order->getExternalRef(),
                'mode' => 'test',
                'to_address' => [
                    'name' => $order->getCustomerName(),
                    'street1' => $order->getShippingAddress1(),
                    'city' => $order->getCity(),
                    'state' => $order->getShippingAddress3(),
                    'zip' => $order->getPostCode(),
                    'country' => $order->getCountryCode(),
                    'phone' => '',
                    'email' => $order->getEmail()
                ],
                'from_address' => [
                    'name' => '9xb TestShipper',
                    'street1' => '311 New Bern Ave',
                    'street2' => 'Raleigh',
                    'city' => 'North Carolina',
                    'state' => 'NC',
                    'zip' => '27601',
                    'country' => 'US',
                    'phone' => '',
                    'email' => 'dan.olawale2@gmail.com'
                ],
                'parcel' => [
                    'length' => 20,
                    'width' => 20,
                    'height' => 20,
                    'weight' => '65.0'
                ]
            ]
        ];

        return $this->api->createShipment($shipmentDetails);
    }

    public function buyShipment(array $shipmentInfo): array
    {
        $buyShipmentPayload = [
            'rate' => ['mode' => 'test', 'id' => $shipmentInfo['rateId'] ]
        ];

        $result = $this->api->buyShipment($buyShipmentPayload, $shipmentInfo['shipmentId']);print_r($result);

        $order = $this->orderRepository->findBy(['externalRef' => $shipmentInfo['reference']])[0];

        $order->setStatus(OrderStatus::COMPLETED->value);

        $this->orderRepository->save($order, true);

        return [];
    }
}