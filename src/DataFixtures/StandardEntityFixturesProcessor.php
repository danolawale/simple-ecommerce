<?php

namespace App\DataFixtures;

use Doctrine\ORM\Mapping\ClassMetadata;
use App\Entity\Utility\EntityMappingHandler;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

final class StandardEntityFixturesProcessor
{
    private EntityManagerInterface $entityManager;

    protected array $data;

    public function __construct(private EntityMappingHandler $entityMappingHandler)
    {
        
    }

    public function load(ObjectManager $manager, array $dataset, string $entityName): void
    {
        $this->entityManager = $manager;

        $metadata = $manager->getClassMetadata($entityName);
        
        $mappedAssociation = $metadata->associationMappings;

        $this->entityMappingHandler->setMappedAssociation($mappedAssociation);

        $foreignData = $this->entityMappingHandler->getForeignKeysData();

        foreach($dataset as $datum)
        {
            $entity = new $entityName();

            foreach($datum as $key => $value)
            {
                $fieldName = $this->getFieldName($metadata, $foreignData, $key);
                $fieldValue = $this->getFieldValue($foreignData, $key, $value);
              
                $methodName = "set". $this->getMethodName($fieldName);

                if(method_exists($entityName, $methodName))
                {
                    $entity->$methodName($fieldValue);
                } 
            }

            $manager->persist($entity);
        }

        $manager->flush();

        $this->data = [];
    }

    private function getFieldName(ClassMetadata $metadata, array $foreignData, string $fieldName): string
    {
        if($fieldName = $metadata->getFieldName($fieldName))
        {
            return ($foreignData[$fieldName] ?? null) 
                ? $foreignData[$fieldName]['fieldName'] 
                : $fieldName;
        }
        else
        {
            throw new \Exception("Field {$fieldName} does not exist in {$metadata->name}");
        }
    }

    private function getFieldValue(array $foreignData, string $fieldName, $fieldValue = null)
    {
        if(($foreignData[$fieldName] ?? null) && null !== $fieldValue)
        {
            $source = $foreignData[$fieldName]['source'];

            return $this->entityManager->getRepository($source)->find($fieldValue);
        }
        else
        {
            return $fieldValue;
        }
    }

    private function getMethodName(string $fieldName): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName)));
    }
}