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

namespace Vainyl\Doctrine\ORM\Bootstrapper;

use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Application\ApplicationInterface;
use Doctrine\DBAL\PostgresTypes\InetType;
use Doctrine\DBAL\PostgresTypes\IntArrayType;
use Doctrine\DBAL\PostgresTypes\MacAddrType;
use Doctrine\DBAL\PostgresTypes\TsqueryType;
use Doctrine\DBAL\PostgresTypes\TsvectorType;
use Doctrine\DBAL\PostgresTypes\XmlType;
use Doctrine\DBAL\Types\Type;
use Vainyl\Core\Application\BootstrapperInterface;
use Vainyl\Doctrine\ORM\Database\DoctrineDatabase;
use Vainyl\Doctrine\ORM\Type\Int8Type;
use Vainyl\Doctrine\ORM\Type\TextArrayType;
use Vainyl\Doctrine\ORM\Type\TimeType;


/**
 * Class DoctrineTypeBootstrapper
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineTypeBootstrapper extends AbstractIdentifiable implements BootstrapperInterface
{

    private $database;

    /**
     * DoctrineTypeBootstrapper constructor.
     *
     * @param DoctrineDatabase $database
     */
    public function __construct(DoctrineDatabase $database)
    {
        $this->database = $database;
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
                     'vain_time'  => ['timestamp', TimeType::class],
                     'int8'       => ['bigint', Int8Type::class],
                 ] as $doctrineType => $typeData) {
            list ($dbType, $className) = $typeData;
            Type::addType($doctrineType, $className);
            $this->database->getDatabasePlatform()->registerDoctrineTypeMapping($dbType, $doctrineType);
            $this->database->getDatabasePlatform()->markDoctrineTypeCommented($doctrineType);
        }

        return $this;
    }

}