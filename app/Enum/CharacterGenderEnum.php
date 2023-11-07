<?php
namespace App\Enum;


enum CharacterGenderEnum:string
{
    case FEMALE = 'Female';
    case MALE = 'Male';
    case GENDERLESS = 'Genderless';
    case UNKNOWN = 'Unknown';
}
