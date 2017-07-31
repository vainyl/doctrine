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

use Doctrine\ORM\EntityManagerInterface;
use Vainyl\Doctrine\ORM\Operation\CreateDoctrineEntityOperation;
use Vainyl\Doctrine\ORM\Operation\DeleteDoctrineEntityOperation;
use Vainyl\Doctrine\ORM\Operation\UpdateDoctrineEntityOperation;
use Vainyl\Doctrine\ORM\Operation\UpsertDoctrineEntityOperation;
use Vainyl\Domain\DomainInterface;
use Vainyl\Domain\Operation\Decorator\AbstractDomainOperationFactoryDecorator;
use Vainyl\Domain\Operation\Factory\DomainOperationFactoryInterface;
use Vainyl\Entity\EntityInterface;
use Vainyl\Entity\Operation\Factory\EntityOperationFactoryInterface;
use Vainyl\Operation\Collection\Factory\CollectionFactoryInterface;
use Vainyl\Operation\OperationInterface;

/**
 * Class DoctrineEntityOperationFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityOperationFactory extends AbstractDomainOperationFactoryDecorator implements
    EntityOperationFactoryInterface
{
    private $collectionFactory;

    private $entityManager;

    /**
     * DoctrineEntityOperationFactory constructor.
     *
     * @param DomainOperationFactoryInterface $operationFactory
     * @param CollectionFactoryInterface      $collectionFactory
     * @param EntityManagerInterface          $entityManager
     */
    public function __construct(
        DomainOperationFactoryInterface $operationFactory,
        CollectionFactoryInterface $collectionFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
        parent::__construct($operationFactory);
    }

    /**
     * @param EntityInterface $domain
     *
     * @return OperationInterface
     */
    public function create(DomainInterface $domain): OperationInterface
    {
        return $this->collectionFactory
            ->create()
            ->add(parent::create($domain))
            ->add(new CreateDoctrineEntityOperation($this->entityManager, $domain));
    }

    /**
     * @param EntityInterface $domain
     *
     * @return OperationInterface
     */
    public function delete(DomainInterface $domain): OperationInterface
    {
        return $this->collectionFactory
            ->create()
            ->add(parent::delete($domain))
            ->add(new DeleteDoctrineEntityOperation($this->entityManager, $domain));
    }

    /**
     * @inheritDoc
     */
    public function supports(DomainInterface $domain): bool
    {
        return $this->entityManager->getMetadataFactory()->hasMetadataFor(get_class($domain));
    }

    /**
     * @param EntityInterface $newDomain
     * @param EntityInterface $oldDomain
     *
     * @return OperationInterface
     */
    public function update(DomainInterface $newDomain, DomainInterface $oldDomain): OperationInterface
    {
        return $this->collectionFactory
            ->create()
            ->add(parent::update($newDomain, $oldDomain))
            ->add(new UpdateDoctrineEntityOperation($this->entityManager, $newDomain, $oldDomain));
    }

    /**
     * @param EntityInterface $domain
     *
     * @return OperationInterface
     */
    public function upsert(DomainInterface $domain): OperationInterface
    {
        return $this->collectionFactory
            ->create()
            ->add(parent::upsert($domain))
            ->add(new UpsertDoctrineEntityOperation($this->entityManager, $domain));
    }
}