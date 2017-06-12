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

use Doctrine\DBAL\Driver\PDOStatement as DoctrineDriverStatementInterface;
use Vainyl\Database\CursorInterface;

/**
 * Class DoctrineCursor
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineCursor implements CursorInterface
{
    private $doctrineStatement;

    private $mode;

    /**
     * DoctrineCursor constructor.
     *
     * @param DoctrineDriverStatementInterface $doctrineStatement
     * @param int                              $mode
     */
    public function __construct(DoctrineDriverStatementInterface $doctrineStatement, int $mode = \PDO::FETCH_ASSOC)
    {
        $this->doctrineStatement = $doctrineStatement;
        $this->mode = $mode;
    }

    /**
     * @inheritDoc
     */
    public function valid() : bool
    {
        return ($this->doctrineStatement->errorCode() === '00000');
    }

    /**
     * @inheritDoc
     */
    public function current() : array
    {
        return $this->doctrineStatement->fetch($this->mode);
    }

    /**
     * @inheritDoc
     */
    public function next() : bool
    {
        $this->doctrineStatement->nextRowset();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function close() : CursorInterface
    {
        $this->doctrineStatement->closeCursor();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function mode(int $mode) : CursorInterface
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSingle() : array
    {
        return $this->doctrineStatement->fetch($this->mode);
    }

    /**
     * @inheritDoc
     */
    public function getAll() : array
    {
        return $this->doctrineStatement->fetchAll($this->mode);
    }

    /**
     * @inheritDoc
     */
    public function count() : int
    {
        return $this->doctrineStatement->rowCount();
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->doctrineStatement->rowCount();
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        return $this;
    }
}
