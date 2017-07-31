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

use Vainyl\Core\AbstractArray;
use Vainyl\Core\ArrayInterface;
use Doctrine\Common\Cache\Cache as DoctrineCacheInterface;

/**
 * Class DoctrineORMSettings
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineORMSettings extends AbstractArray implements ArrayInterface
{
    private $cache;

    private $driverName;

    private $globalFileName;

    private $fileExtension;

    private $proxyNamespace;

    private $tempDir;

    private $extraPaths;

    /**
     * DoctrineORMSettings constructor.
     *
     * @param DoctrineCacheInterface $cache
     * @param string                 $driverName
     * @param string                 $globalFileName
     * @param string                 $fileExtension
     * @param string                 $proxyNamespace
     * @param string                 $tempDir
     * @param array                  $extraPaths
     */
    public function __construct(
        DoctrineCacheInterface $cache,
        string $driverName,
        string $globalFileName,
        string $fileExtension,
        string $proxyNamespace,
        string $tempDir,
        array $extraPaths
    ) {
        $this->cache = $cache;
        $this->driverName = $driverName;
        $this->globalFileName = $globalFileName;
        $this->fileExtension = $fileExtension;
        $this->proxyNamespace = $proxyNamespace;
        $this->tempDir = $tempDir;
        $this->extraPaths = $extraPaths;
    }

    /**
     * @return DoctrineCacheInterface
     */
    public function getCache(): DoctrineCacheInterface
    {
        return $this->cache;
    }

    /**
     * @return string
     */
    public function getDriverName(): string
    {
        return $this->driverName;
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    /**
     * @return string
     */
    public function getGlobalFileName(): string
    {
        return $this->globalFileName;
    }

    /**
     * @return string
     */
    public function getProxyNamespace(): string
    {
        return $this->proxyNamespace;
    }

    /**
     * @return string
     */
    public function getTempDir(): string
    {
        return $this->tempDir;
    }

    /**
     * @return array
     */
    public function getExtraPaths(): array
    {
        return $this->extraPaths;
    }
}