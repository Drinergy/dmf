<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

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
            Infolists\Components\Grid::make(['default' => 1, 'lg' => 3])->schema([
                // Left Side (2/3 width on large screens)
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
                                ->date('F j, Y')
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
                ])->columnSpan(['lg' => 2]),

                // Right Side (1/3 width on large screens)
                Infolists\Components\Group::make([
                    Infolists\Components\Section::make('Payment Summary')
                        ->icon('heroicon-o-credit-card')
                        ->schema([
                            Infolists\Components\TextEntry::make('status')
                                ->badge()
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                ->color(fn (string $state): string => match ($state) {
                                    'confirmed' => 'success',
                                    'paid'      => 'success',
                                    'pending'   => 'warning',
                                    'failed'    => 'danger',
                                    'cancelled' => 'danger',
                                    default     => 'gray',
                                })
                                ->formatStateUsing(fn ($state) => match ($state) {
                                    'pending'   => 'Awaiting Payment',
                                    'confirmed' => 'Enrolled',
                                    'paid'      => 'Enrolled',
                                    default     => strtoupper((string) $state),
                                }),
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
                                ->label('Payment Plan')
                                ->formatStateUsing(fn ($state) => $state === 'full' ? 'Full Payment' : 'Downpayment')
                                ->badge()
                                ->color('gray'),
                            Infolists\Components\TextEntry::make('base_amount')
                                ->label('Base Price')
                                ->money('PHP'),
                            Infolists\Components\TextEntry::make('convenience_fee')
                                ->label('Convenience Fee')
                                ->money('PHP'),
                            Infolists\Components\TextEntry::make('total_amount')
                                ->label('Total Due')
                                ->money('PHP')
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                ->weight('bold')
                                ->color('primary'),
                        ])->columns(1),

                    Infolists\Components\Section::make('System Activity')
                        ->icon('heroicon-o-clock')
                        ->schema([
                            Infolists\Components\TextEntry::make('created_at')
                                ->label('Submitted At')
                                ->dateTime('M j, Y g:i A'),
                        ])->columns(1),
                ])->columnSpan(['lg' => 1]),
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
                    ->color('primary'),

                Tables\Columns\TextColumn::make('student_name')
                    ->label('Student')
                    ->getStateUsing(fn ($record) => "{$record->first_name} {$record->surname}")
                    ->description(fn ($record) => $record->email)
                    ->searchable(query: function (\Illuminate\Database\Eloquent\Builder $query, string $search): \Illuminate\Database\Eloquent\Builder {
                        return $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program & Payment')
                    ->description(fn ($record) => match($record->payment_type) {
                        'full' => 'Full Payment',
                        'downpayment' => 'Downpayment',
                        default => ucfirst($record->payment_type),
                    })
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable()
                    ->alignment(\Filament\Support\Enums\Alignment::End)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'paid'      => 'success',
                        'pending'   => 'warning',
                        'failed'    => 'danger',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'   => 'Awaiting Payment',
                        'confirmed' => 'Enrolled',
                        'paid'      => 'Enrolled',
                        default     => strtoupper((string) $state),
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Enrolled')
                    ->dateTime('M j, Y')
                    ->description(fn ($record) => $record->created_at->diffForHumans())
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Awaiting Payment',
                        'confirmed' => 'Enrolled',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('payment_type')
                    ->label('Payment Type')
                    ->options([
                        'full'        => 'Full Payment',
                        'downpayment' => 'Downpayment',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->tooltip('View Enrollment Record'),
            ])
            ->bulkActions([]);  // No bulk delete
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEnrollments::route('/'),
            'view'   => Pages\ViewEnrollment::route('/{record}'),
        ];
    }
}
