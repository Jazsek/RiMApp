<?php
namespace App\Enum;
use Filament\Support\Contracts\HasLabel;


enum CharacterStatusEnum: string implements HasLabel
{
    case Alive = 'Alive';
    case Dead = 'Dead';
    case Unknown = 'unknown';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
