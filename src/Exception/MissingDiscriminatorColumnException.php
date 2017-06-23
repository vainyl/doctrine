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

use Vainyl\Core\Exception\AbstractArrayFactoryException;
use Vainyl\Entity\Factory\EntityFactoryInterface;

/**
 * Class MissingDiscriminatorColumnException
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class MissingDiscriminatorColumnException extends AbstractArrayFactoryException
{
    private $column;

    private $externalData;

    /**
     * MissingDiscriminatorColumnException constructor.
     *
     * @param EntityFactoryInterface $factory
     * @param string                 $column
     * @param array                  $externalData
     */
    public function __construct(EntityFactoryInterface $factory, string $column, array $externalData)
    {
        $this->column = $column;
        $this->externalData = $externalData;
        parent::__construct(
            $factory,
            sprintf('Column %s not found in external data %s', $column, json_encode($externalData))
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_merge(['column' => $this->column, 'external_data' => $this->externalData], parent::toArray());
    }
}