<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderNotFoundException extends NotFoundHttpException
{
    public static function withRef(string $orderRef): self
    {
        return new self(sprintf('Order with reference "%s" not found', $orderRef));
    }
}