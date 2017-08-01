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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Tools\Setup;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Application\EnvironmentInterface;
use Vainyl\Core\Extension\ExtensionInterface;
use Vainyl\Doctrine\ORM\DoctrineEntitySettings;
use Vainyl\Doctrine\ORM\Exception\UnknownDoctrineConfigTypeException;

/**
 * Class DoctrineORMConfigurationFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineORMConfigurationFactory extends AbstractIdentifiable
{
    private $bundleStorage;

    /**
     * DoctrineConfigurationFactory constructor.
     *
     * @param \Traversable $bundleStorage
     */
    public function __construct(\Traversable $bundleStorage)
    {
        $this->bundleStorage = $bundleStorage;
    }

    /**
     * @param EnvironmentInterface   $environment
     * @param DoctrineEntitySettings $settings
     *
     * @return Configuration
     *
     * @throws UnknownDoctrineConfigTypeException
     */
    public function getConfiguration(EnvironmentInterface $environment, DoctrineEntitySettings $settings): Configuration
    {
        $paths = [];
        foreach ($settings->getExtraPaths() as $extraPath) {
            if (false === is_dir($extraPath['dir'])) {
                continue;
            }
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

        switch ($settings->getDriverName()) {
            case 'yaml':
                $driver = new SimplifiedYamlDriver($paths, $settings->getFileExtension());
                break;
            case 'xml':
                $driver = new XmlDriver($paths, $settings->getFileExtension());
                break;
            case 'annotation':
                $driver = new AnnotationDriver(new AnnotationReader(), $paths);
                break;
            default:
                throw new UnknownDoctrineConfigTypeException($this, $settings->getDriverName());
        }
        $driver->setGlobalBasename($settings->getGlobalFileName());
        $config = Setup::createConfiguration(
            $environment->isDebugEnabled(),
            $environment->getCacheDirectory() . DIRECTORY_SEPARATOR . $settings->getTempDir(),
            $settings->getCache()
        );
        $config->setProxyDir($environment->getCacheDirectory());
        $config->setProxyNamespace($settings->getCache());
        $config->setMetadataDriverImpl($driver);
        $config->setClassMetadataFactoryName(DoctrineEntityMetadataFactory::class);

        return $config;
    }
}
