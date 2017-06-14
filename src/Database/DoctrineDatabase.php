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
 * Class DoctrineDatabase
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDatabase extends Connection implements MvccDatabaseInterface
{
    /**
     * DoctrineDatabase constructor.
     *
     * @param array         $configData
     * @param Configuration $config
     * @param Driver        $driver
     * @param EventManager  $eventManager
     */
    public function __construct(
        array $configData,
        Configuration $config,
        Driver $driver,
        EventManager $eventManager
    ) {
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
        return 'doctrine';
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
    public function runQuery($query, array $bindParams, array $bindTypeParams = []): CursorInterface
    {
        return new DoctrineCursor($this->query($query, $bindParams));
    }
}
