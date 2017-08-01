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
 * Interface EntityDriverExceptionInterface
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
interface EntityDriverExceptionInterface extends \Throwable
{
    /**
     * @return MappingDriver
     */
    public function getDriver(): MappingDriver;
}