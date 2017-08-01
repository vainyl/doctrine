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

namespace Vainyl\Doctrine\ORM\Bootstrapper;

use Doctrine\DBAL\PostgresTypes\InetType;
use Doctrine\DBAL\PostgresTypes\IntArrayType;
use Doctrine\DBAL\PostgresTypes\MacAddrType;
use Doctrine\DBAL\PostgresTypes\TsqueryType;
use Doctrine\DBAL\PostgresTypes\TsvectorType;
use Doctrine\DBAL\PostgresTypes\XmlType;
use Doctrine\DBAL\Types\Type;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Application\ApplicationInterface;
use Vainyl\Core\Application\BootstrapperInterface;
use Vainyl\Doctrine\ORM\Database\DoctrineORMDatabase;
use Vainyl\Doctrine\ORM\Type\Int8Type;
use Vainyl\Doctrine\ORM\Type\TextArrayType;
use Vainyl\Doctrine\ORM\Type\TimeType;
use Vainyl\Time\Factory\TimeFactoryInterface;

/**
 * Class DoctrineTypeBootstrapper
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineTypeBootstrapper extends AbstractIdentifiable implements BootstrapperInterface
{
    private $database;

    private $timeFactory;

    /**
     * DoctrineTypeBootstrapper constructor.
     *
     * @param DoctrineORMDatabase  $database
     * @param TimeFactoryInterface $timeFactory
     */
    public function __construct(DoctrineORMDatabase $database, TimeFactoryInterface $timeFactory)
    {
        $this->database = $database;
        $this->timeFactory = $timeFactory;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'doctrine.orm_type';
    }

    /**
     * @inheritDoc
     */
    public function process(ApplicationInterface $application): BootstrapperInterface
    {
        foreach ([
                     'text_array' => ['_text', TextArrayType::class],
                     'int_array'  => ['_int', IntArrayType::class],
                     'tsvector'   => ['tsvector', TsvectorType::class],
                     'tsquery'    => ['tsquery', TsqueryType::class],
                     'xml'        => ['xml', XmlType::class],
                     'inet'       => ['inet', InetType::class],
                     'macaddr'    => ['macaddr', MacAddrType::class],
                     'v_time'     => ['timestamp', TimeType::class],
                     'int8'       => ['bigint', Int8Type::class],
                 ] as $doctrineType => $typeData) {
            list ($dbType, $className) = $typeData;
            Type::addType($doctrineType, $className);
            $this->database->getDatabasePlatform()->registerDoctrineTypeMapping($dbType, $doctrineType);
            $this->database->getDatabasePlatform()->markDoctrineTypeCommented($doctrineType);
        }

        Type::getType('v_time')->setTimeFactory($this->timeFactory);

        return $this;
    }
}