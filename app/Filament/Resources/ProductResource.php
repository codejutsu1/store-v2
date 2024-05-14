<?php

namespace App\Filament\Resources;

use App\Models\Tag;
use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Subdomain;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

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
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                            ->required(),
                                    TextInput::make('slug')
                                            ->label('Product Slug')
                                            ->readOnly()
                                            ->required(),
                                    MarkdownEditor::make('description')
                                                ->label('A short description of your product.')
                                                ->required()
                                                ->columnSpan('full')
                                ])->columns(2),

                        Section::make('Pricing')
                                ->schema([
                                    TextInput::make('price')
                                            ->prefix('₦')
                                            ->numeric()
                                            ->minValue(1)
                                            ->required(),

                                    TextInput::make('cost')
                                            ->label('Cost per item')
                                            ->prefix('₦')
                                            ->minValue(1)
                                            ->helperText('Customers won\'t see this price.')
                                            ->numeric(),

                                    TextInput::make('discount')
                                            ->label('Discount Price')
                                            ->minValue(1)
                                            ->numeric()
                                            ->prefix('₦')
                                            ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])


                                ])->columns(2),

                        Section::make('Image')
                                    ->schema([
                                        FileUpload::make('image')
                                                ->image()
                                                ->imageEditor()
                                                ->directory('products')
                                                ->preserveFilenames()
                                                ->downloadable()
                                                ->openable()
                                                ->getUploadedFileNameForStorageUsing(
                                                    fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                                        ->prepend(Subdomain::where('user_id', auth()->id())->value('name') . '-'),
                                                )
                                                ->maxSize(512),
                                    ])
                                    ->collapsible(),
                        Section::make('Mutiple Images')
                                    ->schema([
                                        FileUpload::make('extra_images')
                                                ->label('Extra Images')
                                                ->helperText('Upload extra multiple image of your product')
                                                ->image()
                                                ->imageEditor()
                                                ->multiple()
                                                ->reorderable()
                                                ->appendFiles()
                                                ->directory('products')
                                                ->preserveFilenames()
                                                ->downloadable()
                                                ->openable()
                                                ->getUploadedFileNameForStorageUsing(
                                                    fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                                        ->prepend(Subdomain::where('user_id', auth()->id())->value('name') . '-'),
                                                )
                                                ->maxSize(512)
                                                ->maxFiles(5)
                                                ->columns(2),
                                    ])
                                    ->collapsible(),
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
                            Section::make('Attributes')
                                    ->schema([
                                        TextInput::make('quantity')
                                            ->label('Quantity of products in stock.')
                                            ->numeric()
                                            ->minValue(1)
                                            ->required(),

                                        TextInput::make('weight')
                                            ->label('Measurement of Product.')
                                            ->numeric()
                                            ->minValue(1),

                                        Select::make('unit')
                                            ->options([
                                                'kg' => 'Kilogram (Kg)',
                                                'ltr' => 'Litre (ltr)',
                                                'g' => 'Gram (g)',
                                            ]),
                                    ]),
                            Section::make('Association')
                                    ->schema([
                                        Select::make('category_id')
                                                ->relationship(name: 'category', titleAttribute:'name')
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
                                                ->helperText('Click Enter after inputting your tag,')
                                    ]),
                        ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
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
