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

namespace Vainyl\Doctrine\ORM\Exception;

use Vainyl\Entity\Exception\AbstractHydratorException;
use Vainyl\Entity\Hydrator\EntityHydratorInterface;

/**
 * Class UnknownReferenceEntityException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UnknownReferenceEntityException extends AbstractHydratorException
{
    private $entityName;

    private $referenceId;

    /**
     * UnknownReferenceEntityException constructor.
     *
     * @param EntityHydratorInterface $hydrator
     * @param string                  $entityName
     * @param mixed                   $referenceId
     */
    public function __construct(EntityHydratorInterface $hydrator, string $entityName, $referenceId)
    {
        $this->entityName = $entityName;
        $this->referenceId = $referenceId;
        parent::__construct(
            $hydrator,
            sprintf('Cannot find reference entity %s by id %s', $entityName, $referenceId)
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(
            ['entity' => $this->entityName, 'reference_id' => $this->referenceId],
            parent::toArray()
        );
    }
}