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
use Vainyl\Core\Storage\StorageInterface;
use Vainyl\Doctrine\ORM\Database\DoctrineDatabase;

/**
 * Class DoctrineDatabaseFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDatabaseFactory
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
     * @param string $name
     * @param string $connectionName
     * @param array  $configData
     *
     * @return DoctrineDatabase
     */
    public function createDatabase(string $name, string $connectionName, array $configData): DoctrineDatabase
    {
        return new DoctrineDatabase(
            $name,
            $configData,
            $this->configuration,
            $this->connectionStorage[$connectionName],
            $this->eventManager
        );
    }
}