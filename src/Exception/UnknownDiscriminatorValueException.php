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

use Vainyl\Entity\Exception\AbstractEntityFactoryException;
use Vainyl\Entity\Factory\EntityFactoryInterface;

/**
 * Class UnknownDiscriminatorValueException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UnknownDiscriminatorValueException extends AbstractEntityFactoryException
{
    private $value;

    private $discriminatorMap;

    /**
     * UnknownDiscriminatorValueException constructor.
     *
     * @param EntityFactoryInterface $factory
     * @param string                 $value
     * @param array                  $discriminatorMap
     */
    public function __construct(EntityFactoryInterface $factory, $value, array $discriminatorMap)
    {
        $this->value = $value;
        $this->discriminatorMap = $discriminatorMap;
        parent::__construct(
            $factory,
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