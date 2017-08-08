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

namespace Vainyl\Doctrine\ORM\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Vainyl\Core\Exception\MissingRequiredServiceException;
use Vainyl\Core\Extension\AbstractExtension;
use Vainyl\Core\Extension\AbstractFrameworkExtension;

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

        if (false === $container->hasDefinition('doctrine.settings.entity')) {
            throw new MissingRequiredServiceException($container, 'doctrine.settings.entity');
        }

        $configuration = new DoctrineORMConfiguration();
        $ormConfig = $this->processConfiguration($configuration, $configs);

        $container
            ->findDefinition('doctrine.settings.entity')
            ->replaceArgument(1, $ormConfig['config'])
            ->replaceArgument(2, $ormConfig['file'])
            ->replaceArgument(3, $ormConfig['extension'])
            ->replaceArgument(4, $ormConfig['tmp_dir'])
            ->replaceArgument(5, $ormConfig['proxy']);

        foreach ($ormConfig['decorators'] as $decorator) {
            $decoratorId = 'doctrine.mapping.driver.' . $decorator;
            if (false === $container->hasDefinition($decoratorId)) {
                throw new MissingRequiredServiceException($container, $decoratorId);
            }
            $definition = (clone $container->getDefinition($decoratorId))
                ->setDecoratedService('doctrine.mapping.driver.entity')
                ->clearTag('driver.decorator')
                ->replaceArgument(0, new Reference($decoratorId . '.entity.inner'));
            $container->setDefinition($decoratorId . '.entity', $definition);
        }

        return $this;
    }
}
