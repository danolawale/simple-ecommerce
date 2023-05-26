<?php

namespace App\ThirdParty\EasyPost;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final class Api implements ApiInterface
{
    private const BASE_URL = 'https://api.easypost.com/v2';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $easypostApiKey
    ) {
    }

    public function createShipment(array $shipment): array
    {
        try {
            $response = $this->httpClient->request(
                method: 'POST',
                url: self::BASE_URL . "/shipments",
                options: [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'auth_basic' => [$this->easypostApiKey],
                    'body' => $shipment
                ]
            );

            $result = json_decode($response->getContent(), true);

            Assert::keyExists($result, 'messages', 'Invalid Response object. Messages Key not found');

            if (count($result['messages']) > 0) {
                $this->getShipmentErrors($result['messages']);
            }

            Assert::keyExists($result, 'id', 'Unable to determine the shipment Id');
            Assert::keyExists($result, 'reference', 'Unable to reconcile response with request');

            Assert::keyExists($result, 'rates');

            $rate = $this->findCheapestRate($result['rates']);

            return [
                'orderRef' => $result['reference'],
                ... $rate
            ];
        } catch (InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }


    public function buyShipment(array $buyShipmentPayload, string $shipmentId): array
    {
        try {
            $response = $this->httpClient->request(
                method: 'POST',
                url: self::BASE_URL . "/shipments/$shipmentId/buy",
                options: [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'auth_basic' => [$this->easypostApiKey],
                    'body' => $buyShipmentPayload
                ]
            );

            $result = json_decode($response->getContent(), true);

            Assert::keyExists($result, 'messages', 'Invalid Response object. Messages Key not found');

            if (count($result['messages']) > 0) {
                $this->getShipmentErrors($result['messages']);
            }

            Assert::keyExists($result, 'id', 'Unable to determine the shipment Id');
            Assert::true($result['id'] === $shipmentId, "Unable to match shipment Id");
            Assert::keyExists($result, 'tracking_code', 'Unable to determine the tracking code');
            Assert::keyExists($result, 'postage_label', 'Unable to find postage data');
            Assert::keyExists($result['postage_label'], 'label_url', 'Unable to find label url');

            return [
                'shipmentId' => $result['id'],
                'trackingCode' => $result['tracking_code'],
                'labelUrl' => $result['postage_label']['label_url']
            ];
        } catch (InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    private function findCheapestRate(array $rates): array
    {
        uasort($rates, static fn(array $a, array $b) => $a['rate'] <=> $b['rate']);

        $cheapestRate = $rates[0] ?? null;

        if ($cheapestRate === null) {
            throw new BadRequestHttpException("Unable to find rate info for shipment");
        }

        Assert::keyExists($cheapestRate, 'id');
        Assert::keyExists($cheapestRate, 'carrier');
        Assert::keyExists($cheapestRate, 'rate');
        Assert::keyExists($cheapestRate, 'shipment_id');

        return [
            'rateId' => $cheapestRate['id'],
            'carrier' => $cheapestRate['carrier'],
            'rate' => $cheapestRate['rate'],
            'shipmentId' => $cheapestRate['shipment_id'],
        ];
    }

    private function getShipmentErrors(array $errors): void
    {
        $messages = array_map(static fn(array $message): string => $message['message'], $errors);

        throw new BadRequestHttpException(implode('; ', $messages));
    }
}
