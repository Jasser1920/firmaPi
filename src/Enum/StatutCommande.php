<?php
namespace App\Enum;

enum StatutCommande: string
{
    case EN_ATTENTE = 'en_attente';
    case EN_COURS = 'en_cours';
    case RESOLUE = 'resolue';
    case REJETEE = 'rejetee';


    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::EN_COURS => 'En cours',
            self::RESOLUE => 'Résolue',
            self::REJETEE => 'rejetee',
        };
    }
}