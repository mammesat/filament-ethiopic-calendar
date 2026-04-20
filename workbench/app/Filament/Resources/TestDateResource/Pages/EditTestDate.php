<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\TestDateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Workbench\App\Filament\Resources\TestDateResource;

class EditTestDate extends EditRecord
{
    protected static string $resource = TestDateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
