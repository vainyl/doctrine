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

namespace Vainyl\Doctrine\ORM\Entity\Operation\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Doctrine\ORM\Entity\Operation\CreateDoctrineEntityOperation;
use Vainyl\Doctrine\ORM\Entity\Operation\DeleteDoctrineEntityOperation;
use Vainyl\Doctrine\ORM\Entity\Operation\UpdateDoctrineEntityOperation;
use Vainyl\Doctrine\ORM\Entity\Operation\UpsertDoctrineEntityOperation;
use Vainyl\Entity\EntityInterface;
use Vainyl\Entity\Operation\Factory\EntityOperationFactoryInterface;
use Vainyl\Operation\Collection\Factory\OperationFactoryInterface;
use Vainyl\Operation\OperationInterface;

/**
 * Class DoctrineEntityOperationFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityOperationFactory extends AbstractIdentifiable implements EntityOperationFactoryInterface
{
    private $operationFactory;

    private $entityManager;

    /**
     * DoctrineEntityOperationFactory constructor.
     *
     * @param OperationFactoryInterface $operationFactory
     * @param EntityManagerInterface    $entityManager
     */
    public function __construct(OperationFactoryInterface $operationFactory, EntityManagerInterface $entityManager)
    {
        $this->operationFactory = $operationFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function create(EntityInterface $entity): OperationInterface
    {
        return $this->operationFactory->decorate(new CreateDoctrineEntityOperation($this->entityManager, $entity));
    }

    /**
     * @inheritDoc
     */
    public function update(EntityInterface $newEntity, EntityInterface $oldEntity): OperationInterface
    {
        return $this->operationFactory->decorate(
            new UpdateDoctrineEntityOperation($this->entityManager, $newEntity, $oldEntity)
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(EntityInterface $entity): OperationInterface
    {
        return $this->operationFactory->decorate(new DeleteDoctrineEntityOperation($this->entityManager, $entity));
    }

    /**
     * @inheritDoc
     */
    public function upsert(EntityInterface $entity): OperationInterface
    {
        return $this->operationFactory->decorate(new UpsertDoctrineEntityOperation($this->entityManager, $entity));
    }
}