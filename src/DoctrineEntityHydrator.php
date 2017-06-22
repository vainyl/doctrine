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

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Doctrine\ORM\Exception\MissingDiscriminatorColumnException;
use Vainyl\Doctrine\ORM\Exception\UnknownDiscriminatorValueException;
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

    /**
     * DoctrineEntityHydrator constructor.
     *
     * @param ClassMetadataFactory $metadataFactory
     * @param AbstractPlatform     $databasePlatform
     */
    public function __construct(ClassMetadataFactory $metadataFactory, AbstractPlatform $databasePlatform)
    {
        $this->metadataFactory = $metadataFactory;
        $this->databasePlatform = $databasePlatform;
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