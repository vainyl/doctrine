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
 * Class UpdateDoctrineEntityOperation
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UpdateDoctrineEntityOperation extends AbstractOperation
{
    private $entityManager;

    private $newEntity;

    private $oldEntity;

    /**
     * UpdateDoctrineEntityOperation constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EntityInterface        $newEntity
     * @param EntityInterface        $oldEntity
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntityInterface $newEntity,
        EntityInterface $oldEntity
    ) {
        $this->entityManager = $entityManager;
        $this->newEntity = $newEntity;
        $this->oldEntity = $oldEntity;
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        return new SuccessfulOperationResult($this);
    }
}