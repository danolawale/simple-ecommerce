<?php

namespace App\ThirdParty\Ebay;

interface ApiInterface
{
    public function getOrder(): array;
}