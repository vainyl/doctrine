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

namespace Vainyl\Doctrine\ORM\Operation;

use Vainyl\Core\AbstractFailedResult;

/**
 * Class DoctrineFailedResult
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineFailedResult extends AbstractFailedResult
{
    private $exception;

    /**
     * DoctrineFailedResult constructor.
     *
     * @param \Throwable $exception
     */
    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(
            [
                'code'    => $this->exception->getCode(),
                'message' => $this->exception->getMessage(),
                'trace'   => $this->exception->getTrace(),
            ],
            parent::toArray()
        );
    }
}