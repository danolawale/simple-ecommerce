<?php

namespace App\DataFixtures;

interface EntityFixturesInterface
{
    public function setFixturesData(array $data): void;
    public function getFixturesData(): array;
    public function getCustomFixturesData(): array;
}
