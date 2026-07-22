<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorResource\Pages;
use App\Filament\Resources\SponsorResource\RelationManagers; 
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class SponsorResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $slug = 'sponsors';

    public static function getModelLabel(): string
    {
        return __('sidebar.model.sponsor');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.model.sponsors_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.sponsors');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الكفيل')
                            ->placeholder('أدخل الاسم الكامل')
                            ->required(),

                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->placeholder('مثلاً: 7xxxxxxxx')
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->placeholder('example@mail.com')
                            ->required(),

                        Forms\Components\TextInput::make('address')
                            ->label('العنوان')
                            ->placeholder('المدينة - الشارع'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الكفيل')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->icon('heroicon-s-phone')
                    ->copyable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->icon('heroicon-s-mail'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الانضمام')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('sponsor_report')
                    ->label('طباعة تقرير الكفيل')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->url(fn (User $record): string => $record->sponsor ? route('sponsor.report', $record->sponsor) : '#')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SponsorshipsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSponsors::route('/'),
            'create' => Pages\CreateSponsor::route('/create'),
            'edit' => Pages\EditSponsor::route('/{record}/edit'),
        ];
    }
}