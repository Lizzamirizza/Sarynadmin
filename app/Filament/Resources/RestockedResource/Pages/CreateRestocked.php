<?php

namespace App\Filament\Resources\RestockedResource\Pages;

use App\Filament\Resources\RestockedResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRestocked extends CreateRecord
{
    protected static string $resource = RestockedResource::class;
}
