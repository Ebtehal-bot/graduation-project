<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Spatie\Permission\Models\Role as SpatieRole;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function getModelLabel(): string
    {
        return __('sidebar.model.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.model.users_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.users');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.nav_group.general_management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    TextInput::make('name')
                        ->label('اسم المستخدم')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),

                    TextInput::make('password')
                        ->label('كلمة المرور')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->dehydrated(fn ($state) => filled($state)) // لا يتم تحديث كلمة المرور إذا تركت فارغة
                        ->placeholder('اتركه فارغاً إذا لا تريد التغيير'),

                    Select::make('role')
                        ->label('نوع الصلاحية')
                        ->options([
                            'super_admin' => 'مدير النظام',
                            'supervisor' => 'المشرف الأكاديمي',
                            'employee' => 'موظف',
                            'sponsor' => 'الكفيل',
                        ])
                        ->required()
                        ->default('employee')
                        ->afterStateUpdated(function ($record, $state) {
                            if ($record) {
                                $record->syncRoles([$state]);
                            }
                        })
                        ->dehydrated(fn ($state) => filled($state)),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),

                BadgeColumn::make('role')
                    ->label('الصلاحية')
                    ->colors([
                        'danger' => 'super_admin',
                        'warning' => 'supervisor',
                        'success' => 'employee',
                        'primary' => 'sponsor',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'مدير النظام',
                        'supervisor' => 'المشرف الأكاديمي',
                        'employee' => 'موظف',
                        'sponsor' => 'الكفيل',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}