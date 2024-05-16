<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Enums\OrderStatus;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\OrdersResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrdersResource\RelationManagers;

class OrdersResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                        ->schema(static::getDetailsFormSchema())
                        ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orderId')
                            ->label('Order ID')
                            ->searchable()
                            ->sortable(),
                TextColumn::make('user.name')
                            ->label('Customer')
                            ->searchable()
                            ->sortable(),
                TextColumn::make('total_price')
                            ->summarize([
                                Sum::make()->money(),
                            ]),
                TextColumn::make('status')
                            ->badge(),
                TextColumn::make('created_at')
                            ->label('Order Date')
                            ->date()
                            ->toggleable(),
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
            ])
            ->groups([
                Group::make('created_at')
                    ->label('Order Date')
                    ->date()
                    ->collapsible(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrders::route('/create'),
            'edit' => Pages\EditOrders::route('/{record}/edit'),
        ];
    }

    public static function getDetailsFormSchema(): array
    {
        return [
            TextInput::make('orderId')
                    ->label('Order ID')
                    ->default('OR-'.mt_rand(100000, 999999))
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->unique(Order::class, 'orderId', ignoreRecord:true),

            Select::make('user_id')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required()
                        ->createOptionForm([
                            TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            TextInput::make('email')
                                    ->label('Email Address')
                                    ->required()
                                    ->email()
                                    ->maxLength(255)
                                    ->unique(),
                            TextInput::make('password')
                                    ->required()
                                    ->password()
                                    ->revealable()
                        ])
                        ->createOptionAction(function (Action $action) {
                            return $action
                                ->modalHeading('Create customer')
                                ->modalSubmitActionLabel('Create customer')
                                ->modalWidth('lg');
                        }),

            ToggleButtons::make('status')
                        ->inline()
                        ->required()
                        ->options(OrderStatus::class),
            
            Select::make('payment_status')
                        ->required()
                        ->options([
                            'paid' => 'Paid',
                            'pending' => 'Pending',
                            'declined' => 'Declined'
                        ])
                        ->native(false),

            MarkdownEditor::make('description')
                        ->label('Notes')
                        ->columnSpan('full'),
        ];
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('orderProducts')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::query()->pluck('name', 'id'))
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Set $set) => $set('price', Product::find($state)?->price ?? 0))
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->columnSpan([
                                        'md' => 5,
                                    ])
                                    ->searchable(),

                            TextInput::make('qty')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->columnSpan([
                                        'md' => 2,
                                    ])
                                    ->required(),

                            TextInput::make('price')
                                    ->label('Unit Price')
                                    ->disabled()
                                    ->prefix('₦')
                                    ->dehydrated()
                                    ->numeric()
                                    ->required()
                                    ->columnSpan([
                                        'md' => 3,
                                    ]),

                        ])
                        ->defaultItems(1)
                        ->hiddenLabel()
                        ->columns([
                            'md' => 10,
                        ])
                        ->required();
    }
}
