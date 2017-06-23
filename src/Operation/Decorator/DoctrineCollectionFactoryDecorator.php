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

namespace Vainyl\Doctrine\ORM\Operation\Decorator;

use Vainyl\Doctrine\ORM\DoctrineEntityManager;
use Vainyl\Operation\Collection\CollectionInterface;
use Vainyl\Operation\Collection\Decorator\AbstractCollectionFactoryDecorator;
use Vainyl\Operation\Collection\Factory\CollectionFactoryInterface;

/**
 * Class DoctrineCollectionFactoryDecorator
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineCollectionFactoryDecorator extends AbstractCollectionFactoryDecorator
{
    private $entityManager;

    /**
     * DoctrineCollectionFactoryDecorator constructor.
     *
     * @param CollectionFactoryInterface $collectionFactory
     * @param DoctrineEntityManager      $entityManager
     */
    public function __construct(CollectionFactoryInterface $collectionFactory, DoctrineEntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($collectionFactory);
    }

    /**
     * @inheritDoc
     */
    public function create(array $operations = []): CollectionInterface
    {
        return new DoctrineCollectionDecorator(parent::create($operations), $this->entityManager);
    }
}
