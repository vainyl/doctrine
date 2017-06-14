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
    private $config;

    private $connectionStorage;

    /**
     * PdoDatabaseFactory constructor.
     *
     * @param StorageInterface $connectionStorage
     */
    public function __construct(
        StorageInterface $config,
        StorageInterface $connectionStorage
    ) {
        $this->config = $config;
        $this->connectionStorage = $connectionStorage;
    }

    /**
     * @param string        $name
     * @param Configuration $configuration
     * @param EventManager  $eventManager
     *
     * @return DoctrineDatabase
     */
    public function createDatabase(
        string $name,
        Configuration $configuration,
        EventManager $eventManager
    ): DoctrineDatabase {
        $config = $this->config[$name];

        return new DoctrineDatabase(
            $config,
            $configuration,
            $this->connectionStorage[$config['connection']],
            $eventManager
        );
    }
}