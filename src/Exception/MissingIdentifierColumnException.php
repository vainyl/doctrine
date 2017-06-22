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
 * Class MissingIdentifierColumnException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class MissingIdentifierColumnException extends AbstractHydratorException
{
    private $identifier;

    private $externalData;

    /**
     * MissingIdentifierColumnException constructor.
     *
     * @param EntityHydratorInterface $hydrator
     * @param string                  $identifier
     * @param array                   $externalData
     */
    public function __construct(EntityHydratorInterface $hydrator, string $identifier, array $externalData)
    {
        $this->identifier = $identifier;
        $this->externalData = $externalData;
        parent::__construct(
            $hydrator,
            sprintf('Identifier %s not found in external data %s', $identifier, json_encode($externalData))
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(['identifier' => $this->identifier, 'external_data' => $this->externalData], parent::toArray());
    }
}