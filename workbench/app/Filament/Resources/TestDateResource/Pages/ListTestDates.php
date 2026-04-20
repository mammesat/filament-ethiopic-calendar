<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\TestDateResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Workbench\App\Filament\Resources\TestDateResource;

class ListTestDates extends ListRecords
{
    protected static string $resource = TestDateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
