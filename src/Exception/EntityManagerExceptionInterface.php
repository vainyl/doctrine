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

namespace Vainyl\Doctrine\ORM\Exception;

use Doctrine\ORM\EntityManagerInterface;
use Vainyl\Core\ArrayInterface;

/**
 * Interface EntityManagerExceptionInterface
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
interface EntityManagerExceptionInterface extends ArrayInterface, \Throwable
{
    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager() : EntityManagerInterface;
}