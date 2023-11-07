<?php

namespace App\Models;

use App\Models\Character;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'air_date',
        'episode',
    ];

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class);
    }
}
