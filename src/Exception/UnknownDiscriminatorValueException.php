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
 * Class UnknownDiscriminatorValueException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UnknownDiscriminatorValueException extends AbstractHydratorException
{
    private $value;

    private $discriminatorMap;

    /**
     * UnknownDiscriminatorValueException constructor.
     *
     * @param EntityHydratorInterface $hydrator
     * @param string                  $value
     * @param array                   $discriminatorMap
     */
    public function __construct(EntityHydratorInterface $hydrator, $value, array $discriminatorMap)
    {
        $this->value = $value;
        $this->discriminatorMap = $discriminatorMap;
        parent::__construct(
            $hydrator,
            sprintf(
                'Value %s is not found in discriminator map %s',
                $value,
                json_encode($discriminatorMap)
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(
            ['value' => $this->value, 'discriminator_map' => $this->discriminatorMap],
            parent::toArray()
        );
    }
}