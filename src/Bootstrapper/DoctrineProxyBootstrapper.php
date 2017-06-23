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

namespace Vainyl\Doctrine\ORM\Bootstrapper;

use Doctrine\Common\Proxy\Autoloader;
use Vainyl\Core\AbstractIdentifiable;
use Vainyl\Core\Application\ApplicationInterface;
use Vainyl\Core\Application\BootstrapperInterface;
use Vainyl\Core\Application\EnvironmentInterface;

/**
 * Class DoctrineProxyBootstrapper
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineProxyBootstrapper extends AbstractIdentifiable implements BootstrapperInterface
{
    private $environment;

    /**
     * DoctrineProxyBootstrapper constructor.
     *
     * @param EnvironmentInterface $environment
     */
    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'doctrine.orm_proxy';
    }

    /**
     * @inheritDoc
     */
    public function process(ApplicationInterface $application): BootstrapperInterface
    {
        Autoloader::register($this->environment->getCacheDirectory() . DIRECTORY_SEPARATOR . 'doctrine', 'Proxy');

        return $this;
    }
}