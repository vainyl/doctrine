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

use Doctrine\DBAL\Types\Type;
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
     * @param string        $column
     * @param               $value
     * @param ClassMetadata $classMetadata
     *
     * @return array
     */
    protected function populateColumn(string $column, $value, ClassMetadata $classMetadata)
    {
        if (array_key_exists($column, $classMetadata->fieldNames)) {
            $fieldMapping = $classMetadata->fieldMappings[$classMetadata->fieldNames[$column]];
            return [
                'name' => $fieldMapping['fieldName'],
                'value' => Type::getType($fieldMapping['type'])->convertToPHPValue(
                    $value,
                    $this->entityManager->getConnection()->getDatabasePlatform()
                )
            ];
        }

        return ['name' => '', 'value' => ''];
//
//        switch (true) {
//            // NOTE: Most of the times it's a field mapping, so keep it first!!!
//            case (isset($this->_rsm->fieldMappings[$key])):
//                $classMetadata = $this->getClassMetadata($this->_rsm->declaringClasses[$key]);
//                $fieldName = $this->_rsm->fieldMappings[$key];
//                $fieldMapping = $classMetadata->fieldMappings[$fieldName];
//
//                return $this->cache[$key] = [
//                    'isIdentifier' => in_array($fieldName, $classMetadata->identifier),
//                    'fieldName'    => $fieldName,
//                    'type'         => Type::getType($fieldMapping['type']),
//                    'dqlAlias'     => $this->_rsm->columnOwnerMap[$key],
//                ];
//
//            case (isset($this->_rsm->newObjectMappings[$key])):
//                // WARNING: A NEW object is also a scalar, so it must be declared before!
//                $mapping = $this->_rsm->newObjectMappings[$key];
//
//                return $this->cache[$key] = [
//                    'isScalar'             => true,
//                    'isNewObjectParameter' => true,
//                    'fieldName'            => $this->_rsm->scalarMappings[$key],
//                    'type'                 => Type::getType($this->_rsm->typeMappings[$key]),
//                    'argIndex'             => $mapping['argIndex'],
//                    'objIndex'             => $mapping['objIndex'],
//                    'class'                => new \ReflectionClass($mapping['className']),
//                ];
//
//            case (isset($this->_rsm->scalarMappings[$key])):
//                return $this->cache[$key] = [
//                    'isScalar'  => true,
//                    'fieldName' => $this->_rsm->scalarMappings[$key],
//                    'type'      => Type::getType($this->_rsm->typeMappings[$key]),
//                ];
//
//            case (isset($this->_rsm->metaMappings[$key])):
//                // Meta column (has meaning in relational schema only, i.e. foreign keys or discriminator columns).
//                $fieldName = $this->_rsm->metaMappings[$key];
//                $dqlAlias = $this->_rsm->columnOwnerMap[$key];
//                $classMetadata = $this->getClassMetadata($this->_rsm->aliasMap[$dqlAlias]);
//                $type = isset($this->_rsm->typeMappings[$key])
//                    ? Type::getType($this->_rsm->typeMappings[$key])
//                    : null;
//
//                return $this->cache[$key] = [
//                    'isIdentifier' => isset($this->_rsm->isIdentifierColumn[$dqlAlias][$key]),
//                    'isMetaColumn' => true,
//                    'fieldName'    => $fieldName,
//                    'type'         => $type,
//                    'dqlAlias'     => $dqlAlias,
//                ];
//        }
//
//        // this column is a left over, maybe from a LIMIT query hack for example in Oracle or DB2
//        // maybe from an additional column that has not been defined in a NativeQuery ResultSetMapping.
//        return null;
    }

    /**
     * @param string        $entityName
     * @param array         $entityData
     * @param ClassMetadata $classMetadata
     *
     * @return string
     */
    public function getEntityName(string $entityName, array $entityData, ClassMetadata $classMetadata) : string
    {
        return $entityName;
//
//                // We need to find the correct entity class name if we have inheritance in resultset
//        if ($classMetadata->inheritanceType !== ClassMetadata::INHERITANCE_TYPE_NONE) {
//            $discrColumnName = $this->_platform->getSQLResultCasing($classMetadata->discriminatorColumn['name']);
//
//            // Find mapped discriminator column from the result set.
//            if ($metaMappingDiscrColumnName = array_search($discrColumnName, $this->_rsm->metaMappings)) {
//                $discrColumnName = $metaMappingDiscrColumnName;
//            }
//
//            if (!isset($sqlResult[$discrColumnName])) {
//                throw HydrationException::missingDiscriminatorColumn(
//                    $entityName,
//                    $discrColumnName,
//                    key($this->_rsm->aliasMap)
//                );
//            }
//
//            if ($sqlResult[$discrColumnName] === '') {
//                throw HydrationException::emptyDiscriminatorValue(key($this->_rsm->aliasMap));
//            }
//
//            $discrMap = $classMetadata->discriminatorMap;
//
//            if (!isset($discrMap[$sqlResult[$discrColumnName]])) {
//                throw HydrationException::invalidDiscriminatorValue(
//                    $sqlResult[$discrColumnName],
//                    array_keys($discrMap)
//                );
//            }
//
//            $entityName = $discrMap[$sqlResult[$discrColumnName]];
//
//            unset($sqlResult[$discrColumnName]);
//        }
    }

    /**
     * @inheritDoc
     */
    public function hydrate(string $entityName, array $externalData): EntityInterface
    {
        $classMetadata = $this->getClassMetadata($entityName);
        $entityName = $this->getEntityName($entityName, $externalData, $classMetadata);

        $entityData = [];
        foreach ($externalData as $column => $value) {
            $parsedField = $this->populateColumn($column, $value, $classMetadata);
            $entityData[$parsedField['name']] = $parsedField['value'];
        }

        return $this->entityManager->getUnitOfWork()->createEntity($entityName, $entityData);
    }
}