<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Models\Category;
use App\Models\Package;
use App\Models\Program;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Packages';

    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Package')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('tag')
                            ->maxLength(255)
                            ->nullable(),
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
                        Forms\Components\TextInput::make('price_early')
                            ->label('Early Bird Price')
                            ->numeric()
                            ->nullable()
                            ->minValue(0),
                        Forms\Components\DatePicker::make('early_deadline')
                            ->label('Early-bird discount ends')
                            ->nullable(),
                        Forms\Components\Textarea::make('early_bird_label')
                            ->rows(2)
                            ->nullable(),
                    ])->columns(2),
                Forms\Components\Section::make('Included Programs')
                    ->schema([
                        Forms\Components\Select::make('programs')
                            ->label('Programs')
                            ->relationship('programs', 'name')
                            ->options(fn () => Program::query()->orderBy('sort_order')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->multiple()
                            ->required(),
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
                Tables\Columns\TextColumn::make('price_full')
                    ->label('Full')
                    ->money('PHP')
                    ->sortable()
                    ->alignment(Alignment::End),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
