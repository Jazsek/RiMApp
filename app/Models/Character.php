<?php

namespace App\Models;

use App\Enum\CharacterGenderEnum;
use App\Enum\CharacterStatusEnum;
use App\Models\Episode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'species',
        'type',
        'gender',
        'image',
    ];

    protected $casts = [
        'status' => CharacterStatusEnum::class,
        'gender' => CharacterGenderEnum::class
    ];

    public function episodes(): BelongsToMany
    {
        return $this->belongsToMany(Episode::class);
    }
}
