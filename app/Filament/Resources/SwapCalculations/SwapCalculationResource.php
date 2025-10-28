<?php

namespace App\Filament\Resources\SwapCalculations;

use App\Filament\Resources\SwapCalculations\Pages\CreateSwapCalculation;
use App\Filament\Resources\SwapCalculations\Pages\EditSwapCalculation;
use App\Filament\Resources\SwapCalculations\Pages\ListSwapCalculations;
use App\Filament\Resources\SwapCalculations\Pages\ViewSwapCalculation;
use App\Filament\Resources\SwapCalculations\Schemas\SwapCalculationForm;
use App\Filament\Resources\SwapCalculations\Schemas\SwapCalculationInfolist;
use App\Filament\Resources\SwapCalculations\Tables\SwapCalculationsTable;
use App\Models\SwapCalculation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SwapCalculationResource extends Resource
{
    protected static ?string $model = SwapCalculation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Calculator;

    public static function form(Schema $schema): Schema
    {
        return SwapCalculationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SwapCalculationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SwapCalculationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSwapCalculations::route('/'),
            'create' => CreateSwapCalculation::route('/create'),
            'view' => ViewSwapCalculation::route('/{record}'),
            'edit' => EditSwapCalculation::route('/{record}/edit'),
        ];
    }
}
