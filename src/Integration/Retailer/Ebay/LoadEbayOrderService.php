<?php
declare(strict_types=1);

namespace App\Integration\Retailer\Ebay;

use App\Integration\Retailer\LoadCustomerOrderServiceInterface;
use App\ThirdParty\Ebay\ApiInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class LoadEbayOrderService implements LoadCustomerOrderServiceInterface
{
    public function __construct(
        private readonly ApiInterface $api,
    ) {
    }

    public function load(): int
    {
        try {
            $order = $this->api->getOrder();

            $buyerDetails = $order['buyer']['buyerRegistrationAddress'];
            $buyerAddress = $buyerDetails['contactAddress'];
            $shipTo = $order['fulfillmentStartInstructions'][0]['shippingStep']['shipTo'];
            $shippingAddress = $order['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['contactAddress'];
            $items = $order['lineItems'];

            $orderDetails = [
                'externalRef' => $order['orderId'],
                //'status' => Order::LOADED,
                'customerName' => $shipTo['fullName'] ?: $buyerDetails['fullName'],
                'shipping_address_1' => $shippingAddress['addressLine1'] ?: $buyerAddress['addressLine1'],
                'city' => $shippingAddress['city'] ?: $buyerAddress['city'],
                'postCode' => $shippingAddress['postalCode'] ?: $buyerAddress['postalCode'],
                'countryCode' => $shippingAddress['countryCode'] ?: $buyerAddress['countryCode'],
                'email' => $shipTo['email'] ?: $buyerDetails['email'],
                'price' => $order['pricingSummary']['total']['value'],
                'currency' => $order['pricingSummary']['total']['currency'],
                'items' => array_map(function(array $item): array {
                    return [
                        'sku' => $item['lineItemId'],
                        'description' => $item['title'],
                        'quantity' => $item['quantity'],
                        'price' => $item['lineItemCost']['value'],
                    ];
                }, $items)
            ];

            return (int)$order['orderId'];

        } catch(InvalidArgumentException $exception) {
            throw new BadRequestException($exception->getMessage());
        }

    }
}