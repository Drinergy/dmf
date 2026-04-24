<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\AssistantUserResource\Pages;
use App\Models\User;
use App\Rules\AssistantEmailUsernameUnique;
use App\Support\PermissionCodes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Admin-only Filament resource for managing assistant user accounts.
 *
 * Only the primary administrator (admin@dmfdental.com) may list, create,
 * edit, or delete assistant accounts. Any access attempt by a non-admin user
 * is denied and logged.
 *
 * @author CKD
 *
 * @created 2026-04-24
 */
class AssistantUserResource extends Resource
{
    /**
     * Filament Fieldsets default to two columns; one child would only use the first half.
     *
     * @var array<string, int>
     */
    private const FIELDSET_SINGLE_COLUMN = [
        'default' => 1,
        'sm' => 1,
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
        '2xl' => 1,
    ];

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Assistants';

    protected static ?string $modelLabel = 'Assistant';

    protected static ?string $pluralModelLabel = 'Assistants';

    protected static ?int $navigationSort = 99;

    /**
     * Scope the resource table to assistant-role users only.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', UserRole::Assistant->value);
    }

    /**
     * Only the admin may see this resource in navigation and access it.
     */
    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user?->isAdmin()) {
            Log::warning('Unauthorized access attempt to AssistantUserResource', [
                'user_id' => $user?->id,
                'user_email' => $user?->email,
            ]);

            return false;
        }

        return true;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        $isEdit = $form->getRecord() !== null;
        $assistantEmailSuffix = '@dmfdental.com';

        return $form->schema([
            Forms\Components\Section::make('Account Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email username')
                        ->suffix($assistantEmailSuffix)
                        ->placeholder('e.g. maria.santos')
                        ->autocomplete('off')
                        ->required()
                        ->maxLength(64)
                        ->hintIcon('heroicon-o-information-circle')
                        ->hintIconTooltip('Enter the username only. The '.$assistantEmailSuffix.' domain is added for you.')
                        ->hintColor('gray')
                        ->formatStateUsing(static function (?string $state): ?string {
                            if (! filled($state)) {
                                return $state;
                            }

                            $state = trim($state);

                            return str_contains($state, '@')
                                ? explode('@', $state, 2)[0]
                                : $state;
                        })
                        ->dehydrateStateUsing(static function (?string $state) use ($assistantEmailSuffix): string {
                            if (! filled($state)) {
                                return '';
                            }

                            $state = trim($state);

                            if (str_contains($state, '@')) {
                                return strtolower($state);
                            }

                            return strtolower($state).strtolower($assistantEmailSuffix);
                        })
                        ->rules([
                            'string',
                            'max:64',
                            new AssistantEmailUsernameUnique($form, $assistantEmailSuffix),
                        ]),

                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->required(fn () => ! $isEdit)
                        ->minLength(8)
                        ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->helperText($isEdit ? 'Leave blank to keep current password.' : null),

                    Forms\Components\Select::make('role')
                        ->label('Role')
                        ->options(UserRole::assignableOptions())
                        ->default(UserRole::Assistant->value)
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Access & permissions')
                ->description('Choose what this assistant may view and do. Leave unchecked for no access to that area.')
                ->schema([
                    Forms\Components\Section::make('Enrollment')
                        ->schema([
                            Forms\Components\Fieldset::make('On the enrollment record (detail)')
                                ->columns(self::FIELDSET_SINGLE_COLUMN)
                                ->schema([
                                    Forms\Components\CheckboxList::make('perm_enrollment_sections')
                                        ->label('')
                                        ->options(PermissionCodes::bucketEnrollmentSections())
                                        ->bulkToggleable()
                                        ->columns([
                                            'default' => 1,
                                            'sm' => 2,
                                            'md' => 3,
                                            'lg' => 3,
                                            'xl' => 3,
                                            '2xl' => 3,
                                        ])
                                        ->gridDirection('row')
                                        ->extraAttributes([
                                            'class' => 'w-full !gap-x-5 sm:!gap-x-8 md:!gap-x-10 !gap-y-3',
                                        ]),
                                ]),
                            Forms\Components\Fieldset::make('List, links & payments')
                                ->columns(self::FIELDSET_SINGLE_COLUMN)
                                ->schema([
                                    Forms\Components\CheckboxList::make('perm_enrollment_tools')
                                        ->label('')
                                        ->options(PermissionCodes::bucketEnrollmentTools())
                                        ->bulkToggleable()
                                        ->columns([
                                            'default' => 1,
                                            'sm' => 2,
                                            'md' => 3,
                                            'lg' => 3,
                                            'xl' => 3,
                                            '2xl' => 3,
                                        ])
                                        ->gridDirection('row')
                                        ->extraAttributes([
                                            'class' => 'w-full !gap-x-5 sm:!gap-x-8 md:!gap-x-10 !gap-y-3',
                                        ]),
                                ]),
                        ])
                        ->columnSpanFull(),
                    Forms\Components\Section::make('Catalog')
                        ->description('Use View to show that area in the menu and open records. Create, Edit, and Delete add those actions.')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Fieldset::make('Categories')
                                        ->columns(self::FIELDSET_SINGLE_COLUMN)
                                        ->schema([
                                            Forms\Components\CheckboxList::make('perm_catalog_categories')
                                                ->label('')
                                                ->options(PermissionCodes::bucketCatalogOptions('categories'))
                                                ->bulkToggleable()
                                                ->columns([
                                                    'default' => 2,
                                                    'sm' => 4,
                                                    'md' => 4,
                                                    'lg' => 4,
                                                    'xl' => 4,
                                                    '2xl' => 4,
                                                ])
                                                ->gridDirection('row')
                                                ->extraAttributes([
                                                    'class' => 'w-full !gap-x-4 sm:!gap-x-8 md:!gap-x-12 !gap-y-2',
                                                ]),
                                        ]),
                                    Forms\Components\Fieldset::make('Packages')
                                        ->columns(self::FIELDSET_SINGLE_COLUMN)
                                        ->schema([
                                            Forms\Components\CheckboxList::make('perm_catalog_packages')
                                                ->label('')
                                                ->options(PermissionCodes::bucketCatalogOptions('packages'))
                                                ->bulkToggleable()
                                                ->columns([
                                                    'default' => 2,
                                                    'sm' => 4,
                                                    'md' => 4,
                                                    'lg' => 4,
                                                    'xl' => 4,
                                                    '2xl' => 4,
                                                ])
                                                ->gridDirection('row')
                                                ->extraAttributes([
                                                    'class' => 'w-full !gap-x-4 sm:!gap-x-8 md:!gap-x-12 !gap-y-2',
                                                ]),
                                        ]),
                                    Forms\Components\Fieldset::make('Programs')
                                        ->columns(self::FIELDSET_SINGLE_COLUMN)
                                        ->schema([
                                            Forms\Components\CheckboxList::make('perm_catalog_programs')
                                                ->label('')
                                                ->options(PermissionCodes::bucketCatalogOptions('programs'))
                                                ->bulkToggleable()
                                                ->columns([
                                                    'default' => 2,
                                                    'sm' => 4,
                                                    'md' => 4,
                                                    'lg' => 4,
                                                    'xl' => 4,
                                                    '2xl' => 4,
                                                ])
                                                ->gridDirection('row')
                                                ->extraAttributes([
                                                    'class' => 'w-full !gap-x-4 sm:!gap-x-8 md:!gap-x-12 !gap-y-2',
                                                ]),
                                        ]),
                                    Forms\Components\Fieldset::make('Schedules')
                                        ->columns(self::FIELDSET_SINGLE_COLUMN)
                                        ->schema([
                                            Forms\Components\CheckboxList::make('perm_catalog_schedules')
                                                ->label('')
                                                ->options(PermissionCodes::bucketCatalogOptions('schedules'))
                                                ->bulkToggleable()
                                                ->columns([
                                                    'default' => 2,
                                                    'sm' => 4,
                                                    'md' => 4,
                                                    'lg' => 4,
                                                    'xl' => 4,
                                                    '2xl' => 4,
                                                ])
                                                ->gridDirection('row')
                                                ->extraAttributes([
                                                    'class' => 'w-full !gap-x-4 sm:!gap-x-8 md:!gap-x-12 !gap-y-2',
                                                ]),
                                        ]),
                                ]),
                        ])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state instanceof UserRole ? $state->label() : ucfirst((string) $state))
                    ->color('info')
                    ->alignment(Alignment::Start),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('M j, Y', config('app.display_timezone'))
                    ->sortable()
                    ->alignment(Alignment::Start),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit assistant'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Remove assistant')
                    ->modalDescription(fn (User $record): string => sprintf(
                        'This permanently deletes the assistant account for %s and revokes their access to the admin panel. This action cannot be undone.',
                        $record->name,
                    )),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssistantUsers::route('/'),
            'create' => Pages\CreateAssistantUser::route('/create'),
            'edit' => Pages\EditAssistantUser::route('/{record}/edit'),
        ];
    }
}
