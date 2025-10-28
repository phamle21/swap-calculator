<?php

namespace App\Filament\Resources\SwapCalculations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SwapCalculationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('currency_pair_id')
                    ->label('Currency Pair')
                    ->relationship('pair', 'symbol')
                    ->searchable()
                    ->required(),
                // Select::make('profile_id')
                //     ->label('Profile')
                //     ->relationship('profile', 'name')
                //     ->searchable()
                //     ->preload()
                //     ->nullable(),
                TextInput::make('lot_size')
                    ->required()
                    ->numeric()
                    ->step('0.01'),
                Select::make('position_type')
                    ->options(['Long' => 'Long', 'Short' => 'Short'])
                    ->required(),
                TextInput::make('swap_rate')
                    ->required()
                    ->numeric()
                    ->step('0.0001'),
                TextInput::make('days')
                    ->required()
                    ->numeric()
                    ->step(1),
                // Toggle::make('cross_wednesday'),
                TextInput::make('total_swap')
                    ->numeric()
                    ->disabled()
                    ->helperText('Computed total swap (read-only)'),
                Textarea::make('note')
                    ->rows(3),
                KeyValue::make('inputs')
                    ->keyLabel('Key')
                    ->valueLabel('Value')
                    ->columns(1),
            ]);
    }
}
