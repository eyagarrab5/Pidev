<?php

namespace App\Enum;

enum StatutProposition: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case ACCEPTE = 'ACCEPTE';
    case REFUSE = 'REFUSE';
}
