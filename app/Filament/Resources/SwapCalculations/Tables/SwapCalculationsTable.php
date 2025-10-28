<?php

namespace App\Filament\Resources\SwapCalculations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SwapCalculationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pair.symbol')
                    ->label('Pair')
                    ->sortable()
                    ->searchable(),
                // TextColumn::make('profile.name')
                //     ->label('Profile')
                //     ->sortable()
                //     ->searchable(),
                TextColumn::make('lot_size')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('position_type'),
                TextColumn::make('swap_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('days')
                    ->numeric()
                    ->sortable(),
                // IconColumn::make('cross_wednesday')
                //     ->boolean(),
                TextColumn::make('total_swap')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('note')
                    ->searchable(),
                // TextColumn::make('inputs')
                //     ->label('Inputs')
                //     ->limit(80)
                //     ->wrap(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
