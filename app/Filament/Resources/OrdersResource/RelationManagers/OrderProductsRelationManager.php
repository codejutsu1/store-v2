<?php

namespace App\Filament\Resources\OrdersResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderProducts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('product.image')
                                        ->label('Image'),
                Tables\Columns\TextColumn::make('product.name')
                                        ->label('Name')
                                        ->searchable()
                                        ->sortable(),
                Tables\Columns\TextColumn::make('qty'),
                Tables\Columns\TextColumn::make('price')
                                        ->numeric(decimalPlaces:2)
                                        ->prefix('₦'),
                Tables\Columns\TextColumn::make('product.description')
                                        ->label('Short Note'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
