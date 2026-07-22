<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Sponsorship;
use App\Models\Orphan;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-cash';

    public static function getModelLabel(): string
    {
        return __('sidebar.model.payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.model.payments_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.payments');
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
                        Forms\Components\Select::make('sponsorship_id')
                            ->label('اختر الكفالة (اليتيم - الكفيل)')
                            ->options(fn () => Sponsorship::with(['orphan', 'sponsor'])
                                ->get()
                                ->mapWithKeys(fn ($s) => [
                                    $s->id => "يتيم: " . ($s->orphan->name ?? 'غير معروف') . " | كفيل: " . ($s->sponsor->name ?? 'غير معروف')
                                ])
                            )
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ المدفوع')
                            ->numeric()
                            ->prefix('ر.ي')
                            ->required(),

                        Forms\Components\DatePicker::make('date')
                            ->label('تاريخ الدفع')
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('payment_status')
                            ->label('حالة الدفع')
                            ->options([
                                'paid' => 'تم الدفع',
                                'pending' => 'معلق',
                            ])
                            ->default('paid')
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // الترقيم المتسلسل
                Tables\Columns\TextColumn::make('index')
                    ->label('#')
                    ->getStateUsing(static function ($rowLoop, $livewire): string {
                        return (string) ($rowLoop->iteration + ($livewire->tableRecordsPerPage * ($livewire->page - 1)));
                    }),
                
                Tables\Columns\TextColumn::make('sponsorship.orphan.name')
                    ->label('اسم اليتيم')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ المدفوع')
                    ->money('YER', true)
                    ->sortable(),

                // التحصيل السنوي
                Tables\Columns\TextColumn::make('yearly_target')
                    ->label('التحصيل السنوي')
                    ->getStateUsing(function ($record) {
                        $monthlyAmount = $record->sponsorship->monthly_amount ?? 0;
                        return number_format($monthlyAmount * 12) . ' ر.ي';
                    })
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('date')
                    ->label('تاريخ الدفع')
                    ->date('Y-m-d')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'pending',
                    ]),
            ])
            ->headerActions([
                // زر الإحصائية السريعة
                Action::make('yearly_stat')
                    ->label(function() {
                        $filters = request()->query('tableFilters');
                        $year = $filters['year_report']['year'] ?? null;
                        
                        $query = Payment::where('payment_status', 'paid');
                        
                        if ($year) {
                            $query->whereYear('date', $year);
                            $label = "إجمالي تحصيل سنة $year";
                        } else {
                            $label = "إجمالي التحصيل العام";
                        }

                        $total = $query->sum('amount');
                        return $label . ": " . number_format($total) . " ر.ي";
                    })
                    ->color('warning')
                    ->icon('heroicon-o-calculator')
                    ->button()
                    ->disabled(),

                    // 2. زر التقرير الجماعي (هذا هو الزر الجديد)
                Action::make('print_all_payments')
                    ->label('التقرير الجماعي للمدفوعات')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->button()
                    ->url(fn (): string => route('payments.general_report'))
                    ->openUrlInNewTab(),
            ])
            
            ->filters([
                Tables\Filters\Filter::make('year_report')
                    ->form([
                        Forms\Components\Select::make('year')
                            ->label('تقرير سنة محددة')
                            ->options([
                                '2024' => '2024',
                                '2025' => '2025',
                                '2026' => '2026',
                                '2027' => '2027',
                            ])
                            ->placeholder('عرض كل التواريخ') 
                            ->default(null), 
                    ])
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['year'],
                        fn ($q, $year) => $q->whereYear('date', $year)
                    )),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // --- التعديل الجذري هنا لحل مشكلة سجل المدفوعات ---
                Action::make('printOrphanReport')
                    ->label('تقرير تفصيلي')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Payment $record) => route('orphan.yearly.report', [
                        'record' => $record->sponsorship->orphan_id, // تم تغيير المفتاح ليتطابق مع ملف web.php
                    ]))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PaymentResource\Widgets\PaymentStats::class,
        ];
    }
}