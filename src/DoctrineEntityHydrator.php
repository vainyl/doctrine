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
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Vainyl\Core\ArrayInterface;
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

    private $doctrineRegistry;

    /**
     * DoctrineEntityHydrator constructor.
     *
     * @param ClassMetadataFactory      $metadataFactory
     * @param AbstractPlatform          $databasePlatform
     * @param DoctrineRegistryInterface $doctrineRegistry
     */
    public function __construct(
        ClassMetadataFactory $metadataFactory,
        AbstractPlatform $databasePlatform,
        DoctrineRegistryInterface $doctrineRegistry
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->databasePlatform = $databasePlatform;
        $this->doctrineRegistry = $doctrineRegistry;
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
     * @return DoctrineRepositoryInterface
     */
    public function getRepository(string $className): DoctrineRepositoryInterface
    {
        return $this->doctrineRegistry->getRepository($className);
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
                                    $this->getRepository($referenceEntity)->find($value)
                                );

                            break;
                        case ClassMetadataInfo::ONE_TO_MANY:
                        case ClassMetadataInfo::MANY_TO_MANY:
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
            }
        }

        return $entity;
    }
}