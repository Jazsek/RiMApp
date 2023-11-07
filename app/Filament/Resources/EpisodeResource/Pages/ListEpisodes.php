<?php

namespace App\Filament\Resources\EpisodeResource\Pages;

use Filament\Actions;
use App\Models\Episode;
use App\Models\Character;
use Illuminate\Support\Str;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\EpisodeResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use NickBeen\RickAndMortyPhpApi\Episode as RickAndMortyPhpApiEpisode;
use NickBeen\RickAndMortyPhpApi\Character as RickAndMortyPhpApiCharacter;

class ListEpisodes extends ListRecords
{
    protected static string $resource = EpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('callApi')
                ->label('Sync episodes and characters with API')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Sync episodes and characters with API')
                ->modalDescription('Attention! The process may take several minutes (estimated time: 5min), as the system checks that you cannot create the same character more than once.')
                ->action(function () {
                    $this->importEpisodesAndCharacters();
                })
        ];
    }

    private function importEpisodesAndCharacters(): void
    {
        $this->truncateTables();

        $episodeAPI = new RickAndMortyPhpApiEpisode;
        $page = 1;
        $lastPage = null;

        do {
            $episodes = $episodeAPI->page($page)->get();

            if (empty($lastpage)){
                $lastPage = $episodes->info->pages;
            }

            foreach($episodes->results as $episodeObj) {
                $episode = $this->createEpisodes($episodeObj);

                if(!empty($episodeObj->characters)){
                    foreach($episodeObj->characters as $characterURL){
                        $this->createCharactersAndAttachToEpisode($characterURL, $episode);
                    }
                }
            }

            $page++;
        } while($page <= $lastPage);
    }

    private function truncateTables(): void
    {
        DB::table('character_episode')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Character::truncate();
        Episode::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function createEpisodes(object $episodeObj): Model
    {
        return Episode::create([
            'name' => $episodeObj->name,
            'air_date' => $episodeObj->air_date,
            'episode' => $episodeObj->episode,
        ]);
    }

    private function createCharactersAndAttachToEpisode(string $characterURL, Model $episode): void
    {
        $characterAPI = new RickAndMortyPhpApiCharacter;

        $character = $characterAPI->get(Str::remove('https://rickandmortyapi.com/api/character/', $characterURL));

        Character::firstOrCreate(
            [
                'name' => $character->name,
                'status' => $character->status,
                'species' => $character->species,
                'type' => $character->type,
                'gender' => $character->gender,
                'image' => $character->image,
            ]
        )->episodes()->attach($episode);
    }
}
