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

namespace Vainyl\Doctrine\ORM\Operation;

use Doctrine\ORM\EntityManagerInterface;
use Vainyl\Core\ResultInterface;
use Vainyl\Entity\EntityInterface;
use Vainyl\Operation\AbstractOperation;
use Vainyl\Operation\SuccessfulOperationResult;

/**
 * Class DeleteDoctrineEntityOperation
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DeleteDoctrineEntityOperation extends AbstractOperation
{
    private $entityManager;

    private $entity;

    /**
     * DeleteDoctrineEntityOperation constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EntityInterface        $entity
     */
    public function __construct(EntityManagerInterface $entityManager, EntityInterface $entity)
    {
        $this->entityManager = $entityManager;
        $this->entity = $entity;
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        $this->entityManager->remove($this->entity);

        return new SuccessfulOperationResult($this);
    }
}