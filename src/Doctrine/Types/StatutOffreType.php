<?php

namespace App\Doctrine\Types;

use App\Enum\StatutOffre;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class StatutOffreType extends StringType
{
    const STATUT_OFFRE = 'statut_offre'; // Nom du type personnalisé

    public function convertToPHPValue($value, AbstractPlatform $platform): ?StatutOffre
    {
        return $value !== null ? StatutOffre::from($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof StatutOffre ? $value->value : null;
    }

    public function getName(): string
    {
        return self::STATUT_OFFRE;
    }
}
