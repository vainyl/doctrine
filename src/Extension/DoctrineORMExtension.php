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

        if (false === $container->hasDefinition('doctrine.configuration.orm')) {
            throw new MissingRequiredServiceException($container, 'doctrine.configuration.orm');
        }

        $configuration = new DoctrineORMConfiguration();
        $ormConfig = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('doctrine.configuration.orm');
        $definition->replaceArgument(2, $ormConfig['orm']['config']);
        $definition->replaceArgument(3, $ormConfig['orm']['file']);
        $definition->replaceArgument(4, $ormConfig['orm']['extension']);
        $definition->replaceArgument(5, $ormConfig['orm']['tmp_dir']);
        $definition->replaceArgument(6, $ormConfig['orm']['proxy']);

        return $this;
    }
}
