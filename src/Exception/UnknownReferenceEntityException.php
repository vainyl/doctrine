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

namespace Vainyl\Doctrine\ORM\Exception;

use Vainyl\Core\Exception\AbstractHydratorException;
use Vainyl\Core\Hydrator\HydratorInterface;

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
     * @param HydratorInterface $hydrator
     * @param string            $entityName
     * @param array             $referenceId
     */
    public function __construct(HydratorInterface $hydrator, string $entityName, array $referenceId)
    {
        $this->entityName = $entityName;
        $this->referenceId = $referenceId;
        parent::__construct(
            $hydrator,
            sprintf('Cannot find reference entity %s by id %s', $entityName, json_encode($referenceId))
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