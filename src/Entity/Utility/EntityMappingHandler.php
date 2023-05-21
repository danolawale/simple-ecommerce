<?php

namespace App\Entity\Utility;

final class EntityMappingHandler
{
    private array $mappedAssociationData;

    public function setMappedAssociation(array $data): void
    {
        $this->mappedAssociationData = $data;
    }

    public function getForeignKeysData(): array
    {
        $foreignKeysMetadata = [];

        foreach($this->mappedAssociationData as $key => $data)
        {
            $targetEntity = $data['targetEntity'];

            $foreignData = $data['joinColumns'] ?? [];

            foreach($foreignData as $properties)
            {
                $foreignKey = $properties['name'];

                $foreignKeysMetadata[$foreignKey] = [
                    'foreignKey' => $foreignKey,
                    'fieldName' => $data['fieldName'],
                    'source' => $targetEntity,
                ];
            }
        }

        return $foreignKeysMetadata;
    }
}