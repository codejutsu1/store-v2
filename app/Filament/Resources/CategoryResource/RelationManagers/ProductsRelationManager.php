<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

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
                ImageColumn::make('image'),
                TextColumn::make('name')
                            ->sortable()
                            ->searchable(),
                TextColumn::make('category.name')
                            ->sortable()
                            ->searchable()
                            ->toggleable(),
                TextColumn::make('slug')
                            ->toggleable()
                            ->sortable()
                            ->toggledHiddenByDefault(),
                TextColumn::make('price')
                            ->numeric(decimalPlaces:2)
                            ->prefix('₦')
                            ->sortable(),
                TextColumn::make('discount')
                            ->numeric(decimalPlaces:2)
                            ->prefix('₦')
                            ->sortable()
                            ->toggleable()
                            ->toggledHiddenByDefault(),
                TextColumn::make('quantity')
                            ->toggleable()
                            ->toggledHiddenByDefault(),
                SelectColumn::make('unit')
                            ->options([
                                'kg' => 'Kilogram (Kg)',
                                'ltr' => 'Litre (ltr)',
                                'g' => 'Gram (g)',
                            ])
                            ->toggleable()
                            ->toggledHiddenByDefault()
                            ->rules(['required']),
                IconColumn::make('is_visible')
                            ->label('Visibility')
                            ->boolean(),
                TextColumn::make('created_at')
                            ->label('Created')
                            ->since(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
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
