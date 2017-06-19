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

namespace Vainyl\Doctrine\ORM\Exception;

use Vainyl\Core\Exception\AbstractCoreException;
use Vainyl\Doctrine\ORM\Factory\DoctrineORMConfigurationFactory;

/**
 * Class UnknownDoctrineConfigTypeException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class UnknownDoctrineConfigTypeException extends AbstractCoreException
{
    private $configurationFactory;

    private $driver;

    /**
     * UnknownDoctrineDriverTypeException constructor.
     *
     * @param DoctrineORMConfigurationFactory $configurationFactory
     * @param string                       $driver
     */
    public function __construct(DoctrineORMConfigurationFactory $configurationFactory, string $driver)
    {
        $this->configurationFactory = $configurationFactory;
        $this->driver = $driver;
        parent::__construct(sprintf('Cannot create doctrine config reader of unknown type %s', $driver));
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(
            ['configuration_factory' => $this->configurationFactory->getId(), 'driver' => $this->driver],
            parent::toArray()
        );
    }
}