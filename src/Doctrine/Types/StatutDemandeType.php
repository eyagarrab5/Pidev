<?php

namespace App\Doctrine\Types;

use App\Enum\StatutDemande;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\StringType;

class StatutDemandeType extends StringType
{
    const STATUT_DEMANDE = 'statut_demande'; // Nom du type personnalisé

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return $value !== null ? StatutDemande::from($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof StatutDemande ? $value->value : null;
    }

    public function getName(): string
    {
        return self::STATUT_DEMANDE;
    }
}
