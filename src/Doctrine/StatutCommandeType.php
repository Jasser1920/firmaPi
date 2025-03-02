<?php

namespace App\Doctrine;

use App\Enum\StatutCommande;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class StatutCommandeType extends Type
{
    public const NAME = 'statut_commande';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof StatutCommande) {
            return $value->value;
        }

        throw new \InvalidArgumentException('Expected StatutCommande, got ' . (is_object($value) ? get_class($value) : gettype($value)));
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?StatutCommande
    {
        if ($value === null) {
            return null;
        }

        return StatutCommande::from($value);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}