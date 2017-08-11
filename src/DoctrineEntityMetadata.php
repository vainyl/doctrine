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

namespace Vainyl\Doctrine\ORM;

use Doctrine\ORM\Mapping\ClassMetadata;
use Vainyl\Doctrine\Common\Metadata\DoctrineDomainMetadataInterface;
use Vainyl\Domain\Metadata\DomainMetadataInterface;

/**
 * Class DoctrineEntityMetadata
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntityMetadata extends ClassMetadata implements DoctrineDomainMetadataInterface
{
    public $domainMetadata;

    /**
     * DoctrineEntityMetadata constructor.
     *
     * @param string                  $entityName
     * @param DomainMetadataInterface $domainMetadata
     * @param null                    $namingStrategy
     */
    public function __construct($entityName, DomainMetadataInterface $domainMetadata, $namingStrategy = null)
    {
        $this->domainMetadata = $domainMetadata;
        parent::__construct($entityName, $namingStrategy);
    }

    /**
     * @inheritDoc
     */
    public function __sleep()
    {
        return array_merge(parent::__sleep(), ['domainMetadata']);
    }

    /**
     * @inheritDoc
     */
    public function getDomainMetadata(): DomainMetadataInterface
    {
        return $this->domainMetadata;
    }
}