<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine-ORM-Bridge
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ORM\Operation\Decorator;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Vainyl\Core\ResultInterface;
use Vainyl\Doctrine\ORM\DoctrineEntityManager;
use Vainyl\Doctrine\ORM\Operation\DoctrineFailedResult;
use Vainyl\Operation\Collection\CollectionInterface;
use Vainyl\Operation\Collection\Decorator\AbstractCollectionDecorator;

/**
 * Class DoctrineCollectionDecorator
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineCollectionDecorator extends AbstractCollectionDecorator
{
    private $entityManager;

    /**
     * DoctrineCollectionDecorator constructor.
     *
     * @param CollectionInterface   $collection
     * @param DoctrineEntityManager $entityManager
     */
    public function __construct(CollectionInterface $collection, DoctrineEntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($collection);
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        try {
            $this->entityManager->init();

            $result = parent::execute();
            if (false === $result->isSuccessful()) {
                return $result;
            }

            $this->entityManager->flush();
        } catch (DBALException $exception) {
            return new DoctrineFailedResult($exception);
        } catch (ORMException $exception) {
            return new DoctrineFailedResult($exception);
        }

        return $result;
    }
}
