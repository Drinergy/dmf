<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
use App\Models\Category;
use App\Models\Program;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Programs';
    protected static ?int $navigationSort = 20;
    protected static bool $shouldRegisterNavigation = true;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Program')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->options(fn () => Category::query()->orderBy('sort_order')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\TextInput::make('category')
                        ->helperText('Legacy category string (kept for backward compatibility).')
                        ->maxLength(255)
                        ->required(),

                    Forms\Components\TextInput::make('tag')
                        ->maxLength(255)
                        ->nullable(),

                    Forms\Components\Toggle::make('is_active')
                        ->default(true),

                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->minValue(0),
                ])->columns(2),

            Forms\Components\Section::make('Pricing')
                ->schema([
                    Forms\Components\TextInput::make('price_full')
                        ->numeric()
                        ->required()
                        ->minValue(0),

                    Forms\Components\TextInput::make('price_dp')
                        ->label('Downpayment Price')
                        ->numeric()
                        ->required()
                        ->minValue(0),

                    Forms\Components\TextInput::make('price_early')
                        ->label('Early Bird Price')
                        ->numeric()
                        ->nullable()
                        ->minValue(0),

                    Forms\Components\DatePicker::make('early_deadline')
                        ->nullable(),

                    Forms\Components\Textarea::make('early_bird_label')
                        ->rows(2)
                        ->nullable(),
                ])->columns(2),

            Forms\Components\Section::make('Inclusions')
                ->schema([
                    Forms\Components\Repeater::make('inclusions')
                        ->schema([
                            Forms\Components\TextInput::make('value')
                                ->label('Inclusion')
                                ->required(),
                        ])
                        ->defaultItems(0)
                        ->addActionLabel('Add inclusion')
                        ->dehydrated(true)
                        ->mutateDehydratedStateUsing(function ($state) {
                            $items = is_array($state) ? $state : [];
                            return array_values(array_filter(array_map(
                                fn ($row) => is_array($row) ? ($row['value'] ?? null) : null,
                                $items
                            )));
                        })
                        ->mutateStateUsing(function ($state) {
                            if (!is_array($state)) {
                                return [];
                            }

                            return array_map(fn ($value) => ['value' => $value], $state);
                        }),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('category_label')
                    ->label('Category')
                    ->getStateUsing(fn (Program $record) => $record->category_label)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('category', $direction)->orderBy('name', 'asc');
                    }),

                Tables\Columns\TextColumn::make('price_full')
                    ->label('Full')
                    ->money('PHP')
                    ->sortable()
                    ->alignment(\Filament\Support\Enums\Alignment::End),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit'   => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}

