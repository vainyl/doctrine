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

namespace Vainyl\Doctrine\ORM\Operation;

use Doctrine\ORM\EntityManagerInterface;
use Vainyl\Core\ResultInterface;
use Vainyl\Entity\EntityInterface;
use Vainyl\Operation\AbstractOperation;

/**
 * Class UpsertDoctrineEntityOperation
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UpsertDoctrineEntityOperation extends AbstractOperation
{
    private $entityManager;

    private $entity;

    /**
     * UpsertDoctrineEntityOperation constructor.
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
        trigger_error('Method execute is not implemented', E_USER_ERROR);
    }
}