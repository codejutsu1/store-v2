<?php

namespace App\Filament\Resources\SubdomainResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SubdomainResource;

class CreateSubdomain extends CreateRecord
{
    protected static string $resource = SubdomainResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        $data['name'] = Str::slug($data['name']);
    
        return $data;
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Subdomain successfully created.';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
