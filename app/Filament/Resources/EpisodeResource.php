<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Episode;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EpisodeResource\Pages;
use App\Filament\Resources\EpisodeResource\RelationManagers;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';

    protected static ?string $navigationGroup = 'Details';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('air_date')
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('episode')
                    ->required()
                    ->maxLength(255),
            ])->columns('full');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('air_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('episode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('characters_count')->counts('characters')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('air_date')
                    ->form([
                        Forms\Components\DatePicker::make('air_date_from')
                            ->native(false),
                        Forms\Components\DatePicker::make('air_date_until')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['air_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('air_date', '>=', $date),
                            )
                            ->when(
                                $data['air_date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('air_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['air_date_from'] && !$data['air_date_until']) {
                            return null;
                        }

                        if ($data['air_date_from'] && !$data['air_date_until']) {
                            return 'Air date from: ' . Carbon::parse($data['air_date_from'])->toFormattedDateString();
                        }

                        if (!$data['air_date_from'] && $data['air_date_until']) {
                            return 'Air date until: ' . Carbon::parse($data['air_date_until'])->toFormattedDateString();
                        }

                        if ($data['air_date_from'] && $data['air_date_until']) {
                            return 'Air date between: ' . Carbon::parse($data['air_date_from'])->toFormattedDateString() . ' and ' . Carbon::parse($data['air_date_until'])->toFormattedDateString();
                        }
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CharactersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEpisodes::route('/'),
            'create' => Pages\CreateEpisode::route('/create'),
            'edit' => Pages\EditEpisode::route('/{record}/edit'),
        ];
    }
}
