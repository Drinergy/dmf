<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use App\Filament\Resources\EnrollmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEnrollment extends ViewRecord
{
    protected static string $resource = EnrollmentResource::class;
    protected ?string $subheading = null;

    public function getTitle(): string
    {
        $record = $this->getRecord();

        $fullName = trim(sprintf(
            '%s %s %s',
            (string) ($record->first_name ?? ''),
            (string) ($record->middle_name ?? ''),
            (string) ($record->surname ?? ''),
        ));

        $label = $fullName !== '' ? $fullName : 'Student';

        return "Enrollment Record — {$label}";
    }

    // Hide breadcrumbs
    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
            ->label('Back to Enrollments')
            ->icon('heroicon-m-arrow-left')
            ->color('gray')
            ->url(fn() => static::getResource()::getUrl('index')),
        ];
    }
}