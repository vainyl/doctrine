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

namespace Vainyl\Doctrine\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry as DoctrineRegistryInterface;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\Common\Persistence\ObjectRepository as DoctrineRepositoryInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Vainyl\Core\ArrayInterface;
use Vainyl\Core\Hydrator\AbstractHydrator;
use Vainyl\Doctrine\ORM\Exception\MissingDiscriminatorColumnException;
use Vainyl\Doctrine\ORM\Exception\UnknownDiscriminatorValueException;
use Vainyl\Entity\EntityInterface;

/**
 * Class DoctrineEntityHydrator
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityHydrator extends AbstractHydrator
{
    private $doctrineRegistry;

    private $databasePlatform;

    private $metadataFactory;

    /**
     * DoctrineEntityHydrator constructor.
     *
     * @param DoctrineRegistryInterface $doctrineRegistry
     * @param AbstractPlatform          $databasePlatform
     * @param ClassMetadataFactory      $metadataFactory
     */
    public function __construct(
        DoctrineRegistryInterface $doctrineRegistry,
        AbstractPlatform $databasePlatform,
        ClassMetadataFactory $metadataFactory
    ) {
        $this->doctrineRegistry = $doctrineRegistry;
        $this->databasePlatform = $databasePlatform;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $name): bool
    {
        try {
            $this->metadataFactory->getMetadataFor($name);
        } catch (MappingException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $className
     *
     * @return DoctrineRepositoryInterface
     */
    public function getRepository(string $className): DoctrineRepositoryInterface
    {
        return $this->doctrineRegistry->getRepository($className, 'entity');
    }

    /**
     * @param array         $entityData
     * @param ClassMetadata $classMetadata
     *
     * @return string
     */
    public function getEntityName(array $entityData, ClassMetadata $classMetadata): string
    {
        if (ClassMetadata::INHERITANCE_TYPE_NONE === $classMetadata->inheritanceType) {
            return $classMetadata->name;
        }

        if (false === array_key_exists($classMetadata->discriminatorColumn['name'], $entityData)) {
            throw new MissingDiscriminatorColumnException(
                $this,
                $classMetadata->discriminatorColumn['name'],
                $entityData
            );
        }

        $discriminatorColumnValue = $entityData[$classMetadata->discriminatorColumn['name']];
        if (false === array_key_exists($discriminatorColumnValue, $classMetadata->discriminatorMap)) {
            throw new UnknownDiscriminatorValueException(
                $this,
                $discriminatorColumnValue,
                $classMetadata->discriminatorMap
            );
        }

        return $classMetadata->discriminatorMap[$discriminatorColumnValue];
    }

    /**
     * @inheritDoc
     */
    public function doCreate(string $name, array $entityData = []): ArrayInterface
    {
        /**
         * @var ClassMetadata   $classMetadata
         * @var EntityInterface $entity
         */
        $entityName = $this->getEntityName($entityData, $this->metadataFactory->getMetadataFor($name));
        $classMetadata = $this->metadataFactory->getMetadataFor($entityName);
        $entity = $classMetadata->newInstance();

        foreach ($entityData as $field => $value) {
            switch (true) {
                case array_key_exists($field, $classMetadata->fieldMappings):
                    $fieldMapping = $classMetadata->fieldMappings[$field];
                    $classMetadata->reflFields[$fieldMapping['fieldName']]
                        ->setValue(
                            $entity,
                            Type::getType($fieldMapping['type'])
                                ->convertToPHPValue(
                                    $value,
                                    $this->databasePlatform
                                )
                        );
                    break;
                case array_key_exists($field, $classMetadata->associationMappings):
                    $associationMapping = $classMetadata->associationMappings[$field];
                    $referenceEntity = $associationMapping['targetEntity'];
                    switch ($associationMapping['type']) {
                        case ClassMetadata::ONE_TO_ONE:
                        case ClassMetadata::MANY_TO_ONE:
                            $classMetadata->reflFields[$associationMapping['fieldName']]
                                ->setValue(
                                    $entity,
                                    $this->getRepository($referenceEntity)->find($value)
                                );

                            break;
                        case ClassMetadata::ONE_TO_MANY:
                        case ClassMetadata::MANY_TO_MANY:
                            $collection = new ArrayCollection();
                            $repository = $this->getRepository($referenceEntity);
                            foreach ($value as $referenceData) {
                                $collection->add($repository->find($referenceData));
                            }
                            $classMetadata->reflFields[$associationMapping['fieldName']]
                                ->setValue(
                                    $entity,
                                    $collection
                                );

                            break;
                    }
                    break;
            }
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function doUpdate($object, array $data): ArrayInterface
    {
        trigger_error('Method doUpdate is not implemented', E_USER_ERROR);
    }
}