<?php

namespace App\Filament\Resources;

use App\Models\Tag;
use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make()
                                ->schema([
                                    TextInput::make('name')
                                            ->label('Name of Product')
                                            ->required(),
                                    TextInput::make('slug')
                                            ->label('Product Slug')
                                            ->required(),
                                    MarkdownEditor::make('description')
                                                ->label('A short description of your product.')
                                                ->required()
                                                ->columnSpan('full')
                                ])->columns(2),

                        Section::make('Image')
                                    ->schema([
                                        FileUpload::make('image')
                                                ->image()
                                                ->imageEditor(),
                                    ])
                                    ->collapsible(),

                        Section::make('Pricing')
                                    ->schema([
                                        TextInput::make('price')
                                                ->numeric()
                                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                                ->required(),

                                        TextInput::make('cost')
                                                ->label('Cost per item')
                                                ->helperText('Customers won\'t see this price.')
                                                ->numeric()
                                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                                ->required(),


                                    ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                    Group::make()
                        ->schema([
                            Section::make('Status')
                                    ->schema([
                                        Toggle::make('is_visible')
                                                ->label('Visible')
                                                ->helperText('Choose to show or hide this product from your customers.')
                                                ->default(true),
                                    ]),
                            Section::make('Association')
                                    ->schema([
                                        Select::make('categories')
                                                ->relationship('category', 'name')
                                                ->searchable()
                                                ->preload()
                                                ->required(),
                                    ]),
                            Section::make('Tags')
                                    ->schema([
                                        Select::make('tags')
                                                ->multiple()
                                                ->relationship(titleAttribute:'name')
                                                ->searchable()
                                                ->preload()
                                                ->required(),

                                        TagsInput::make('extra_tags')
                                                ->label('Extra Tags')
                                                ->separator(',')
                                                ->helperText('Click Enter after inputting your tag,')
                                    ]),
                        ])->columnSpan(['lg' => 1]),
            ])->columns(3);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
