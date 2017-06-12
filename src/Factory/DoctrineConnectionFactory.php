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

namespace Vainyl\Doctrine\ORM\Factory;

use Vainyl\Connection\ConnectionInterface;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Doctrine\ORM\DoctrineMysqlConnection;
use Vainyl\Doctrine\ORM\DoctrinePostgresqlConnection;
use Vainyl\Doctrine\ORM\Exception\UnknownDoctrineDriverTypeException;

/**
 * Class DoctrineConnectionFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineConnectionFactory extends AbstractIdentifiable
{
    private $connection;

    /**
     * DoctrineConnectionFactory constructor.
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
    public function getName() : string
    {
        return 'doctrine';
    }

    /**
     * @inheritDoc
     */
    public function createConnection(string $connectionName) : ConnectionInterface
    {
        $type = 'pgsql';
        switch ($type) {
            case 'pgsql':
                return new DoctrinePostgresqlConnection($this->connection);
                break;
            case 'mysql':
                return new DoctrineMysqlConnection($this->connection);
                break;
            default:
                throw new UnknownDoctrineDriverTypeException($this, $type);
        }
    }
}
