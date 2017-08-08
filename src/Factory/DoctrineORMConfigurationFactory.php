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

use Doctrine\Common\Persistence\Mapping\Driver\FileDriver;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\Setup;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Application\EnvironmentInterface;
use Vainyl\Doctrine\ORM\DoctrineEntitySettings;

/**
 * Class DoctrineORMConfigurationFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineORMConfigurationFactory extends AbstractIdentifiable
{
    /**
     * @param EnvironmentInterface   $environment
     * @param DoctrineEntitySettings $settings
     * @param FileDriver             $mappingDriver
     *
     * @return Configuration
     */
    public function getConfiguration(
        EnvironmentInterface $environment,
        DoctrineEntitySettings $settings,
        FileDriver $mappingDriver
    ): Configuration {

        $mappingDriver->setGlobalBasename($settings->getGlobalFileName());
        $config = Setup::createConfiguration(
            $environment->isDebugEnabled(),
            $environment->getCacheDirectory() . DIRECTORY_SEPARATOR . $settings->getTempDir(),
            $settings->getCache()
        );
        $config->setProxyDir($environment->getCacheDirectory());
        $config->setProxyNamespace($settings->getCache());
        $config->setMetadataDriverImpl($mappingDriver);
        $config->setClassMetadataFactoryName(DoctrineEntityMetadataFactory::class);

        return $config;
    }
}
