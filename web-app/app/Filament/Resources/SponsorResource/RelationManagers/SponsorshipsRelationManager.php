<?php

namespace App\Filament\Resources\SponsorResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class SponsorshipsRelationManager extends RelationManager
{
    protected static string $relationship = 'sponsorships';

    // تسمية التبويب في صفحة الكفيل
    protected static ?string $title = 'الأيتام المكفولين';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // اختيار اليتيم من قائمة
                Forms\Components\Select::make('orphan_id')
                    ->label('اليتيم')
                    ->relationship('orphan', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('monthly_amount')
                    ->label('المبلغ الشهري')
                    ->numeric()
                    ->prefix('ر.ي')
                    ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->label('تاريخ بدء الكفالة')
                    ->default(now())
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('حالة الكفالة')
                    ->options([
                        'active' => 'نشطة',
                        'stopped' => 'متوقفة',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('orphan.name')
                    ->label('اسم اليتيم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('monthly_amount')
                    ->label('المبلغ')
                    ->money('yer')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'stopped',
                    ]),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ البدء')
                    ->date(),
            ])
            ->headerActions([
                // هذا الزر هو الذي سيسمح بإضافة اليتيم رقم 1 و 2 حتى 10
                Tables\Actions\CreateAction::make()
                    ->label('إضافة كفالة ليتيم جديد'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }    
}