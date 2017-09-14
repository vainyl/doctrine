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

namespace Vainyl\Doctrine\ORM;

use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Vainyl\Doctrine\ORM\Exception\LevelIntegrityDoctrineException;
use Vainyl\Doctrine\ORM\Factory\DoctrineEntityMetadataFactory;
use Vainyl\Domain\DomainInterface;
use Vainyl\Domain\Metadata\Factory\DomainMetadataFactoryInterface;
use Vainyl\Domain\Scenario\Storage\DomainScenarioStorageInterface;
use Vainyl\Domain\Storage\DomainStorageInterface;
use Vainyl\Time\Factory\TimeFactoryInterface;

/**
 * Class DoctrineEntityManager
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 *
 * @method DoctrineEntityMetadataFactory getMetadataFactory
 */
class DoctrineEntityManager extends EntityManager implements DomainStorageInterface, DomainScenarioStorageInterface
{
    private $timeFactory;

    private $domainMetadataFactory;

    private $flushLevel = 0;

    /**
     * DoctrineEntityManager constructor.
     *
     * @param Connection                     $conn
     * @param Configuration                  $config
     * @param EventManager                   $eventManager
     * @param TimeFactoryInterface           $timeFactory
     * @param DomainMetadataFactoryInterface $domainMetadataFactory
     */
    protected function __construct(
        Connection $conn,
        Configuration $config,
        EventManager $eventManager,
        TimeFactoryInterface $timeFactory,
        DomainMetadataFactoryInterface $domainMetadataFactory
    ) {
        $this->timeFactory = $timeFactory;
        $this->domainMetadataFactory = $domainMetadataFactory;
        parent::__construct($conn, $config, $eventManager);
    }

    /**
     * @param mixed                          $conn
     * @param Configuration                  $config
     * @param EventManager                   $eventManager
     * @param TimeFactoryInterface           $timeFactory
     * @param DomainMetadataFactoryInterface $metadataFactory
     *
     * @return DoctrineEntityManager
     * @throws ORMException
     */
    public static function createExtended(
        $conn,
        Configuration $config,
        EventManager $eventManager,
        TimeFactoryInterface $timeFactory,
        DomainMetadataFactoryInterface $metadataFactory
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

        return new DoctrineEntityManager($conn, $config, $conn->getEventManager(), $timeFactory, $metadataFactory);
    }

    /**
     * @param DoctrineEntityManager $obj
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
    public function findById(string $name, $id): ?DomainInterface
    {
        return $this->getRepository($name)->find($id);
    }

    /**
     * @inheritDoc
     */
    public function findMany(
        string $name,
        array $criteria = [],
        array $orderBy = [],
        int $limit = 0,
        int $offset = 0
    ): array {
        return $this->getRepository($name)->findBy($criteria, $orderBy, $limit
            ? $limit
            : null, $offset
                                                       ? $offset
                                                       : null);
    }

    /**
     * @inheritDoc
     */
    public function findOne(string $name, array $criteria = [], array $orderBy = []): ?DomainInterface
    {
        return $this->getRepository($name)->findOneBy($criteria, $orderBy);
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
     * @return DomainMetadataFactoryInterface
     */
    public function getDomainMetadataFactory(): DomainMetadataFactoryInterface
    {
        return $this->domainMetadataFactory;
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?string
    {
        return spl_object_hash($this);
    }

    /**
     * @inheritDoc
     */
    public function getScenarios(string $name): array
    {
        return $this->getMetadataFactory()->getMetadataFor($name)->getDomainMetadata()->getScenarios();
    }

    /**
     * @return TimeFactoryInterface
     */
    public function getTimeFactory()
    {
        return $this->timeFactory;
    }

    /**
     * @inheritDoc
     */
    public function hash()
    {
        return $this->getId();
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
    public function supports(string $name): bool
    {
        try {
            return $this->getMetadataFactory()->getMetadataFor($name)->getDomainMetadata()->isPrimary();
        } catch (MappingException $e) {
            return false;
        }
    }
}
