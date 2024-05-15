<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use App\Enums\OrderStatus;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
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
                    ->disabled()
                    ->dehydrated()
                    ->required()
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
        return Repeater::make('Products')
                        ->schema([

                        ]);
    }
}
