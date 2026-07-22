<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorshipResource\Pages;
use App\Models\Sponsorship;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class SponsorshipResource extends Resource
{
    protected static ?string $model = Sponsorship::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand';

    protected static ?string $slug = 'sponsorship-records';

    public static function getModelLabel(): string
    {
        return __('sidebar.model.sponsorship');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.model.sponsorships_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.sponsorships');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.nav_group.financial_management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('orphan_id')
                                    ->label('اليتيم المستهدف')
                                    ->relationship('orphan', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                
                                Forms\Components\Select::make('sponsor_id')
                                    ->label('الكفيل')
                                    ->relationship('sponsor', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('monthly_amount')
                                    ->label('المبلغ الشهري')
                                    ->numeric()
                                    ->prefix('ر.ي')
                                    ->required(),

                                // إضافة نوع الكفالة هنا
                                Forms\Components\Select::make('sponsorship_type')
                                    ->label('نوع الكفالة')
                                    ->options([
                                        'financial' => 'كفالة مالية',
                                        'educational' => 'كفالة دراسية',
                                        'medical' => 'كفالة علاجية',
                                    ])
                                    ->default('financial')
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->label('حالة الكفالة')
                                    ->options([
                                        'active' => 'نشطة',
                                        'stopped' => 'متوقفة',
                                    ])
                                    ->default('active')
                                    ->required(),

                                Forms\Components\DatePicker::make('start_date')
                                    ->label('تاريخ البدء')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('تاريخ النهاية'),
                            ]),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('orphan.photo')
                    ->label('الصورة')
                    ->circular()
                    ->default('images/default-avatar.png'),

                Tables\Columns\TextColumn::make('orphan.name')
                    ->label('اليتيم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sponsor.name')
                    ->label('الكفيل')
                    ->searchable()
                    ->sortable(),

                // إضافة عرض نوع الكفالة هنا
                Tables\Columns\BadgeColumn::make('sponsorship_type')
                    ->label('نوع الكفالة')
                    ->colors([
                        'primary' => 'financial',
                        'warning' => 'educational',
                        'success' => 'medical',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'financial' => 'كفالة مالية',
                        'educational' => 'كفالة دراسية',
                        'medical' => 'كفالة علاجية',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('monthly_amount')
                    ->label('المبلغ')
                    ->money('yer')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'stopped',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ البدء')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('تصفية حسب الحالة')
                    ->options([
                        'active' => 'نشطة',
                        'stopped' => 'متوقفة',
                    ]),
                
                Tables\Filters\SelectFilter::make('sponsorship_type')
                    ->label('تصفية حسب النوع')
                    ->options([
                        'financial' => 'كفالة مالية',
                        'educational' => 'كفالة دراسية',
                        'medical' => 'كفالة علاجية',
                    ]),
                
                Tables\Filters\SelectFilter::make('sponsor_id')
                    ->label('تصفية حسب الكفيل')
                    ->relationship('sponsor', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSponsorships::route('/'),
            'create' => Pages\CreateSponsorship::route('/create'),
            'edit' => Pages\EditSponsorship::route('/{record}/edit'),
        ];
    }
}