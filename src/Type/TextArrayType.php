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
declare(strict_types = 1);

namespace Vainyl\Doctrine\ORM\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Class TextArrayType
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class TextArrayType extends Type
{
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
    public function canRequireSQLConversion()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function convertToPHPValueSQL($value, $platform)
    {
        return sprintf('array_to_json(%s)', $value);
    }

    /**
     * @inheritDoc
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('(select array_agg(json) from json_array_elements_text(%s) as json)', $sqlExpr);
    }

    /**
     * @inheritDoc
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return json_encode($value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return json_decode($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'text_array';
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'TEXT[]';
    }
}