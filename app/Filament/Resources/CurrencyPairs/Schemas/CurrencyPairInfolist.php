<?php

namespace App\Filament\Resources\CurrencyPairs\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CurrencyPairInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('symbol'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('base'),
                TextEntry::make('quote'),
                // TextEntry::make('digits')
                //     ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('deleted_at')
                    ->dateTime(),
            ]);
    }
}
