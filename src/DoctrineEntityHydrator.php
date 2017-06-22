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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Vainyl\Core\ArrayInterface;
use Vainyl\Core\Hydrator\AbstractHydrator;
use Vainyl\Core\Hydrator\HydratorInterface;
use Vainyl\Doctrine\ORM\Exception\MissingDiscriminatorColumnException;
use Vainyl\Doctrine\ORM\Exception\MissingIdentifierColumnException;
use Vainyl\Doctrine\ORM\Exception\UnknownDiscriminatorValueException;
use Vainyl\Doctrine\ORM\Exception\UnknownReferenceEntityException;
use Vainyl\Entity\EntityInterface;

/**
 * Class DoctrineEntityHydrator
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityHydrator extends AbstractHydrator implements HydratorInterface
{
    private $metadataFactory;

    private $databasePlatform;

    private $entityManager;

    /**
     * DoctrineEntityHydrator constructor.
     *
     * @param ClassMetadataFactory   $metadataFactory
     * @param AbstractPlatform       $databasePlatform
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ClassMetadataFactory $metadataFactory,
        AbstractPlatform $databasePlatform,
        EntityManagerInterface $entityManager
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->databasePlatform = $databasePlatform;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $class): bool
    {
        try {
            $this->metadataFactory->getMetadataFor($class);
        } catch (MappingException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param array             $entityData
     * @param ClassMetadataInfo $classMetadata
     *
     * @return EntityInterface
     */
    protected function findReference(array $entityData, ClassMetadataInfo $classMetadata): EntityInterface
    {
        $identifier = $classMetadata->identifier[0];
        if (false === array_key_exists($identifier, $entityData)) {
            throw new MissingIdentifierColumnException(
                $this,
                $classMetadata->identifier[0],
                $entityData
            );
        }
        /**
         * @var EntityInterface $reference
         */
        if (null === ($reference = $this->entityManager->find(
                $classMetadata,
                $entityData[$identifier]
            ))
        ) {
            throw new UnknownReferenceEntityException(
                $this,
                $classMetadata->name,
                $entityData[$identifier]
            );
        }

        return $reference;
    }

    /**
     * @param array             $externalData
     * @param EntityInterface   $entity
     * @param ClassMetadataInfo $classMetadata
     *
     * @return EntityInterface
     */
    protected function populateEntity(
        array $externalData,
        EntityInterface $entity,
        ClassMetadataInfo $classMetadata
    ): EntityInterface {
        foreach ($externalData as $field => $value) {
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
                    /**
                     * @var ClassMetadataInfo $referenceMetadata
                     */
                    $referenceMetadata = $this->metadataFactory->getMetadataFor($referenceEntity);
                    switch ($associationMapping['type']) {
                        case ClassMetadataInfo::ONE_TO_ONE:
                        case ClassMetadataInfo::MANY_TO_ONE:
                            $classMetadata->reflFields[$associationMapping['fieldName']]
                                ->setValue(
                                    $entity,
                                    $this->findReference($value, $referenceMetadata)
                                );
                            break;
                        case ClassMetadataInfo::ONE_TO_MANY:
                        case ClassMetadataInfo::MANY_TO_MANY:
                            $collection = new ArrayCollection();
                            foreach ($value as $referenceData) {
                                $collection->add($this->findReference($referenceData, $referenceMetadata));
                            }
                            $classMetadata->reflFields[$associationMapping['fieldName']]
                                ->setValue(
                                    $entity,
                                    $collection
                                );

                            break;
                    }
            }
        }

        return $entity;
    }

    /**
     * @param array             $entityData
     * @param ClassMetadataInfo $classMetadata
     *
     * @return string
     */
    public function getChildEntityName(array $entityData, ClassMetadataInfo $classMetadata): string
    {
        if (ClassMetadataInfo::INHERITANCE_TYPE_NONE === $classMetadata->inheritanceType) {
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
    public function doHydrate(string $entityName, array $externalData): ArrayInterface
    {
        /**
         * @var ClassMetadataInfo $rootClassMetadata
         * @var ClassMetadataInfo $childClassMetadata
         * @var EntityInterface   $entity
         */
        $rootClassMetadata = $this->metadataFactory->getMetadataFor($entityName);
        $childEntityName = $this->getChildEntityName($externalData, $rootClassMetadata);
        $childClassMetadata = $this->metadataFactory->getMetadataFor($childEntityName);
        $entity = $childClassMetadata->newInstance();

        return $this->populateEntity($externalData, $entity, $childClassMetadata);
    }
}