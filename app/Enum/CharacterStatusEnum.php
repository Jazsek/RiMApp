<?php
namespace App\Enum;


enum CharacterStatusEnum:string
{
    case ALIVE = 'Alive';
    case DEAD = 'Dead';
    case UNKNOWN = 'Unknown';
}
