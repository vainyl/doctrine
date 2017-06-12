<?php
/**
 * Vain Framework
 *
 * PHP Version 7
 *
 * @package   vain-doctrine
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://github.com/allflame/vain-doctrine
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
     * @param \Exception|null        $previous
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        string $message,
        int $code = 500,
        \Exception $previous = null
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
