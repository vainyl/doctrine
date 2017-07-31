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

namespace Vainyl\Doctrine\ORM\Operation\Factory;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManagerInterface;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Doctrine\ORM\Operation\CreateDoctrineEntityOperation;
use Vainyl\Doctrine\ORM\Operation\DeleteDoctrineEntityOperation;
use Vainyl\Doctrine\ORM\Operation\UpdateDoctrineEntityOperation;
use Vainyl\Doctrine\ORM\Operation\UpsertDoctrineEntityOperation;
use Vainyl\Domain\DomainInterface;
use Vainyl\Entity\EntityInterface;
use Vainyl\Entity\Operation\Factory\EntityOperationFactoryInterface;
use Vainyl\Operation\Collection\Factory\CollectionFactoryInterface;
use Vainyl\Operation\OperationInterface;

/**
 * Class DoctrineEntityOperationFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityOperationFactory extends AbstractIdentifiable implements
    EntityOperationFactoryInterface
{
    private $collectionFactory;

    private $entityManager;

    /**
     * DoctrineEntityOperationFactory constructor.
     *
     * @param CollectionFactoryInterface $collectionFactory
     * @param EntityManagerInterface     $entityManager
     */
    public function __construct(
        CollectionFactoryInterface $collectionFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @param EntityInterface $domain
     *
     * @return OperationInterface
     */
    public function create(DomainInterface $domain): OperationInterface
    {
        return new CreateDoctrineEntityOperation($this->entityManager, $domain);
    }

    /**
     * @param EntityInterface $domain
     *
     * @return OperationInterface
     */
    public function delete(DomainInterface $domain): OperationInterface
    {
        return new DeleteDoctrineEntityOperation($this->entityManager, $domain);
    }

    /**
     * @inheritDoc
     */
    public function supports(DomainInterface $domain): bool
    {
        try {
            $this->entityManager->getMetadataFactory()->getMetadataFor(get_class($domain));
        } catch (MappingException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param EntityInterface $newDomain
     * @param EntityInterface $oldDomain
     *
     * @return OperationInterface
     */
    public function update(DomainInterface $newDomain, DomainInterface $oldDomain): OperationInterface
    {
        return new UpdateDoctrineEntityOperation($this->entityManager, $newDomain, $oldDomain);
    }

    /**
     * @param EntityInterface $domain
     *
     * @return OperationInterface
     */
    public function upsert(DomainInterface $domain): OperationInterface
    {
        return new UpsertDoctrineEntityOperation($this->entityManager, $domain);
    }
}