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

namespace Vainyl\Doctrine\ORM\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Vainyl\Core\Exception\MissingRequiredServiceException;
use Vainyl\Core\Extension\AbstractExtension;
use Vainyl\Core\Extension\AbstractFrameworkExtension;
use Vainyl\Doctrine\ORM\Factory\DoctrineORMSettings;

/**
 * Class DoctrineORMExtension
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineORMExtension extends AbstractFrameworkExtension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container): AbstractExtension
    {
        parent::load($configs, $container);

        if (false === $container->hasDefinition('doctrine.configuration.orm')) {
            throw new MissingRequiredServiceException($container, 'doctrine.configuration.orm');
        }

        $configuration = new DoctrineORMConfiguration();
        $ormConfig = $this->processConfiguration($configuration, $configs);

        $settingsDefinition = (new Definition())
            ->setClass(DoctrineORMSettings::class)
            ->setArguments(
                [
                    new Reference('doctrine.settings'),
                    $ormConfig['config'],
                    $ormConfig['file'],
                    $ormConfig['extension'],
                    $ormConfig['tmp_dir'],
                    $ormConfig['proxy']
                ]
            );
        $container->setDefinition('doctrine.settings.orm', $settingsDefinition);

        $configurationDefinition = $container->getDefinition('doctrine.configuration.orm');
        $configurationDefinition->replaceArgument(2, new Reference('doctrine.settings.orm'));

        return $this;
    }
}
