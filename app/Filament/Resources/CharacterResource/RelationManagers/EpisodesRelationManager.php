<?php

namespace App\Filament\Resources\CharacterResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\RelationManagers\RelationManager;

class EpisodesRelationManager extends RelationManager
{
    protected static string $relationship = 'episodes';

    public function form(Form $form): Form
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
