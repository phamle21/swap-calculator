<?php

namespace App\Filament\Resources\CurrencyPairs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;

class CurrencyPairForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('symbol')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('base')
                    ->required(),
                TextInput::make('quote')
                    ->required(),
                // TextInput::make('digits')
                //     ->required()
                //     ->numeric()
                //     ->default(5),
                // KeyValue::make('meta')
                //     ->keyLabel('Key')
                //     ->valueLabel('Value')
                //     ->columns(1),
            ]);
    }
}
