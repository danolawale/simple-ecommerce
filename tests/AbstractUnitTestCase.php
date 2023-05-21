<?php

namespace App\Tests;

use App\DataFixtures\AppFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\DataFixtures\AppFixturesFactory;
use App\DataFixtures\EntityFixturesInterface;
use App\Entity\Utility\EntityMappingHandler;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Monolog\Handler\Handler;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractUnitTestCase extends KernelTestCase
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->truncateEntities($kernel); //comment this out if working on sqlite

        SchemaLoader::load($this->entityManager);

        $this->createDataset();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    private function truncateEntities(KernelInterface $kernel)
    {
        //this does not truncate the tables, just deletes the entries
        //$purger = new ORMPurger($this->entityManager);
        //$purger->purge();
        $this->entityManager->getConnection()->executeUpdate("SET foreign_key_checks = 0;");

        DatabasePrimer::truncateAll($kernel);

        $this->entityManager->getConnection()->executeUpdate("SET foreign_key_checks = 1;");
    }

    protected function _createTestDataset(): array
    {
        return [];
    }

    protected function createDataset(?array $dataset = null)
    {
        $dataset ??= $this->_createTestDataset();

        if($dataset && !empty($dataset))
        {
            $fixtureFactory = new AppFixturesFactory();

            $fixtures = array_map(function($entityName, $values) use ($fixtureFactory)
            {
                $handler = ($fixtureFactory)($entityName);

                if($handler instanceof EntityFixturesInterface)
                {
                    $handler->setFixturesData($values);

                    return $handler->load($this->entityManager);
                }
                else
                {
                    throw new \Exception("Invalid fixture for entity {$entityName}");
                }

                
            },array_keys($dataset), $dataset);
        }
    }

    public function getSortedDataset(array $dataset): array
    {
        $sorted = [];

        $entityMappingHandler = new EntityMappingHandler;

        foreach($dataset as $entityName => $data)
        {
            if(!isset($sorted[$entityName]))
            {
                $metadata = $this->entityManager->getClassMetadata($entityName);

                $entityMappingHandler->setMappedAssociation($metadata->associationMappings);

                $foreignData = $entityMappingHandler->getForeignKeysData();

                if(empty($foreignData))
                {
                    $sorted[$entityName] = $data;
                }
                else
                {
                    foreach($foreignData as $foreignKey => $info)
                    {
                        $source = $info['source'];

                        if($dataset[$source] ?? null)
                        {
                            $sorted[$source] = $dataset[$source];
                        }
                    }

                    if(!isset($sorted[$entityName]))
                    {
                        $sorted[$entityName] = $data;
                    }
                }
            }
        }

        return $sorted;  
    }

    public function getEntity(string $entityName, array $criteria)
    {
        return $this->entityManager->getRepository($entityName)->findOneBy($criteria);
    }
}