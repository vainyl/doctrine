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
use Vainyl\Connection\Factory\ConnectionFactoryInterface;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Storage\StorageInterface;
use Vainyl\Doctrine\ORM\Database\DoctrineMysqlConnection;
use Vainyl\Doctrine\ORM\Database\DoctrinePostgresqlConnection;
use Vainyl\Doctrine\ORM\Exception\UnknownDoctrineDriverTypeException;
use Vainyl\Pdo\PdoConnection;

/**
 * Class DoctrineConnectionFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineConnectionFactory extends AbstractIdentifiable implements ConnectionFactoryInterface
{
    private $connectionStorage;

    /**
     * DoctrineConnectionFactory constructor.
     *
     * @param StorageInterface $connectionStorage
     */
    public function __construct(StorageInterface $connectionStorage)
    {
        $this->connectionStorage = $connectionStorage;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'doctrine.orm';
    }

    /**
     * @inheritDoc
     */
    public function createConnection(
        string $name,
        string $engine,
        string $host,
        int $port,
        string $databaseName,
        string $userName,
        string $password,
        array $options
    ): ConnectionInterface {
        $connection = new PdoConnection($name, $host, $engine, $port, $databaseName, $userName, $password, $options);
        switch ($engine) {
            case 'pgsql':
                return new DoctrinePostgresqlConnection($connection);
                break;
            case 'mysql':
                return new DoctrineMysqlConnection($connection);
                break;
            default:
                throw new UnknownDoctrineDriverTypeException($this, $engine);
        }
    }
}
