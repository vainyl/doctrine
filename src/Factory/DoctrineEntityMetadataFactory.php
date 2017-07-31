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

namespace Vainyl\Doctrine\ORM\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Vainyl\Doctrine\ORM\DoctrineEntityManager;
use Vainyl\Doctrine\ORM\DoctrineEntityMetadata;

/**
 * Class DoctrineEntityMetadataFactory
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
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
        return new DoctrineEntityMetadata($className, $this->entityManager->getConfiguration()->getNamingStrategy());
    }

    /**
     * @inheritDoc
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->entityManager = $em;

        return parent::setEntityManager($em);
    }
}