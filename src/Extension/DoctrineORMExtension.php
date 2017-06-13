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
use Vainyl\Core\Application\EnvironmentInterface;
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
        $container->addCompilerPass(new DoctrineDatabaseCompilerPass())
                  ->addCompilerPass(new DoctrineFactoryCompilerPass());

        return parent::load($configs, $container, $environment);
    }
}
