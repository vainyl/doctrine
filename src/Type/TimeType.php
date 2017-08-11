<?php
/**
 * Vain Framework
 *
 * PHP Version 7
 *
 * @package   vain-doctrine
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://github.com/allflame/vain-doctrine
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ORM\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Vainyl\Time\Factory\TimeFactoryInterface;
use Vainyl\Time\TimeInterface;

/**
 * Class TimeType
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class TimeType extends Type
{
    /**
     * @var TimeFactoryInterface
     */
    private $timeFactory;

    /**
     * @param TimeFactoryInterface $timeFactory
     *
     * @return TimeType
     */
    public function setTimeFactory(TimeFactoryInterface $timeFactory): TimeType
    {
        $this->timeFactory = $timeFactory;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof TimeInterface) {
            return $value;
        }

        $val = $this->timeFactory->createFromString($value);

        if (!$val) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateFormatString()
            );
        }

        return $val;
    }

    /**
     * @inheritDoc
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof TimeInterface) {
            return $value->toDateTime();
        }

        throw new \InvalidArgumentException(sprintf('%s is not a properly formatted TIME type.', get_class($value)));
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'v_time';
    }

    /**
     * @inheritDoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getDateTimeTypeDeclarationSQL($fieldDeclaration);
    }
}
