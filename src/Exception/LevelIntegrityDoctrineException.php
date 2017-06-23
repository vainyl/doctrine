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

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class LevelIntegrityDoctrineException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class LevelIntegrityDoctrineException extends AbstractEntityManagerException
{
    private $level;

    /**
     * LevelIntegrityDoctrineException constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param int                    $level
     */
    public function __construct(EntityManagerInterface $entityManager, int $level)
    {
        $this->level = $level;
        parent::__construct($entityManager, sprintf('Level integrity check exception for level %d', $level));
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(['level' => $this->level], parent::toArray());
    }
}
