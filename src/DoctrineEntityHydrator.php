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
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Doctrine\ORM\Exception\MissingDiscriminatorColumnException;
use Vainyl\Doctrine\ORM\Exception\MissingIdentifierColumnException;
use Vainyl\Doctrine\ORM\Exception\UnknownDiscriminatorValueException;
use Vainyl\Doctrine\ORM\Exception\UnknownReferenceEntityException;
use Vainyl\Entity\EntityInterface;
use Vainyl\Entity\Hydrator\EntityHydratorInterface;

/**
 * Class DoctrineEntityHydrator
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityHydrator extends AbstractIdentifiable implements EntityHydratorInterface
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
                            break;
                        case ClassMetadataInfo::ONE_TO_MANY:
                        case ClassMetadataInfo::MANY_TO_MANY:
                            $collection = new ArrayCollection();
                            foreach ($value as $referenceData) {
                                $identifier = $referenceMetadata->identifier[0];
                                if (false === array_key_exists($identifier, $referenceData)) {
                                    throw new MissingIdentifierColumnException(
                                        $this,
                                        $referenceMetadata->identifier[0],
                                        $referenceData
                                    );
                                }
                                if (null === ($reference = $this->entityManager->find(
                                        $referenceEntity,
                                        $referenceData[$identifier]
                                    ))
                                ) {
                                    throw new UnknownReferenceEntityException(
                                        $this,
                                        $referenceEntity,
                                        $referenceData[$identifier]
                                    );
                                }
                                $collection->add($reference);
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
     * @param string            $entityName
     * @param array             $entityData
     * @param ClassMetadataInfo $classMetadata
     *
     * @return string
     */
    public function getChildEntityName(string $entityName, array $entityData, ClassMetadataInfo $classMetadata): string
    {
        if (ClassMetadataInfo::INHERITANCE_TYPE_NONE === $classMetadata->inheritanceType) {
            return $entityName;
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
    public function hydrate(string $entityName, array $externalData): EntityInterface
    {
        /**
         * @var ClassMetadataInfo $rootClassMetadata
         * @var ClassMetadataInfo $childClassMetadata
         * @var EntityInterface   $entity
         */
        $rootClassMetadata = $this->metadataFactory->getMetadataFor($entityName);
        $childEntityName = $this->getChildEntityName($entityName, $externalData, $rootClassMetadata);
        $childClassMetadata = $this->metadataFactory->getMetadataFor($childEntityName);
        $entity = $childClassMetadata->newInstance();

        return $this->populateEntity($externalData, $entity, $childClassMetadata);
    }
}