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

namespace Vainyl\Doctrine\ORM\Database;

use Doctrine\DBAL\Driver\AbstractMySQLDriver;
use Vainyl\Connection\ConnectionInterface;

/**
 * Class DoctrineMysqlConnection
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineMysqlConnection extends AbstractMySQLDriver implements ConnectionInterface
{
    private $connection;

    /**
     * PostgresqlDoctrineDriver constructor.
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        return $this->establish();
    }

    /**
     * @param DoctrineMysqlConnection $obj
     *
     * @return bool
     */
    public function equals($obj): bool
    {
        return $this->getId() === $obj->getId();
    }

    /**
     * @inheritDoc
     */
    public function establish()
    {
        return $this->connection->establish();
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return spl_object_hash($this);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'pdo_mysql';
    }

    /**
     * @inheritDoc
     */
    public function hash()
    {
        return $this->getId();
    }
}
