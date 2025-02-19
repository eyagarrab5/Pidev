<?php

namespace App\Enum;

enum StatutReservation: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case SELECTIONNE = 'SELECTIONNE';
    case CONFIRME = 'CONFIRME';
}
