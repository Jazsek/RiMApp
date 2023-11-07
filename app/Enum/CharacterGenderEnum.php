<?php
namespace App\Enum;
use Filament\Support\Contracts\HasLabel;


enum CharacterGenderEnum: string implements HasLabel
{
    case Female = 'Female';
    case Male = 'Male';
    case Genderless = 'Genderless';
    case Unknown = 'unknown';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
