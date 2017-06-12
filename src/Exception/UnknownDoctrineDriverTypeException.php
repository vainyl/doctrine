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

use Vainyl\Core\Exception\AbstractCoreException;
use Vainyl\Doctrine\ORM\Connection\Factory\DoctrineConnectionFactory;

/**
 * Class UnknownDoctrineDriverTypeException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UnknownDoctrineDriverTypeException extends AbstractCoreException
{
    private $connectionFactory;

    private $driver;

    /**
     * UnknownDoctrineDriverTypeException constructor.
     *
     * @param DoctrineConnectionFactory $connectionFactory
     * @param string                     $driver
     */
    public function __construct(DoctrineConnectionFactory $connectionFactory, string $driver)
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
