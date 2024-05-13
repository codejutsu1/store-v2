<?php

namespace App\Filament\Resources\SubdomainResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SubdomainResource;

class ListSubdomains extends ListRecords
{
    protected static string $resource = SubdomainResource::class;

    protected function getHeaderActions(): array
    {
        if(!Auth()->user()->subdomain->exists()){
            return [
                Actions\CreateAction::make(),
            ];
        }

        return [];
    }
}
