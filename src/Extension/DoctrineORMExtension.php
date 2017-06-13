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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vainyl\Core\Application\EnvironmentInterface;
use Vainyl\Core\Exception\MissingRequiredServiceException;
use Vainyl\Core\Extension\AbstractExtension;

/**
 * Class DoctrineORMExtension
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineORMExtension extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function load(
        array $configs,
        ContainerBuilder $container,
        EnvironmentInterface $environment = null
    ): AbstractExtension {
        if (false === $container->hasDefinition('database.entity')) {
            throw new MissingRequiredServiceException($container, 'database.entity');
        }

        $definition = $container->getDefinition('database.entity');
        if ($definition->isSynthetic()) {
            $container->set('database.document', new Alias('database.document.doctrine'));
        }

        if (false === $container->hasDefinition('entity.operation.factory')) {
            throw new MissingRequiredServiceException($container, 'entity.operation.factory');
        }

        $definition = $container->getDefinition('entity.operation.factory.doctrine');
        if ($definition->isSynthetic()) {
            $container->set('entity.operation.factory', new Alias('entity.operation.factory.doctrine'));
        }

        return parent::load($configs, $container, $environment);
    }
}
