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
use Vainyl\Core\Extension\ExtensionInterface;
use Vainyl\Doctrine\ORM\Exception\UnknownDoctrineConfigTypeException;

/**
 * Class DoctrineORMConfigurationFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineORMConfigurationFactory extends AbstractIdentifiable
{
    private $bundleStorage;

    private $extraPaths = [];

    /**
     * DoctrineConfigurationFactory constructor.
     *
     * @param \Traversable $bundleStorage
     * @param array $extraPaths
     */
    public function __construct(\Traversable $bundleStorage, array $extraPaths = [])
    {
        $this->bundleStorage = $bundleStorage;
        $this->extraPaths = $extraPaths;
    }

    /**
     * @param DoctrineCacheInterface $doctrineCache
     * @param EnvironmentInterface   $environment
     * @param string                 $driverName
     * @param string                 $globalFileName
     * @param string                 $fileExtension
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
        string $fileExtension,
        string $proxyNamespace,
        string $tempDir
    ): Configuration {
        $paths = [];
        foreach ($this->extraPaths as $extraPath) {
            $paths[$extraPath['dir']] = $extraPath['prefix'];
        }
        /**
         * @var ExtensionInterface $bundle
         */
        foreach ($this->bundleStorage as $bundle) {
            $configDirectory = $bundle->getConfigDirectory();
            if (false === is_dir($configDirectory)) {
                continue;
            }
            $paths[$bundle->getConfigDirectory()] = $bundle->getNamespace();
        }

        switch ($driverName) {
            case 'yaml':
                $driver = new SimplifiedYamlDriver($paths, $fileExtension);
                break;
            case 'xml':
                $driver = new XmlDriver($paths, $fileExtension);
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
