<?php

namespace App\Filament\Resources\SwapCalculations\Schemas;

use Filament\Forms\Components\Radio;
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
                    ->preload()
                    ->searchable()
                    ->required(),

                TextInput::make('lot_size')
                    ->required()
                    ->numeric()
                    ->step('0.01')
                    ->reactive()
                    ->afterStateUpdated(
                        fn($state, callable $set, callable $get) =>
                        self::calculateTotal($set, $get)
                    ),

                TextInput::make('inputs.swap_long')
                    ->label('Swap Long')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->readOnly(fn(callable $get) => $get('position_type') === 'Short')
                    ->helperText(fn(callable $get) =>
                        $get('position_type') === 'Short'
                            ? 'Swap Long is read-only for Short positions.'
                            : null
                    )
                    ->afterStateUpdated(
                        fn($state, callable $set, callable $get) =>
                        self::calculateTotal($set, $get)
                    ),

                TextInput::make('inputs.swap_short')
                    ->label('Swap Short')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->readOnly(fn(callable $get) => $get('position_type') === 'Long')
                    ->helperText(fn(callable $get) =>
                        $get('position_type') === 'Long'
                            ? 'Swap Short is read-only for Long positions.'
                            : null
                    )
                    ->afterStateUpdated(
                        fn($state, callable $set, callable $get) =>
                        self::calculateTotal($set, $get)
                    ),

                Radio::make('position_type')
                    ->label('Position Type')
                    ->options([
                        'Long' => 'Long',
                        'Short' => 'Short',
                    ])
                    ->inline()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(
                        fn($state, callable $set, callable $get) =>
                        self::calculateTotal($set, $get)
                    ),

                TextInput::make('days')
                    ->label('Holding Days')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(
                        fn($state, callable $set, callable $get) =>
                        self::calculateTotal($set, $get)
                    ),

                TextInput::make('total_swap')
                    ->numeric()
                    ->disabled()
                    ->helperText('Computed total swap (read-only)'),

                Textarea::make('note')
                    ->rows(3),

            ]);
    }

    protected static function calculateTotal(callable $set, callable $get): void
    {
        $lot = (float) $get('lot_size');
        $days = (int) $get('days');
        $position = $get('position_type');

        $swapRate = $position === 'Short'
            ? (float) $get('swap_short')
            : (float) $get('swap_long');

        $set('total_swap', round($lot * $swapRate * $days, 2));
    }
}
