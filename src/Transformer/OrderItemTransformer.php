<?php

namespace App\Transformer;

use App\Entity\Item;
use App\Entity\Order;

class OrderItemTransformer
{
    public static function transform(array $orderDetails): Order
    {
        $items = $orderDetails['items'];
        unset($orderDetails['items']);

        $order = new Order;
        foreach ($orderDetails as $field => $value) {
            $setMethod = "set".ucfirst($field);

            $order->$setMethod($value);
        }

        foreach ($items as $itemData) {
            $item = new Item();

            $item->setSku($itemData['sku']);
            $item->setDescription($itemData['description']);
            $item->setQuantity($itemData['quantity']);
            $item->setPrice($itemData['price']);
            $item->setOrder($order);
        }

        return $order;

    }
}