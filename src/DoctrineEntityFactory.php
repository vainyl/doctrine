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

use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\ArrayInterface;
use Vainyl\Core\Hydrator\HydratorInterface;
use Vainyl\Doctrine\ORM\Exception\MissingDiscriminatorColumnException;
use Vainyl\Doctrine\ORM\Exception\UnknownDiscriminatorValueException;
use Vainyl\Entity\Factory\EntityFactoryInterface;

/**
 * Class DoctrineEntityFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityFactory extends AbstractIdentifiable implements EntityFactoryInterface
{
    private $hydratorStorage;

    private $metadataFactory;

    /**
     * DoctrineEntityFactory constructor.
     *
     * @param \ArrayAccess         $hydratorStorage
     * @param ClassMetadataFactory $metadataFactory
     */
    public function __construct(\ArrayAccess $hydratorStorage, ClassMetadataFactory $metadataFactory)
    {
        $this->hydratorStorage = $hydratorStorage;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $name): bool
    {
        return true;
    }

    /**
     * @param array             $entityData
     * @param ClassMetadataInfo $classMetadata
     *
     * @return string
     */
    public function getEntityName(array $entityData, ClassMetadataInfo $classMetadata): string
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
    public function create(string $name, array $entityData = []): ArrayInterface
    {
        $entityName = $this->getEntityName($entityData, $this->metadataFactory->getMetadataFor($name));
        $entity = $this->metadataFactory->getMetadataFor($entityName)->newInstance();

        /**
         * @var HydratorInterface $hydrator
         */
        foreach ($this->hydratorStorage as $hydrator) {
            if ($hydrator->supports($entity)) {
                return $hydrator->hydrate($entity, $entityData);
            }
        }

        return null;
    }
}