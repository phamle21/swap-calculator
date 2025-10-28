<?php

namespace App\Filament\Resources\SwapCalculations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SwapCalculationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('pair.symbol'),
                // TextEntry::make('profile.name'),
                //     ->numeric(),
                TextEntry::make('lot_size')
                    ->numeric(),
                TextEntry::make('position_type'),
                TextEntry::make('swap_rate')
                    ->numeric(),
                TextEntry::make('days')
                    ->numeric(),
                // IconEntry::make('cross_wednesday')
                //     ->boolean(),
                TextEntry::make('total_swap')
                    ->numeric(),
                TextEntry::make('note'),
                TextEntry::make('created_at')
                    ->dateTime(),
            ]);
    }
}
