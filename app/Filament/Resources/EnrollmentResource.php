<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Filament\Resources\EnrollmentResource\RelationManagers;
use App\Models\Enrollment;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Js;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Enrollments';

    protected static ?int $navigationSort = 1;

    // Hides the resource from the navigation menu
    protected static bool $shouldRegisterNavigation = false;

    // Disable create button
    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Grid::make(['default' => 1, 'lg' => 2])->schema([
                Infolists\Components\Group::make([
                    Infolists\Components\Section::make('Applicant Profile')
                        ->description('Personal information and contact details.')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Infolists\Components\TextEntry::make('full_name')
                                ->label('Full Name')
                                ->getStateUsing(fn ($record) => trim("{$record->first_name} {$record->middle_name} {$record->surname}"))
                                ->weight('bold')
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                ->columnSpanFull(),
                            Infolists\Components\TextEntry::make('birthday')
                                ->date('F j, Y', config('app.display_timezone'))
                                ->icon('heroicon-o-gift')
                                ->weight('semibold'),
                            Infolists\Components\TextEntry::make('sex')
                                ->icon('heroicon-o-users')
                                ->weight('semibold'),
                            Infolists\Components\TextEntry::make('email')
                                ->icon('heroicon-o-envelope')
                                ->copyable()
                                ->weight('semibold'),
                            Infolists\Components\TextEntry::make('phone')
                                ->icon('heroicon-o-phone')
                                ->copyable()
                                ->weight('semibold'),
                            Infolists\Components\TextEntry::make('facebook')
                                ->icon('heroicon-o-link')
                                ->url(fn ($state) => $state)
                                ->openUrlInNewTab()
                                ->placeholder('—')
                                ->columnSpanFull()
                                ->weight('semibold'),
                        ])->columns(['default' => 1, 'md' => 2]),

                    Infolists\Components\Section::make('Academic Background')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Infolists\Components\TextEntry::make('school')
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                ->columnSpanFull()
                                ->weight('bold'),
                            Infolists\Components\TextEntry::make('year_level')->label('Year Level')->weight('semibold'),
                            Infolists\Components\TextEntry::make('year_graduated')->label('Year Graduated')->placeholder('—')->weight('semibold'),
                            Infolists\Components\TextEntry::make('taker_status')
                                ->label('Taker Status')
                                ->badge()
                                ->color('info'),
                        ])->columns(['default' => 1, 'md' => 3]),

                    Infolists\Components\Section::make('Home Address')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Infolists\Components\TextEntry::make('addr_street')->label('Street')->columnSpanFull()->weight('semibold'),
                            Infolists\Components\TextEntry::make('addr_city')->label('City')->weight('semibold'),
                            Infolists\Components\TextEntry::make('addr_province')->label('Province')->weight('semibold'),
                            Infolists\Components\TextEntry::make('addr_zip')->label('Zip Code')->weight('semibold'),
                        ])->columns(['default' => 1, 'md' => 3]),
                ])->columnSpan(1),

                Infolists\Components\Group::make([
                    Infolists\Components\Section::make('Plan & checkout')
                        ->icon('heroicon-o-credit-card')
                        ->schema([
                            Infolists\Components\TextEntry::make('status')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'confirmed' => 'success',
                                    'paid' => 'success',
                                    'partially_paid' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    'cancelled' => 'danger',
                                    default => 'gray',
                                })
                                ->formatStateUsing(fn ($state) => match ($state) {
                                    'pending' => 'Awaiting Payment',
                                    'partially_paid' => 'Enrolled — DP paid (balance due)',
                                    'confirmed' => 'Enrolled (fully paid)',
                                    'paid' => 'Enrolled',
                                    default => strtoupper((string) $state),
                                })
                                ->columnSpanFull(),
                            Infolists\Components\TextEntry::make('reference_number')
                                ->label('Reference #')
                                ->fontFamily('mono')
                                ->copyable()
                                ->weight('bold'),
                            Infolists\Components\TextEntry::make('program.name')
                                ->label('Program')
                                ->weight('bold')
                                ->color('primary'),
                            Infolists\Components\TextEntry::make('schedule.label')
                                ->label('Batch')
                                ->placeholder('—')
                                ->weight('semibold'),
                            Infolists\Components\TextEntry::make('payment_type')
                                ->label('Plan')
                                ->formatStateUsing(fn ($state) => $state === 'full' ? 'Full' : 'Downpayment')
                                ->badge()
                                ->color('gray'),
                            Infolists\Components\TextEntry::make('base_amount')
                                ->label(fn (Enrollment $record): string => match ($record->payment_type) {
                                    'downpayment' => 'Downpayment (excl. convenience fee)',
                                    'full' => 'Program charge (excl. convenience fee)',
                                    default => 'Checkout amount (excl. convenience fee)',
                                })
                                ->money('PHP'),
                            Infolists\Components\TextEntry::make('convenience_fee')
                                ->label('Convenience fee')
                                ->money('PHP'),
                            Infolists\Components\TextEntry::make('total_amount')
                                ->label('First payment')
                                ->hintIcon(
                                    'heroicon-o-information-circle',
                                    'First Paymongo checkout at enrollment. Should match the two amounts above. Later tuition is under Tuition & balance.',
                                )
                                ->hintColor('gray')
                                ->money('PHP')
                                ->weight('bold')
                                ->color('primary')
                                ->columnSpanFull(),
                            Infolists\Components\TextEntry::make('created_at')
                                ->label('Submitted')
                                ->dateTime('M j, Y g:i A', config('app.display_timezone'))
                                ->icon('heroicon-o-clock')
                                ->columnSpanFull(),
                        ])->columns(['default' => 1, 'md' => 3]),

                    Infolists\Components\Section::make('Tuition & balance')
                        ->icon('heroicon-o-calculator')
                        ->schema([
                            Infolists\Components\TextEntry::make('tuition_list_amount')
                                ->label('Regular list price')
                                ->hintIcon(
                                    'heroicon-o-information-circle',
                                    'Standard full tuition (published list price) after the early-bird window.',
                                )
                                ->hintColor('gray')
                                ->money('PHP')
                                ->placeholder('—'),
                            Infolists\Components\TextEntry::make('tuition_price_early')
                                ->label('Early-bird price')
                                ->hintIcon(
                                    'heroicon-o-information-circle',
                                    'Promotional tuition while the early-bird window is open.',
                                )
                                ->hintColor('gray')
                                ->money('PHP')
                                ->placeholder('—'),
                            Infolists\Components\TextEntry::make('tuition_early_deadline')
                                ->label('Early-bird discount ends')
                                ->hintIcon(
                                    'heroicon-o-information-circle',
                                    'Through this date (Asia/Manila), the system uses the early-bird tuition total to calculate the balance; starting the next day, it uses the regular list price. That is only which price tier applies—not a requirement that the student pays the full balance in one payment by this date.',
                                )
                                ->hintColor('gray')
                                ->date('M j, Y', config('app.display_timezone'))
                                ->placeholder('—'),
                            Infolists\Components\TextEntry::make('amount_paid_tuition')
                                ->label('Tuition paid')
                                ->money('PHP'),
                            Infolists\Components\TextEntry::make('computed_balance_tuition_due')
                                ->label('Remaining')
                                ->hintIcon(
                                    'heroicon-o-information-circle',
                                    'Outstanding tuition: early-bird total applies until the early-bird end date, then the regular list price; minus tuition paid to date. Convenience fees are per checkout.',
                                )
                                ->hintColor('gray')
                                ->money('PHP')
                                ->weight('bold')
                                ->color('danger')
                                ->columnSpan(['md' => 2]),
                        ])->columns(['default' => 1, 'md' => 3]),
                ])->columnSpan(1),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Reference #')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->icon('heroicon-m-hashtag')
                    ->color('primary')
                    ->alignment(Alignment::Start),

                Tables\Columns\TextColumn::make('student_name')
                    ->label('Student')
                    ->getStateUsing(fn ($record) => "{$record->first_name} {$record->surname}")
                    ->description(fn ($record) => $record->email)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->weight('bold')
                    ->alignment(Alignment::Start),

                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->description(fn ($record) => match ($record->payment_type) {
                        'full' => 'Full payment',
                        'downpayment' => 'Downpayment',
                        default => ucfirst((string) $record->payment_type),
                    })
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->alignment(Alignment::Start),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('First payment')
                    ->tooltip(function (Enrollment $record): string {
                        $bal = (int) $record->computed_balance_tuition_due;
                        $first = 'First Paymongo checkout when they enrolled. Tuition line plus one convenience fee. Not the same as remaining tuition.';
                        if ($bal > 0) {
                            return $first.' Remaining tuition: ₱'.number_format($bal).'.';
                        }

                        return $first.' No tuition balance left.';
                    })
                    ->money('PHP')
                    ->sortable()
                    ->alignment(Alignment::Start)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->alignment(Alignment::Start)
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'paid' => 'success',
                        'partially_paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Awaiting payment',
                        'partially_paid' => 'Partial — balance due',
                        'confirmed' => 'Enrolled (fully paid)',
                        'paid' => 'Enrolled',
                        default => strtoupper((string) $state),
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enrolled')
                    ->dateTime('M j, Y', config('app.display_timezone'))
                    ->tooltip(fn (Enrollment $record): string => $record->created_at
                        ->timezone(config('app.display_timezone'))
                        ->diffForHumans())
                    ->sortable()
                    ->alignment(Alignment::Start),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Awaiting payment',
                        'partially_paid' => 'Partial — balance due',
                        'confirmed' => 'Enrolled (fully paid)',
                        'paid' => 'Enrolled',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('payment_type')
                    ->label('Payment Type')
                    ->options([
                        'full' => 'Full Payment',
                        'downpayment' => 'Downpayment',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('copyPayBalanceLink')
                    ->label('Copy payment link')
                    ->icon('heroicon-m-clipboard-document')
                    ->iconButton()
                    ->color('warning')
                    ->tooltip('Copy payment link for student to pay remaining tuition')
                    ->visible(fn (Enrollment $record): bool => $record->payment_type === 'downpayment'
                        && $record->computed_balance_tuition_due > 0)
                    ->action(function (Enrollment $record, $livewire): void {
                        $payUrl = URL::temporarySignedRoute(
                            'enroll.balance',
                            now()->addYears(5),
                            ['reference_number' => $record->reference_number],
                        );

                        $livewire->js('window.navigator.clipboard.writeText('.Js::from($payUrl).')');

                        Notification::make()
                            ->title('Payment link copied')
                            ->body('Paste it into SMS, Messenger, Viber, or email for the student.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->tooltip('View Enrollment Record'),
            ])
            ->bulkActions([]);  // No bulk delete
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'view' => Pages\ViewEnrollment::route('/{record}'),
        ];
    }
}
