<?php
declare(strict_types=1);

namespace App\Integration\Retailer\Ebay;

use App\Entity\Enums\OrderStatus;
use App\Integration\Retailer\LoadCustomerOrderServiceInterface;
use App\Repository\OrderRepository;
use App\ThirdParty\Ebay\ApiInterface;
use App\Transformer\OrderItemTransformer;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class LoadEbayOrderService implements LoadCustomerOrderServiceInterface
{
    public function __construct(
        private readonly ApiInterface $api,
        private readonly OrderRepository $orderRepository
    ) {
    }

    public function load(): void
    {
        try {
            $orderData = $this->api->getOrder();

            $buyerDetails = $orderData['buyer']['buyerRegistrationAddress'];
            $buyerAddress = $buyerDetails['contactAddress'];
            $shipTo = $orderData['fulfillmentStartInstructions'][0]['shippingStep']['shipTo'];
            $shippingAddress = $shipTo['contactAddress'];
            $items = $orderData['lineItems'];

            $orderDetails = [
                'externalRef' => $orderData['orderId'],
                'status' => OrderStatus::STARTED->value,
                'customerName' => $shipTo['fullName'] ?: $buyerDetails['fullName'],
                'shippingAddress1' => $shippingAddress['addressLine1'] ?: $buyerAddress['addressLine1'],
                'city' => $shippingAddress['city'] ?: $buyerAddress['city'],
                'postCode' => $shippingAddress['postalCode'] ?: $buyerAddress['postalCode'],
                'countryCode' => $shippingAddress['countryCode'] ?: $buyerAddress['countryCode'],
                'email' => $shipTo['email'] ?: $buyerDetails['email'],
                'price' => $orderData['pricingSummary']['total']['value'],
                'currency' => $orderData['pricingSummary']['total']['currency'],
                'items' => array_map(function(array $item): array {
                    return [
                        'sku' => $item['lineItemId'],
                        'description' => $item['title'],
                        'quantity' => $item['quantity'],
                        'price' => $item['lineItemCost']['value'],
                    ];
                }, $items)
            ];

            $order = OrderItemTransformer::transform($orderDetails);

            $this->orderRepository->save($order, true);

        } catch(InvalidArgumentException $exception) {
            throw new BadRequestException($exception->getMessage());
        }

    }
}