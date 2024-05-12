<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Subdomain;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SubdomainResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubdomainResource\RelationManagers;

class SubdomainResource extends Resource
{
    protected static ?string $model = Subdomain::class;

    protected static ?string $navigationLabel = 'Subdomain';

    protected static ?string $navigationIcon = 'heroicon-c-globe-alt';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Subdomain Name')
                            ->description('A unique name for your store.')
                            ->completedIcon('heroicon-m-hand-thumb-up')
                            ->schema([
                                TextInput::make('name')
                                        ->required()
                                        ->unique(Subdomain::class, 'name', fn ($record) => $record),
                            ]),
                    Wizard\Step::make('Description')
                            ->description('A short summary.')
                            ->completedIcon('heroicon-m-hand-thumb-up')
                            ->schema([
                                TextInput::make('description')
                                        ->required()
                            ]),
                ])->columnspan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description'),
                ToggleColumn::make('is_visible'),
                            
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
