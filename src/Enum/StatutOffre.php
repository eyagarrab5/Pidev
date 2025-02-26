<?php

namespace App\Enum;

enum StatutOffre: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case ACCEPTEE = 'ACCEPTEE';
    case REFUSEE = 'REFUSEE';
}
