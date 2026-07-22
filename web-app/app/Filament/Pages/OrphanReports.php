<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use App\Models\Branch;

class OrphanReports extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.pages.orphan-reports';
    protected static ?string $title = 'مركز التقارير المتقدمة وكشوفات الصرف الذكية';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    // المتغيرات البرمجية الأساسية
    public $report_type = 'distribution';
    public $report_title = 'كشف صرف مستحقات الأيتام';
    public $branch_id = null;
    public $sponsorship_status = 'all';
    public $gender = 'all';
    public $columns = ['name', 'amount', 'signature'];

    public $months_count = 1;
    public $notes = '';
    public $min_amount = null;
    public $generate_differences = false;

    public function mount(): void
    {
        $this->form->fill([
            'report_type' => $this->report_type,
            'report_title' => $this->report_title,
            'branch_id' => $this->branch_id,
            'sponsorship_status' => $this->sponsorship_status,
            'gender' => $this->gender,
            'columns' => $this->columns,
            'months_count' => $this->months_count,
            'notes' => $this->notes,
            'min_amount' => $this->min_amount,
            'generate_differences' => $this->generate_differences,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Card::make()
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\Select::make('report_type')
                                ->label('نوع التقرير المطلوب استخراجه')
                                ->options([
                                    'distribution' => '📋 كشف صرف مستحقات (مع الخلاصة المالية والتواقيع)',
                                    'cards' => '🪪 بطاقات تعريفية وكروت عضوية للأيتام (ID Cards)',
                                    'expiry' => '⚠️ تقرير إنذار الكفالات الموشكة على الانتهاء أو المتأخرة',
                                    'branches_performance' => '📊 تقرير الأداء المقارن بين كل الفروع والمحافظات',
                                ])
                                ->reactive()
                                ->required(),

                            Forms\Components\TextInput::make('report_title')
                                ->label('عنوان التقرير (الترويسة العلوية)')
                                ->required(),

                            Forms\Components\Select::make('branch_id')
                                ->label('الفرع / المحافظة المستهدفة')
                                ->placeholder('كل الفروع والمحافظات')
                                ->options(Branch::all()->pluck('name', 'id'))
                                ->searchable()
                                ->hidden(fn ($get) => $get('report_type') === 'branches_performance'),

                            Forms\Components\TextInput::make('min_amount')
                                ->label('مبلغ الكفالة (قيمة الصرف لكل يتيم)')
                                ->placeholder('مثال: 50000')
                                ->numeric()
                                ->suffix('ر.ي')
                                ->helperText('سيتم تطبيق هذا المبلغ على جميع الأيتام المتضمنين في الكشف لكافة حالات الكفالة')
                                ->reactive()
                                ->hidden(fn ($get) => $get('report_type') === 'branches_performance'),

                            Forms\Components\Checkbox::make('generate_differences')
                                ->label('كشف الفروقات الإضافي')
                                ->helperText('عرض كشف منفصل للمبلغ المتبقي من الكفالة للأيتام ذوي الكفالات الأعلى')
                                ->visible(fn ($get) => !empty($get('min_amount')) && $get('sponsorship_status') !== 'inactive')
                                ->hidden(fn ($get) => $get('report_type') === 'branches_performance'),
                        ]),

                    Forms\Components\Card::make()
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('sponsorship_status')
                                        ->label('حالة الكفالة')
                                        ->options([
                                            'all' => 'الكل (مكفول وغير مكفول)',
                                            'active' => 'المكفولين فقط',
                                            'inactive' => 'غير المكفولين فقط',
                                        ])
                                        ->hidden(fn ($get) => in_array($get('report_type'), ['expiry', 'branches_performance'])),

                                    Forms\Components\Select::make('gender')
                                        ->label('جنس اليتيم')
                                        ->options([
                                            'all' => 'الكل (ذكور وإناث)',
                                            'male' => 'ذكور فقط',
                                            'female' => 'إناث فقط',
                                        ])
                                        ->hidden(fn ($get) => in_array($get('report_type'), ['expiry', 'branches_performance'])),
                                ]),

                            // الإضافة الجديدة: حقول إعدادات الصرف الذكي (تظهر فقط عند اختيار كشف الصرف)
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('months_count')
                                        ->label('عدد أشهر الصرف المالي')
                                        ->options([
                                            1 => 'شهر واحد (1)',
                                            2 => 'شهرين (2)',
                                            3 => 'ثلاثة أشهر (3)',
                                            4 => 'أربعة أشهر (4)',
                                            6 => 'ستة أشهر (6)',
                                            12 => 'سنة كاملة (12)',
                                        ])
                                        ->required()
                                        ->hidden(fn ($get) => $get('report_type') !== 'distribution'),

                                    Forms\Components\TextInput::make('notes')
                                        ->label('بيان أو ملاحظات الصرف')
                                        ->placeholder('مثال: صرف الربع الأول لعام 2026')
                                        ->hidden(fn ($get) => $get('report_type') !== 'distribution'),
                                ]),

                            Forms\Components\CheckboxList::make('columns')
                                ->label('الأعمدة المضمنة في الجدول (خاص بكشوفات الصرف)')
                                ->options([
                                    'name' => 'اسم اليتيم',
                                    'sponsor' => 'اسم الكفيل',
                                    'amount' => 'مبلغ الكفالة',
                                    'signature' => 'خانة التوقيع والبصمة',
                                    'notes' => 'خانة ملاحظات فارغة',
                                ])
                                ->columns(2)
                                ->hidden(fn ($get) => $get('report_type') !== 'distribution'),
                        ]),
                ])
        ];
    }

    public function generateReport()
    {
        $data = $this->form->getState();

        return redirect()->route('reports.dynamic.print', [
            'type' => $data['report_type'],
            'title' => $data['report_title'],
            'branch' => $data['branch_id'] ?? 'all',
            'status' => $data['sponsorship_status'] ?? 'all',
            'gender' => $data['gender'] ?? 'all',
            'columns' => implode(',', $data['columns'] ?? []),
            'months_count' => $data['months_count'] ?? 1,
            'notes' => $data['notes'] ?? '',
            'min_amount' => $data['min_amount'] ?? '',
            'generate_differences' => $data['generate_differences'] ?? false,
        ]);
    }
}