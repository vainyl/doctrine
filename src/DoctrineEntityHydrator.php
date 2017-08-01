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
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Vainyl\Core\ArrayInterface;
use Vainyl\Core\Hydrator\AbstractHydrator;
use Vainyl\Core\Hydrator\HydratorInterface;
use Vainyl\Core\Hydrator\Registry\HydratorRegistryInterface;
use Vainyl\Doctrine\ORM\Exception\MissingDiscriminatorColumnException;
use Vainyl\Doctrine\ORM\Exception\UnknownDiscriminatorValueException;
use Vainyl\Doctrine\ORM\Exception\UnknownReferenceEntityException;
use Vainyl\Domain\Hydrator\DomainHydratorInterface;
use Vainyl\Domain\Storage\DomainStorageInterface;
use Vainyl\Entity\EntityInterface;

/**
 * Class DoctrineEntityHydrator
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityHydrator extends AbstractHydrator implements DomainHydratorInterface
{
    private $hydratorRegistry;

    private $domainStorage;

    private $databasePlatform;

    private $metadataFactory;

    /**
     * DoctrineEntityHydrator constructor.
     *
     * @param HydratorRegistryInterface $hydratorRegistry
     * @param DomainStorageInterface    $domainStorage
     * @param AbstractPlatform          $databasePlatform
     * @param ClassMetadataFactory      $metadataFactory
     */
    public function __construct(
        HydratorRegistryInterface $hydratorRegistry,
        DomainStorageInterface $domainStorage,
        AbstractPlatform $databasePlatform,
        ClassMetadataFactory $metadataFactory
    ) {
        $this->hydratorRegistry = $hydratorRegistry;
        $this->domainStorage = $domainStorage;
        $this->databasePlatform = $databasePlatform;
        $this->metadataFactory = $metadataFactory;
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
            $reflectionField = $processedValue = null;
            switch (true) {
                case array_key_exists($field, $classMetadata->fieldMappings):
                    $fieldMapping = $classMetadata->fieldMappings[$field];
                    $reflectionField = $classMetadata->reflFields[$fieldMapping['fieldName']];
                    $processedValue = Type::getType($fieldMapping['type'])->convertToPHPValue(
                        $value,
                        $this->databasePlatform
                    );
                    break;
                case array_key_exists($field, $classMetadata->associationMappings):
                    $associationMapping = $classMetadata->associationMappings[$field];
                    $referenceEntity = $associationMapping['targetEntity'];
                    $reflectionField = $classMetadata->reflFields[$associationMapping['fieldName']];
                    switch ($associationMapping['type']) {
                        case ClassMetadata::ONE_TO_ONE:
                        case ClassMetadata::MANY_TO_ONE:
                            if (null === ($processedValue = $this->domainStorage->findOne($referenceEntity, $value))) {
                                throw new UnknownReferenceEntityException($this, $referenceEntity, $value);
                            }
                            break;
                        case ClassMetadata::ONE_TO_MANY:
                        case ClassMetadata::MANY_TO_MANY:
                            $processedValue = new ArrayCollection();
                            foreach ($value as $referenceData) {
                                $processedValue->add($this->domainStorage->findOne($referenceEntity, $referenceData));
                            }
                            break;
                    }
                    break;
            }
            if (null !== $reflectionField) {
                $reflectionField->setValue($entity, $processedValue);
            }
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function doUpdate($entity, array $entityData): ArrayInterface
    {
        /**
         * @var ClassMetadata   $classMetadata
         * @var EntityInterface $entity
         */
        $classMetadata = $this->metadataFactory->getMetadataFor(get_class($entity));

        foreach ($entityData as $field => $value) {
            $reflectionField = $processedValue = null;
            switch (true) {
                case array_key_exists($field, $classMetadata->fieldMappings):
                    $fieldMapping = $classMetadata->fieldMappings[$field];
                    $reflectionField = $classMetadata->reflFields[$fieldMapping['fieldName']];
                    $processedValue = Type::getType($fieldMapping['type'])->convertToPHPValue(
                        $value,
                        $this->databasePlatform
                    );
                    break;
            }
            if (null !== $reflectionField) {
                $reflectionField->setValue($entity, $processedValue);
            }
        }

        return $entity;
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
     * @param string $className
     *
     * @return HydratorInterface
     */
    public function getHydrator(string $className): HydratorInterface
    {
        return $this->hydratorRegistry->getHydrator($className);
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
}