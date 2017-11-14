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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Vainyl\Core\Exception\MissingRequiredServiceException;

/**
 * Class DoctrineEntityMappingDriverPass
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityMappingDriverPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getParameter('doctrine.decorators.entity') as $decorator) {
            $decoratorId = 'doctrine.mapping.driver.' . $decorator;
            if (false === $container->hasDefinition($decoratorId)) {
                throw new MissingRequiredServiceException($container, $decoratorId);
            }
            $definition = (clone $container->getDefinition($decoratorId))
                ->setDecoratedService('doctrine.mapping.driver.entity')
                ->clearTag('mapping.driver.decorator')
                ->replaceArgument(0, new Reference($decoratorId . '.entity.inner'));
            $container->setDefinition($decoratorId . '.entity', $definition);
        }
    }
}
