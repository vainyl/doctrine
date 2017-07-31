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

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Vainyl\Doctrine\ORM\Exception\NoMetadataAliasException;

/**
 * Class DoctrineEntityDriver
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityDriver extends SimplifiedYamlDriver
{
    /**
     * @param string                 $className
     * @param DoctrineEntityMetadata $metadata
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        parent::loadMetadataForClass($className, $metadata);

        $element = $this->getElement($className);
        if (!isset($element['alias'])) {
            throw new NoMetadataAliasException($this, $className);
        }

        $metadata->setAlias($element['alias']);
    }
}