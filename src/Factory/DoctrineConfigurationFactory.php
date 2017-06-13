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

use Doctrine\Common\Cache\Cache as DoctrineCacheInterface;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Tools\Setup;
use Vainyl\Core\Application\EnvironmentInterface;
use Vainyl\Core\Extension\AbstractExtension;
use Vainyl\Core\Extension\ExtensionStorageInterface;

/**
 * Class DoctrineConfigurationFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineConfigurationFactory
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
     * @param string                 $doctrineProxy
     * @param string                 $globalFileName
     * @param string                 $extension
     *
     * @return Configuration
     */
    public function getConfiguration(
        DoctrineCacheInterface $doctrineCache,
        EnvironmentInterface $environment,
        string $doctrineProxy,
        string $globalFileName,
        string $extension
    ): Configuration {
        $paths = [];
        /**
         * @var AbstractExtension $extension
         */
        foreach ($this->extensionStorage->getIterator() as $extension) {
            $paths[$extension->getConfigDirectory($environment)] = $extension->getNamespace();
        }
        $paths[$environment->getConfigDirectory()] = '';

        $driver = new SimplifiedYamlDriver($paths, $extension);
        $driver->setGlobalBasename($globalFileName);

        $config = Setup::createConfiguration(
            $environment->isDebugEnabled(),
            null,
            $doctrineCache
        );
        $config->setProxyDir($environment->getCacheDirectory());
        $config->setProxyNamespace($doctrineProxy);
        $config->setMetadataDriverImpl($driver);

        return $config;
    }
}
