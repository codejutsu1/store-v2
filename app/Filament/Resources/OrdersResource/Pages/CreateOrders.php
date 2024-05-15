<?php

namespace App\Filament\Resources\OrdersResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use App\Filament\Resources\OrdersResource;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;

class CreateOrders extends CreateRecord
{
    use HasWizard;
    
    protected static string $resource = OrdersResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
                    ->schema([
                        Wizard::make($this->getSteps())
                                ->startOnStep($this->getStartStep())
                                ->cancelAction($this->getCancelFormAction())
                                ->submitAction($this->getSubmitFormAction())
                                ->skippable($this->hasSkippableSteps())
                                ->contained(false),
                    ])->columns(null);
    }

    public function getSteps(): array
    {
        return [
            Step::make('Order Details')
                ->schema([
                    Section::make()->schema(OrdersResource::getDetailsFormSchema())->columns(),
                ]),

            Step::make('Order Items')
                ->schema([
                    Section::make()->schema([
                        OrdersResource::getItemsRepeater(),
                    ]),
                ]),
        ];
    }
    
}
