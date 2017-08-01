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

use Vainyl\Core\Exception\AbstractCoreException;
use Vainyl\Doctrine\ORM\Factory\DoctrineORMConnectionFactory;

/**
 * Class UnknownDoctrineDriverTypeException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UnknownDoctrineConnectionTypeException extends AbstractCoreException
{
    private $connectionFactory;

    private $driver;

    /**
     * UnknownDoctrineDriverTypeException constructor.
     *
     * @param DoctrineORMConnectionFactory $connectionFactory
     * @param string                       $driver
     */
    public function __construct(DoctrineORMConnectionFactory $connectionFactory, string $driver)
    {
        $this->connectionFactory = $connectionFactory;
        $this->driver = $driver;
        parent::__construct(sprintf('Cannot create doctrine connection of unknown type %s', $driver));
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(
            ['connection_factory' => $this->connectionFactory->getId(), 'driver' => $this->driver],
            parent::toArray()
        );
    }
}
