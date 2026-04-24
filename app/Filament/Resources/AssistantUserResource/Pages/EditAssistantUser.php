<?php

namespace App\Filament\Resources\AssistantUserResource\Pages;

use App\Filament\Resources\AssistantUserResource;
use App\Support\PermissionCodes;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditAssistantUser extends EditRecord
{
    protected static string $resource = AssistantUserResource::class;

    /**
     * @var list<string>
     */
    protected array $pendingPermissionCodes = [];

    public function getTitle(): string|Htmlable
    {
        return 'Edit Assistant - '.$this->getRecord()->name;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Changes Saved';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $codes = $this->record->permissions()->pluck('code')->all();

        return array_merge($data, PermissionCodes::expandCodesToPermissionFormState($codes));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        [$this->pendingPermissionCodes, $data] = PermissionCodes::extractPermissionCodesFromFormData($data);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->syncPermissionsByCode($this->pendingPermissionCodes);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
