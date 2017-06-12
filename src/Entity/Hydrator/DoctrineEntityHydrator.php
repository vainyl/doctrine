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

namespace Vainyl\Doctrine\ORM\Entity\Hydrator;

use Vainyl\Entity\EntityInterface;
use Vainyl\Entity\Hydrator\EntityHydratorInterface;

/**
 * Class DoctrineEntityHydrator
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityHydrator implements EntityHydratorInterface
{
    /**
     * @inheritDoc
     */
    public function create(string $name, array $entityData): EntityInterface
    {
        trigger_error('Method create is not implemented', E_USER_ERROR);
    }
}