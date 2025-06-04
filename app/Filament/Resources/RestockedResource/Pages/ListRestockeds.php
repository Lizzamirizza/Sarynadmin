<?php

namespace App\Filament\Resources\RestockedResource\Pages;

use App\Filament\Resources\RestockedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRestockeds extends ListRecords
{
    protected static string $resource = RestockedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
