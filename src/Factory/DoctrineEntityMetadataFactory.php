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

namespace Vainyl\Doctrine\ORM\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Vainyl\Doctrine\ORM\DoctrineEntityManager;
use Vainyl\Doctrine\ORM\DoctrineEntityMetadata;

/**
 * Class DoctrineEntityMetadataFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 *
 * @method DoctrineEntityMetadata getMetadataFor($name)
 */
class DoctrineEntityMetadataFactory extends ClassMetadataFactory
{
    /**
     * @var DoctrineEntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function newClassMetadataInstance($className)
    {
        return new DoctrineEntityMetadata(
            $className,
            $this->entityManager->getDomainMetadataFactory()->create(),
            $this->entityManager->getConfiguration()->getNamingStrategy()
        );
    }

    /**
     * @inheritDoc
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        parent::setEntityManager($em);
        $this->entityManager = $em;
    }
}