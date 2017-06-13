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

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vainyl\Core\Exception\MissingRequiredServiceException;

/**
 * Class DoctrineFactoryCompilerPass
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineFactoryCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('entity.operation.factory')) {
            throw new MissingRequiredServiceException($container, 'entity.operation.factory');
        }

        $definition = $container->getDefinition('entity.operation.factory');
        if ($definition->isSynthetic()) {
            $container->removeDefinition('entity.operation.factory');
            $container->setAlias('entity.operation.factory', new Alias('entity.operation.factory.doctrine'));
        }

        return $this;
    }
}