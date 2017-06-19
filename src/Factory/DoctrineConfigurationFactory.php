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
use Doctrine\Common\Cache\Cache as DoctrineCacheInterface;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Tools\Setup;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Application\EnvironmentInterface;
use Vainyl\Core\Extension\AbstractExtension;
use Vainyl\Core\Extension\ExtensionStorageInterface;
use Vainyl\Doctrine\ORM\Exception\UnknownDoctrineConfigTypeException;

/**
 * Class DoctrineConfigurationFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineConfigurationFactory extends AbstractIdentifiable
{
    private $extensionStorage;

    /**
     * DoctrineConfigurationFactory constructor.
     *
     * @param ExtensionStorageInterface $extensionStorage
     */
    public function __construct(ExtensionStorageInterface $extensionStorage)
    {
        $this->extensionStorage = $extensionStorage;
    }

    /**
     * @param DoctrineCacheInterface $doctrineCache
     * @param EnvironmentInterface   $environment
     * @param string                 $driverName
     * @param string                 $globalFileName
     * @param string                 $extension
     * @param string                 $proxyNamespace
     * @param string                 $tempDir
     *
     * @return Configuration
     *
     * @throws UnknownDoctrineConfigTypeException
     */
    public function getConfiguration(
        DoctrineCacheInterface $doctrineCache,
        EnvironmentInterface $environment,
        string $driverName,
        string $globalFileName,
        string $extension,
        string $proxyNamespace,
        string $tempDir
    ): Configuration {
        $paths = [];
        /**
         * @var AbstractExtension $extension
         */
        foreach ($this->extensionStorage->getIterator() as $extension) {
            $paths[$extension->getConfigDirectory()] = $extension->getNamespace();
        }

        switch ($driverName) {
            case 'yaml':
                $driver = new SimplifiedYamlDriver($paths, $extension);
                break;
            case 'xml':
                $driver = new XmlDriver($paths, $extension);
                break;
            case 'annotation':
                $driver = new AnnotationDriver(new AnnotationReader(), $paths);
                break;
            default:
                throw new UnknownDoctrineConfigTypeException($this, $driverName);
        }
        $driver->setGlobalBasename($globalFileName);
        $config = Setup::createConfiguration(
            $environment->isDebugEnabled(),
            $environment->getCacheDirectory() . DIRECTORY_SEPARATOR . $tempDir,
            $doctrineCache
        );
        $config->setProxyDir($environment->getCacheDirectory());
        $config->setProxyNamespace($proxyNamespace);
        $config->setMetadataDriverImpl($driver);

        return $config;
    }
}
