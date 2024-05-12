<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubdomainResource\Pages;
use App\Filament\Resources\SubdomainResource\RelationManagers;
use App\Models\Subdomain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubdomainResource extends Resource
{
    protected static ?string $model = Subdomain::class;

    protected static ?string $navigationLabel = 'Subdomain';

    protected static ?string $navigationIcon = 'heroicon-c-globe-alt';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubdomains::route('/'),
            'create' => Pages\CreateSubdomain::route('/create'),
            'edit' => Pages\EditSubdomain::route('/{record}/edit'),
        ];
    }
}
