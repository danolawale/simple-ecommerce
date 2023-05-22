<?php
declare(strict_types=1);

namespace App\ThirdParty\Ebay;

use stdClass;

class MockResponse
{
    public static function getOrder(): string
    {
        $details = self::orderDetails();
        $address= $details->address;

        return <<<JSON
{
  "orderId": "$details->orderId",
  "legacyOrderId": "254333",
  "creationDate": "2020-05-12T07:58:13.000Z",
  "lastModifiedDate": "2020-05-14T22:25:26.000Z",
  "orderFulfillmentStatus": "NOT_STARTED",
  "orderPaymentStatus": "PAID",
  "sellerId": "k********1",
  "buyer": {
    "username": "m********r",
    "taxAddress": {
      "city": "S********e",
      "stateOrProvince": "**",
      "postalCode": "9***",
      "countryCode": "US"
    },
    "buyerRegistrationAddress": {
      "fullName": "$details->name",
      "contactAddress": {
        "addressLine1": "$address->address1",
        "city": "$address->city",
        "stateOrProvince": "",
        "postalCode": "$address->postCode",
        "countryCode": "UK"
      },
      "primaryPhone": {
        "phoneNumber": ""
      },
      "email": "$details->email"
    }
  },
  "pricingSummary": {
    "priceSubtotal": {
      "value": "263.0",
      "currency": "USD"
    },
    "deliveryCost": {
      "value": "1.0",
      "currency": "USD"
    },
    "tax": {
      "value": "24.33",
      "currency": "USD"
    },
    "fee": {
      "value": "6.0",
      "currency": "USD"
    },
    "total": {
      "value": "$details->price",
      "currency": "USD"
    }
  },
  "cancelStatus": {
    "cancelState": "NONE_REQUESTED",
    "cancelRequests": []
  },
  "paymentSummary": {
    "totalDueSeller": {
      "value": "294.33",
      "currency": "USD"
    },
    "refunds": [],
    "payments": [
      {
        "paymentMethod": "EBAY",
        "paymentReferenceId": "2********L",
        "paymentDate": "2020-05-12T07:58:15.000Z",
        "amount": {
          "value": "$details->price",
          "currency": "USD"
        },
        "paymentStatus": "PAID"
      }
    ]
  },
  "fulfillmentStartInstructions": [
    {
      "fulfillmentInstructionsType": "SHIP_TO",
      "minEstimatedDeliveryDate": "2020-05-20T07:00:00.000Z",
      "maxEstimatedDeliveryDate": "2020-05-20T07:00:00.000Z",
      "ebaySupportedFulfillment": false,
      "shippingStep": {
        "shipTo": {
          "fullName": "$details->name",
          "contactAddress": {
            "addressLine1": "$address->address1",
            "city": "$address->city",
            "stateOrProvince": "**",
            "postalCode": "$address->postCode",
            "countryCode": "US"
          },
          "primaryPhone": {
            "phoneNumber": "4********0"
          },
          "email": "$details->email"
        },
        "shippingServiceCode": "ShippingMethodExpress"
      }
    }
  ],
  "fulfillmentHrefs": [],
  "lineItems": [
    {
      "lineItemId": "$details->itemId",
      "legacyItemId": "2********4",
      "title": "$details->itemName",
      "lineItemCost": {
        "value": "$details->price",
        "currency": "USD"
      },
      "quantity": 1,
      "soldFormat": "FIXED_PRICE",
      "listingMarketplaceId": "EBAY_US",
      "purchaseMarketplaceId": "EBAY_US",
      "lineItemFulfillmentStatus": "NOT_STARTED",
      "total": {
        "value": "294.33",
        "currency": "USD"
      },
      "deliveryCost": {
        "shippingCost": {
          "value": "1.0",
          "currency": "USD"
        }
      },
      "appliedPromotions": [],
      "taxes": [
        {
          "amount": {
            "value": "6.0",
            "currency": "USD"
          },
          "taxType": "ELECTRONIC_RECYCLING_FEE"
        },
        {
          "amount": {
            "value": "24.33",
            "currency": "USD"
          },
          "taxType": "STATE_SALES_TAX"
        }
      ],
      "ebayCollectAndRemitTaxes": [
        {
          "taxType": "STATE_SALES_TAX",
          "amount": {
            "value": "24.33",
            "currency": "USD"
          },
          "collectionMethod": "NET"
        },
        {
          "taxType": "ELECTRONIC_RECYCLING_FEE",
          "amount": {
            "value": "6.0",
            "currency": "USD"
          },
          "collectionMethod": "NET"
        }
      ],
      "properties": {
        "buyerProtection": true
      },
      "lineItemFulfillmentInstructions": {
        "minEstimatedDeliveryDate": "2020-05-20T07:00:00.000Z",
        "maxEstimatedDeliveryDate": "2020-05-20T07:00:00.000Z",
        "shipByDate": "2020-05-14T06:59:59.000Z",
        "guaranteedDelivery": false
      }
    }
  ],
  "ebayCollectAndRemitTax": true,
  "salesRecordReference": "8**7",
  "totalFeeBasisAmount": {
    "value": "294.33",
    "currency": "USD"
  }
}
JSON;

    }

    private static function orderDetails(): stdClass
    {
        $details = [
            [
                'name' => 'David Hawthorne',
                'orderId' => date('ymdHis'),
                'itemId' => "255764",
                'itemName' => "Honor 20 Mobile Phone Cover",
                'price' => 22.55,
                'email' => 'david.hawthorne@test.com',
                'address' => [
                    'address1' => '4918 NC-704',
                    'address2' => 'Sandy Ridge',
                    'address3' => 'NC',
                    'city' => 'North Carolina',
                    'postCode' => '27046'
                ]
            ],
            [
                'name' => 'Jane Kelly',
                'orderId' => date('ymdHis'),
                'itemId' => "155467",
                'itemName' => "LG 55 Inch 2020 Television",
                'price' => 785.80,
                'email' => 'jane.kelly@test.com',
                'address' => [
                    'address1' => '8323 Linville Rd',
                    'address2' => 'Oak Ridge',
                    'address3' => 'NC',
                    'city' => 'North Carolina',
                    'postCode' => '27310'
                ]
            ],
            [
                'name' => 'Matt Stone',
                'orderId' => date('ymdHis'),
                'itemId' => "322564",
                'itemName' => "AAA32 Alkaline Power",
                'price' => 15.99,
                'email' => 'matt.stone@test.com',
                'address' => [
                    'address1' => '5161 NC-211',
                    'address2' => 'West End',
                    'address3' => 'NC',
                    'city' => 'North Carolina',
                    'postCode' => '27376'
                ]
            ],
        ];

        $details = $details[array_rand($details)];

        return json_decode(json_encode($details));
    }
}