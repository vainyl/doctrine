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

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;

/**
 * Class NoMetadataAliasException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class NoMetadataAliasException extends AbstractEntityDriverException
{
    private $className;

    /**
     * NoMetadataAliasException constructor.
     *
     * @param MappingDriver $driver
     * @param string        $className
     */
    public function __construct(MappingDriver $driver, string $className)
    {
        parent::__construct($driver, sprintf('No alias set for %s', $className));
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(['className' => $this->className], parent::toArray());
    }
}