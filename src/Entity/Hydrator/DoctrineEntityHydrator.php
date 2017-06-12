<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine-orm-bridge
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ORM\Entity\Hydrator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Vainyl\Entity\EntityInterface;
use Vainyl\Entity\Hydrator\EntityHydratorInterface;

/**
 * Class DoctrineEntityHydrator
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityHydrator implements EntityHydratorInterface
{
    private $entityManager;

    /**
     * DoctrineEntityFactory constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $entityName
     *
     * @return ClassMetadata
     */
    public function getClassMetadata(string $entityName): ClassMetadata
    {
        return $this->entityManager->getClassMetadata($entityName);
    }

    /**
     * @param EntityInterface $entity
     * @param ClassMetadata   $classMetadata
     * @param array           $entityData
     *
     * @return EntityInterface
     */
    public function populate(
        EntityInterface $entity,
        ClassMetadata $classMetadata,
        array $entityData
    ): EntityInterface {
        $parsedData = [];
        foreach ($entityData as $column => $value) {
            if (array_key_exists($column, $classMetadata->fieldNames)) {
                $parsedData[$classMetadata->fieldNames[$column]] = $value;
                continue;
            }
            foreach ($classMetadata->associationMappings as $associationMapping) {
                if (false === $associationMapping['type'] <= 2) {
                    continue;
                }
                if ($column !== $associationMapping['joinColumns'][0]['name']) {
                    continue;
                }
                if (null === ($associatedEntity = $this->entityManager->find(
                        $associationMapping['targetEntity'],
                        $value
                    ))
                ) {
                    continue;
                }
                $parsedData[$associationMapping['fieldName']] = $associatedEntity;
            }
        }

        return $entity->fromArray($parsedData);
    }

    /**
     * @inheritDoc
     */
    public function create(string $entityName, array $entityData): EntityInterface
    {
        $classMetadata = $this->getClassMetadata($entityName);
        /**
         * @var EntityInterface $entity
         */
        $entity = $classMetadata->getReflectionClass()->newInstance();

        return $this->populate($entity, $classMetadata, $entityData);
    }

    /**
     * @inheritDoc
     */
    public function updateEntity(EntityInterface $entity, array $entityData): EntityInterface
    {
        $classMetadata = $this->getClassMetadata(get_class($entity));

        return $this->populate($entity, $classMetadata, $entityData);
    }
}