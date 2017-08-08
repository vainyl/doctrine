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

namespace Vainyl\Doctrine\ORM\Factory;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Doctrine\Common\Driver\Provider\DoctrineMappingPathProvider;
use Vainyl\Doctrine\ORM\DoctrineEntitySettings;
use Vainyl\Doctrine\ORM\Exception\UnknownDoctrineConfigTypeException;

/**
 * Class DoctrineEntityMappingDriverFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityMappingDriverFactory extends AbstractIdentifiable
{
    private $pathProvider;

    /**
     * DoctrineEntityMappingDriverFactory constructor.
     *
     * @param DoctrineMappingPathProvider $pathProvider
     */
    public function __construct(DoctrineMappingPathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;
    }

    /**
     * @param DoctrineEntitySettings $settings
     *
     * @return MappingDriver
     */
    public function create(DoctrineEntitySettings $settings): MappingDriver
    {
        $paths = $this->pathProvider->getPaths($settings);

        switch ($settings->getDriverName()) {
            case 'yaml':
                return new SimplifiedYamlDriver($paths, $settings->getFileExtension());
                break;
            case 'xml':
                return new XmlDriver($paths, $settings->getFileExtension());
                break;
            case 'annotation':
                return new AnnotationDriver(new AnnotationReader(), $paths);
                break;
            default:
                throw new UnknownDoctrineConfigTypeException($this, $settings->getDriverName());
        }
    }
}