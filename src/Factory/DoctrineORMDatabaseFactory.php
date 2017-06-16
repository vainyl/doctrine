<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine-common-bridge
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ORM\Factory;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Storage\StorageInterface;
use Vainyl\Database\DatabaseInterface;
use Vainyl\Database\Factory\DatabaseFactoryInterface;
use Vainyl\Doctrine\ORM\Database\DoctrineORMDatabase;

/**
 * Class DoctrineORMDatabaseFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineORMDatabaseFactory extends AbstractIdentifiable implements DatabaseFactoryInterface
{
    private $connectionStorage;

    private $configuration;

    private $eventManager;

    /**
     * PdoDatabaseFactory constructor.
     *
     * @param StorageInterface $connectionStorage
     */
    public function __construct(
        StorageInterface $connectionStorage,
        Configuration $configuration,
        EventManager $eventManager
    ) {
        $this->connectionStorage = $connectionStorage;
        $this->configuration = $configuration;
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritDoc
     */
    public function createDatabase(
        string $databaseName,
        string $connectionName,
        array $options = []
    ): DatabaseInterface {
        return new DoctrineORMDatabase(
            $databaseName,
            $options,
            $this->configuration,
            $this->connectionStorage[$connectionName],
            $this->eventManager
        );
    }
}