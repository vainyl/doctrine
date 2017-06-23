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
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Vainyl\Core\ArrayInterface;
use Vainyl\Core\ArrayX\Factory\ArrayFactoryInterface;
use Vainyl\Core\Hydrator\AbstractHydrator;
use Vainyl\Core\Hydrator\HydratorInterface;
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

    /**
     * DoctrineEntityHydrator constructor.
     *
     * @param ClassMetadataFactory $metadataFactory
     * @param AbstractPlatform     $databasePlatform
     */
    public function __construct(
        ClassMetadataFactory $metadataFactory,
        AbstractPlatform $databasePlatform

    ) {
        $this->metadataFactory = $metadataFactory;
        $this->databasePlatform = $databasePlatform;
    }

    /**
     * @inheritDoc
     */
    public function supports($object): bool
    {
        try {
            $this->metadataFactory->getMetadataFor(get_class($object));
        } catch (MappingException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $className
     *
     * @return ArrayFactoryInterface
     */
    public function findFactory(string $className): ArrayFactoryInterface
    {
        /**
         * @var ArrayFactoryInterface $factory
         */
        foreach ($this->getFactoryStorage() as $factory) {
            if ($factory->supports($className)) {
                return $factory;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function doHydrate($entity, array $externalData): ArrayInterface
    {
        /**
         * @var ClassMetadataInfo $classMetadata
         * @var EntityInterface   $entity
         */
        $classMetadata = $this->metadataFactory->getMetadataFor(get_class($entity));

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
                    switch ($associationMapping['type']) {
                        case ClassMetadataInfo::ONE_TO_ONE:
                        case ClassMetadataInfo::MANY_TO_ONE:
                            $classMetadata->reflFields[$associationMapping['fieldName']]
                                ->setValue(
                                    $entity,
                                    $this->findFactory($referenceEntity)->create($referenceEntity, $value)
                                );

                            break;
                        case ClassMetadataInfo::ONE_TO_MANY:
                        case ClassMetadataInfo::MANY_TO_MANY:
                            $collection = new ArrayCollection();
                            $factory = $this->findFactory($referenceEntity);
                            foreach ($value as $referenceData) {
                                $collection->add($factory->create($referenceEntity . $referenceData));
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
}