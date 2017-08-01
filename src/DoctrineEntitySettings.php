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

use Vainyl\Core\AbstractArray;
use Doctrine\Common\Cache\Cache as DoctrineCacheInterface;
use Vainyl\Doctrine\Common\DoctrineSettings;

/**
 * Class DoctrineEntitySettings
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntitySettings extends AbstractArray
{
    private $doctrineSettings;

    private $driverName;

    private $globalFileName;

    private $fileExtension;

    private $proxyNamespace;

    private $tempDir;

    /**
     * DoctrineEntitySettings constructor.
     *
     * @param DoctrineSettings $doctrineSettings
     * @param string           $driverName
     * @param string           $globalFileName
     * @param string           $fileExtension
     * @param string           $proxyNamespace
     * @param string           $tempDir
     */
    public function __construct(
        DoctrineSettings $doctrineSettings,
        string $driverName,
        string $globalFileName,
        string $fileExtension,
        string $proxyNamespace,
        string $tempDir
    ) {
        $this->doctrineSettings = $doctrineSettings;
        $this->driverName = $driverName;
        $this->globalFileName = $globalFileName;
        $this->fileExtension = $fileExtension;
        $this->proxyNamespace = $proxyNamespace;
        $this->tempDir = $tempDir;
    }

    /**
     * @return DoctrineCacheInterface
     */
    public function getCache(): DoctrineCacheInterface
    {
        return $this->doctrineSettings->getCache();
    }

    /**
     * @return string
     */
    public function getDriverName(): string
    {
        return $this->driverName;
    }

    /**
     * @return array
     */
    public function getExtraPaths(): array
    {
        return $this->doctrineSettings->getExtraPaths();
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
}