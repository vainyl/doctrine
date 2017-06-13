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
 * Class DoctrineDatabaseCompilerPass
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineDatabaseCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('database.entity')) {
            throw new MissingRequiredServiceException($container, 'database.entity');
        }

        $definition = $container->getDefinition('database.entity');
        if ($definition->isSynthetic()) {
            $container->removeDefinition('database.entity');
            $container->setAlias('database.entity', new Alias('database.entity.doctrine'));
        }

        return $this;
    }
}