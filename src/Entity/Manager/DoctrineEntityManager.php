<?php
/**
 * Vain Framework
 *
 * PHP Version 7
 *
 * @package   vain-doctrine
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://github.com/allflame/vain-doctrine
 */
declare(strict_types = 1);

namespace Vainyl\Doctrine\ORM\Entity\Manager;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Vainyl\Doctrine\ORM\Exception\LevelIntegrityDoctrineException;
use Vainyl\Time\Factory\TimeFactoryInterface;

/**
 * Class DoctrineEntityManager
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityManager extends EntityManager
{
    /**
     * @var TimeFactoryInterface
     */
    private $timeFactory;

    private $flushLevel = 0;

    /**
     * DoctrineEntityManager constructor.
     *
     * @param Connection           $conn
     * @param Configuration        $config
     * @param EventManager         $eventManager
     * @param TimeFactoryInterface $timeFactory
     */
    protected function __construct(
        Connection $conn,
        Configuration $config,
        EventManager $eventManager,
        TimeFactoryInterface $timeFactory
    ) {
        $this->timeFactory = $timeFactory;
        parent::__construct($conn, $config, $eventManager);
    }

    /**
     * @param                      $conn
     * @param Configuration        $config
     * @param EventManager         $eventManager
     * @param TimeFactoryInterface $timeFactory
     *
     * @return DoctrineEntityManager
     * @throws ORMException
     */
    public static function createWithTimeFactory(
        $conn,
        Configuration $config,
        EventManager $eventManager,
        TimeFactoryInterface $timeFactory
    ) {
        if (!$config->getMetadataDriverImpl()) {
            throw ORMException::missingMappingDriverImpl();
        }

        switch (true) {
            case (is_array($conn)):
                $conn = DriverManager::getConnection(
                    $conn,
                    $config,
                    ($eventManager ?: new EventManager())
                );
                break;

            case ($conn instanceof Connection):
                if ($eventManager !== null && $conn->getEventManager() !== $eventManager) {
                    throw ORMException::mismatchedEventManager();
                }
                break;

            default:
                throw new \InvalidArgumentException("Invalid argument: " . $conn);
        }

        return new DoctrineEntityManager($conn, $config, $conn->getEventManager(), $timeFactory);
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        if (0 <= $this->flushLevel) {
            $this->flushLevel++;
            return $this;
        }

        throw new LevelIntegrityDoctrineException($this, $this->flushLevel);
    }

    /**
     * @inheritDoc
     */
    public function flush($entity = null)
    {
        $this->flushLevel--;

        if (0 < $this->flushLevel) {
            return $this;
        }

        if (0 > $this->flushLevel) {
            throw new LevelIntegrityDoctrineException($this, $this->flushLevel);
        }

        parent::flush($entity);

        return $this;
    }

    /**
     * @return TimeFactoryInterface
     */
    public function getTimeFactory()
    {
        return $this->timeFactory;
    }
}
