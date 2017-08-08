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

use Vainyl\Doctrine\Common\DoctrineSettings;

/**
 * Class DoctrineEntitySettings
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineEntitySettings extends DoctrineSettings
{
    private $globalFileName;

    private $fileExtension;

    private $proxyNamespace;

    private $tempDir;

    /**
     * DoctrineEntitySettings constructor.
     *
     * @param DoctrineSettings $doctrineSettings
     * @param string           $globalFileName
     * @param string           $fileExtension
     * @param string           $proxyNamespace
     * @param string           $tempDir
     */
    public function __construct(
        DoctrineSettings $doctrineSettings,
        string $globalFileName,
        string $fileExtension,
        string $proxyNamespace,
        string $tempDir
    ) {
        $this->globalFileName = $globalFileName;
        $this->fileExtension = $fileExtension;
        $this->proxyNamespace = $proxyNamespace;
        $this->tempDir = $tempDir;
        parent::__construct(
            $doctrineSettings->getCache(),
            $doctrineSettings->getDriverName(),
            $doctrineSettings->getExtraPaths()
        );
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