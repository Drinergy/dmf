<?php

namespace App\Filament\Resources\AssistantUserResource\Pages;

use App\Filament\Resources\AssistantUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssistantUsers extends ListRecords
{
    protected static string $resource = AssistantUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
