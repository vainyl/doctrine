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

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Vainyl\Core\Exception\AbstractCoreException;

/**
 * Class AbstractEntityDriverException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
abstract class AbstractEntityDriverException extends AbstractCoreException implements EntityDriverExceptionInterface
{
    private $driver;

    /**
     * AbstractEntityDriverException constructor.
     *
     * @param MappingDriver   $driver
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct(MappingDriver $driver, string $message, int $code = 500, \Exception $previous = null)
    {
        $this->driver = $driver;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @inheritDoc
     */
    public function getDriver(): MappingDriver
    {
        return $this->driver;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(['driver' => spl_object_hash($this->driver)], parent::toArray());
    }
}