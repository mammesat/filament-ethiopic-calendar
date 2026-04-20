<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\TestDateResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Workbench\App\Filament\Resources\TestDateResource;

class CreateTestDate extends CreateRecord
{
    protected static string $resource = TestDateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
