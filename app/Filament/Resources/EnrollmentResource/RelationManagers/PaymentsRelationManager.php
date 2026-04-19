<?php

declare(strict_types=1);

namespace App\Filament\Resources\EnrollmentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Payments';

    public static function canCreateForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('enrollment'))
            ->recordTitleAttribute('purpose')
            ->columns([
                Tables\Columns\TextColumn::make('enrollment.reference_number')
                    ->label('Reference #')
                    ->fontFamily('mono')
                    ->copyable(),
                Tables\Columns\TextColumn::make('purpose')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'initial' => 'First checkout (enrollment)',
                        'balance' => 'Balance tuition',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'initial' => 'warning',
                        'balance' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_method')->placeholder('—'),
                Tables\Columns\TextColumn::make('tuition_amount')
                    ->label('Tuition (PHP)')
                    ->money('PHP'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('paid_at')->dateTime()->sortable(),
            ])
            ->defaultSort('paid_at', 'desc')
            ->paginated(false);
    }
}
