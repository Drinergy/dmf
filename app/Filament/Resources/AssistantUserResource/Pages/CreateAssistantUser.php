<?php

namespace App\Filament\Resources\AssistantUserResource\Pages;

use App\Filament\Resources\AssistantUserResource;
use App\Support\PermissionCodes;
use Filament\Resources\Pages\CreateRecord;

class CreateAssistantUser extends CreateRecord
{
    protected static string $resource = AssistantUserResource::class;

    /**
     * Permission codes from the form, applied after the user row exists.
     *
     * @var list<string>
     */
    protected array $pendingPermissionCodes = [];

    /**
     * Single-action create flow only — hide "Create & create another" and its handler.
     */
    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        [$this->pendingPermissionCodes, $data] = PermissionCodes::extractPermissionCodesFromFormData($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncPermissionsByCode($this->pendingPermissionCodes);
    }

    /**
     * After create, go to the assistants list — not the edit page (Filament default).
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    /**
     * Success toast copy — clearer than the generic Filament "Created" title.
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Assistant Created';
    }
}
