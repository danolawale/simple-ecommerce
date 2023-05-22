<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Utility\EntityMappingHandler;

class GenericEntityFixtures extends Fixture implements EntityFixturesInterface
{
    protected array $data;
    private string $entityName;

    private StandardEntityFixturesProcessor $standardEntityFixturesProcessor;

    public function __construct()
    {
        $this->standardEntityFixturesProcessor
        = new StandardEntityFixturesProcessor(new EntityMappingHandler());
    }

    public function setEntityName(string $entityName): void
    {
        $this->entityName = $entityName;
    }

    public function load(ObjectManager $manager): void
    {
        $dataset = $this->getFixturesData();

        $this->standardEntityFixturesProcessor->load($manager, $dataset, $this->entityName);
    }

    public function setFixturesData(array $data): void
    {
        $this->data = $data;
    }

    public function getFixturesData(): array
    {
        return $this->data ?: $this->getCustomFixturesData();
    }

    public function getCustomFixturesData(): array
    {
        return [ ];
    }
}
