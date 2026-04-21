<?php

declare(strict_types=1);

namespace App\Filament\Resources\EnrollmentResource\Pages;

use App\Filament\Resources\EnrollmentResource;
use App\Models\Enrollment;
use App\Services\EnrollmentFinancialService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Js;

class ViewEnrollment extends ViewRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected ?string $subheading = null;

    /**
     * Sync tuition ledger from paid payments so the infolist shows correct cumulative tuition (legacy rows may have had `tuition_amount` = 0).
     *
     * @author CKD
     *
     * @created 2026-03-26
     */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        /** @var Enrollment $enrollment */
        $enrollment = $this->getRecord();
        app(EnrollmentFinancialService::class)->recalculateEnrollmentFinancials($enrollment);
        $enrollment->refresh();
    }

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

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        $payBalanceUrl = $this->resolvePayBalanceSignedUrl();
        if ($payBalanceUrl !== null) {
            $actions[] = Actions\Action::make('copyPayBalanceLink')
                ->label('Copy payment link')
                ->icon('heroicon-m-clipboard-document')
                ->color('warning')
                ->action(function () use ($payBalanceUrl): void {
                    $this->js('window.navigator.clipboard.writeText('.Js::from($payBalanceUrl).')');

                    Notification::make()
                        ->title('Payment link copied')
                        ->body('Paste it into SMS, Messenger, Viber, or email for the student.')
                        ->success()
                        ->send();
                });
        }

        $actions[] = Actions\Action::make('back')
            ->label('Back to Enrollments')
            ->icon('heroicon-m-arrow-left')
            ->color('gray')
            ->url(fn () => static::getResource()::getUrl('index'));

        return $actions;
    }

    private function resolvePayBalanceSignedUrl(): ?string
    {
        /** @var Enrollment $record */
        $record = $this->getRecord();

        if ($record->payment_type !== 'downpayment' || $record->computed_balance_tuition_due <= 0) {
            return null;
        }

        return URL::temporarySignedRoute(
            'enroll.balance',
            now()->addYears(5),
            ['reference_number' => $record->reference_number],
        );
    }
}
