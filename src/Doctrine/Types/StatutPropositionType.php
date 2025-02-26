<?php

namespace App\Doctrine\Types;

use App\Enum\StatutProposition;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\StringType;

class StatutPropositionType extends StringType
{
    const STATUT_PROPOSITION = 'statutProposition'; // Nom du type personnalisé

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? StatutProposition::from($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof StatutProposition ? $value->value : null;
    }

    public function getName()
    {
        return self::STATUT_PROPOSITION;
    }
}
