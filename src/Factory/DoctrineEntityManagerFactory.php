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

namespace Vainyl\Doctrine\ORM\Factory;

use Doctrine\Common\EventManager as DoctrineEventManager;
use Doctrine\DBAL\Driver\Connection as DBALDriverConnection;
use Doctrine\ORM\Configuration as DoctrineORMConfiguration;
use Vainyl\Doctrine\ORM\DoctrineEntityManager;
use Vainyl\Domain\Metadata\Factory\DomainMetadataFactoryInterface;
use Vainyl\Time\Factory\TimeFactoryInterface;

/**
 * Class DoctrineEntityManagerFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityManagerFactory
{
    /**
     * @param DBALDriverConnection           $connection
     * @param DoctrineORMConfiguration       $configuration
     * @param DoctrineEventManager           $eventManager
     * @param TimeFactoryInterface           $timeFactory
     * @param DomainMetadataFactoryInterface $domainMetadataFactory
     *
     * @return DoctrineEntityManager
     */
    public function create(
        DBALDriverConnection $connection,
        DoctrineORMConfiguration $configuration,
        DoctrineEventManager $eventManager,
        TimeFactoryInterface $timeFactory,
        DomainMetadataFactoryInterface $domainMetadataFactory
    ): DoctrineEntityManager {
        return DoctrineEntityManager::createExtended(
            $connection,
            $configuration,
            $eventManager,
            $timeFactory,
            $domainMetadataFactory
        );
    }
}
