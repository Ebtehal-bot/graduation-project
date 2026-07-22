<?php

namespace App\Filament\Resources;

use App\Filament\Pages\OrphanReports;
use App\Filament\Resources\ThankYouMessageResource\Pages;
use App\Models\Sponsorship;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class ThankYouMessageResource extends Resource
{
    protected static ?string $model = Sponsorship::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'reports-and-forms';

    public static function getModelLabel(): string
    {
        return __('sidebar.model.report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.model.reports_center');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.reports_center');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.nav_group.reports');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('orphan.photo')
                    ->label('صورة اليتيم')
                    ->circular(),

                Tables\Columns\TextColumn::make('orphan.name')
                    ->label('اسم اليتيم')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sponsor.name')
                    ->label('اسم الكفيل')
                    ->searchable(),

                Tables\Columns\TextColumn::make('monthly_amount')
                    ->label('مبلغ الكفالة')
                    ->money('YER'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'waiting',
                    ]),
            ])
            ->headerActions([
                Action::make('advanced_reports')
                    ->label(__('sidebar.advanced_reports'))
                    ->icon('heroicon-o-document-report')
                    ->color('primary')
                    ->url(fn () => OrphanReports::getUrl())
                    ->openUrlInNewTab(false),
                Action::make('general_report')
                    ->label('التقرير العام')
                    ->icon('heroicon-o-clipboard-list') 
                    ->color('danger')
                    ->url(fn () => route('orphans.general_pdf'))
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Action::make('print_report')
                    ->label('تقرير')
                    ->icon('heroicon-o-printer') 
                    ->color('success')
                    ->url(fn ($record) => route('orphans.print', ['record' => $record->orphan_id]))
                    ->openUrlInNewTab(),

                // تصدير إكسل لسجل واحد ليُحفظ على الجهاز
                Action::make('export_excel_single')
                    ->label('حفظ إكسل')
                    ->icon('heroicon-o-table') 
                    ->color('warning')
                    ->url(fn ($record) => route('orphans.export_excel', ['records' => [$record->id]])),
            ])
            ->bulkActions([
                // تصدير إكسل جماعي ليُحفظ على الجهاز كملف مستقل
                BulkAction::make('export_excel_bulk')
                    ->label('تصدير إكسل للمختار')
                    ->icon('heroicon-o-download') 
                    ->color('warning')
                    ->action(fn (Collection $records) => redirect()->route('orphans.export_excel', ['records' => $records->pluck('id')->toArray()])),
                
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThankYouMessages::route('/'),
        ];
    }
}