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

namespace Vainyl\Doctrine\ORM;

use Doctrine\Common\EventManager;
use Vainyl\Time\Factory\TimeFactoryInterface;

/**
 * Class DoctrineEventManager
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEventManager extends EventManager
{
    private $timeFactory;

    /**
     * DoctrineEventManager constructor.
     *
     * @param TimeFactoryInterface $timeFactory
     */
    public function __construct(TimeFactoryInterface $timeFactory)
    {
        $this->timeFactory = $timeFactory;
    }

    /**
     * @return TimeFactoryInterface
     */
    public function getTimeFactory(): TimeFactoryInterface
    {
        return $this->timeFactory;
    }
}
