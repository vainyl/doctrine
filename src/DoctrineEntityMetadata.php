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

namespace Vainyl\Doctrine\ORM;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Class DoctrineEntityMetadata
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityMetadata extends ClassMetadata
{
    public $alias;

    /**
     * @inheritDoc
     */
    public function __sleep()
    {
        return array_merge(parent::__sleep(), ['alias']);
    }

    /**
     * @param string $alias
     *
     * @return DoctrineEntityMetadata
     */
    public function setAlias(string $alias) : DoctrineEntityMetadata
    {
        $this->alias = $alias;

        return $this;
    }
}