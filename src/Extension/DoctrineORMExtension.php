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
    public function getCompilerPasses(): array
    {
        return [[new DoctrineEntityMappingDriverPass()]];
    }

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
            ->replaceArgument(1, $ormConfig['file'])
            ->replaceArgument(2, $ormConfig['extension'])
            ->replaceArgument(3, $ormConfig['tmp_dir'])
            ->replaceArgument(4, $ormConfig['proxy']);

        $container->setParameter('doctrine.decorators.entity', $ormConfig['decorators']);

        return $this;
    }
}
