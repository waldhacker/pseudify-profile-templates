<?php

declare(strict_types=1);

namespace Waldhacker\Pseudify\Types\TYPO3;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EnumType extends Type
{
    public const TYPE_NAME = 'enum';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return sprintf('ENUM(%s)', implode(', ', array_map([$platform, 'quoteStringLiteral'], $fieldDeclaration['unquotedValues'])));
    }

    public function getName(): string
    {
        return static::TYPE_NAME;
    }
}
