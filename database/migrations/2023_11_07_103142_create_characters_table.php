<?php

use App\Enum\CharacterGenderEnum;
use App\Enum\CharacterStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', [
                CharacterStatusEnum::ALIVE,
                CharacterStatusEnum::DEAD,
                CharacterStatusEnum::UNKNOWN
            ]);
            $table->string('species');
            $table->string('type')->nullable();
            $table->enum('gender', [
                CharacterGenderEnum::FEMALE,
                CharacterGenderEnum::MALE,
                CharacterGenderEnum::GENDERLESS,
                CharacterGenderEnum::UNKNOWN
            ]);
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
