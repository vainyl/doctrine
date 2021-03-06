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
use Vainyl\Core\Exception\AbstractCoreException;

/**
 * Class AbstractEntityManagerException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class AbstractEntityManagerException extends AbstractCoreException
{
    private $entityManager;

    /**
     * AbstractEntityManagerException constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $message
     * @param int                    $code
     * @param \Throwable|null        $previous
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        string $message,
        int $code = 500,
        \Throwable $previous = null
    ) {
        $this->entityManager = $entityManager;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(['entity_manager' => spl_object_hash($this->entityManager)], parent::toArray());
    }
}
