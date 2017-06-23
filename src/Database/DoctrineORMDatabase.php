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

namespace Vainyl\Doctrine\ORM\Database;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Vainyl\Database\CursorInterface;
use Vainyl\Database\MvccDatabaseInterface;

/**
 * Class DoctrineORMDatabase
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineORMDatabase extends Connection implements MvccDatabaseInterface
{
    private $name;

    /**
     * DoctrineDatabase constructor.
     *
     * @param string        $name
     * @param array         $configData
     * @param Configuration $config
     * @param Driver        $driver
     * @param EventManager  $eventManager
     */
    public function __construct(
        string $name,
        array $configData,
        Configuration $config,
        Driver $driver,
        EventManager $eventManager
    ) {
        $this->name = $name;
        parent::__construct($configData, $driver, $config, $eventManager);
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
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function startTransaction(): bool
    {
        $this->beginTransaction();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function commitTransaction(): bool
    {
        $this->commit();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function rollbackTransaction(): bool
    {
        $this->rollBack();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function runQuery($query, array $bindParams = [], array $bindTypeParams = []): CursorInterface
    {
        return new DoctrineCursor($this->query($query, $bindParams));
    }
}
