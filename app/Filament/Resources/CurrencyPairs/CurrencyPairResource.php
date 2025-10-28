<?php

namespace App\Filament\Resources\CurrencyPairs;

use App\Filament\Resources\CurrencyPairs\Pages\CreateCurrencyPair;
use App\Filament\Resources\CurrencyPairs\Pages\EditCurrencyPair;
use App\Filament\Resources\CurrencyPairs\Pages\ListCurrencyPairs;
use App\Filament\Resources\CurrencyPairs\Pages\ViewCurrencyPair;
use App\Filament\Resources\CurrencyPairs\Schemas\CurrencyPairForm;
use App\Filament\Resources\CurrencyPairs\Schemas\CurrencyPairInfolist;
use App\Filament\Resources\CurrencyPairs\Tables\CurrencyPairsTable;
use App\Models\CurrencyPair;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CurrencyPairResource extends Resource
{
    protected static ?string $model = CurrencyPair::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowsRightLeft;

    public static function form(Schema $schema): Schema
    {
        return CurrencyPairForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CurrencyPairInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CurrencyPairsTable::configure($table);
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
            'index' => ListCurrencyPairs::route('/'),
            'create' => CreateCurrencyPair::route('/create'),
            'view' => ViewCurrencyPair::route('/{record}'),
            'edit' => EditCurrencyPair::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
