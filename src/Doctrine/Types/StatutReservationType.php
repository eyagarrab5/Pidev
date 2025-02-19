<?php

namespace App\Doctrine\Types;

use App\Enum\StatutReservation;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\StringType;

class StatutReservationType extends StringType
{
    const STATUT_RESERVATION = 'statut_reservation'; // Nom du type personnalisé

    public function convertToPHPValue($value, AbstractPlatform $platform): ?StatutReservation
    {
        return $value !== null ? StatutReservation::from($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof StatutReservation ? $value->value : null;
    }

    public function getName(): string
    {
        return self::STATUT_RESERVATION;
    }
}
