<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrphanResource\Pages;
use App\Models\Orphan;
use App\Models\Setting;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class OrphanResource extends Resource
{
    protected static ?string $model = Orphan::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getModelLabel(): string
    {
        return __('sidebar.model.orphan');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.model.orphans_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.orphans');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('إستمارة بيانات كفالة اليتيم')
                    ->tabs([
                        // 1. بيانات اليتيم الأساسية
                        Forms\Components\Tabs\Tab::make('بيانات اليتيم')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->label('الصورة الشخصية')
                                    ->image()
                                    ->disk('public')
                                    ->directory('orphans-photos')
                                    ->required(), 
                                
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('اسم اليتيم حسب شهادة الميلاد')
                                        ->required(),
                                    Forms\Components\TextInput::make('file_number')
                                        ->label('رقم الملف')
                                        ->required(),
                                ]),
                                
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\Select::make('gender')
                                        ->label('الجنس')
                                        ->options(['male' => 'ذكر', 'female' => 'أنثى'])
                                        ->required(),
                                    Forms\Components\TextInput::make('religion')
                                        ->label('الديانة')
                                        ->default('الإسلام'),
                                    Forms\Components\TextInput::make('nationality')
                                        ->label('الجنسية')
                                        ->default('يمني'),
                                ]),
                                
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\DatePicker::make('birth_date')
                                        ->label('تاريخ الميلاد')
                                        ->required(),
                                    Forms\Components\TextInput::make('birth_place')
                                        ->label('محل الميلاد'),
                                ]),
                                
                                Forms\Components\Fieldset::make('عنوان الإقامة الحالي')
                                    ->schema([
                                        Forms\Components\TextInput::make('address_gov')->label('المحافظة'),
                                        Forms\Components\TextInput::make('address_dist')->label('المديرية'),
                                        Forms\Components\TextInput::make('address_village')->label('العزلة / القرية'),
                                    ])->columns(3),

                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Select::make('status')
                                        ->label('حالة اليتيم')
                                        ->options([
                                            'active' => 'نشط',
                                            'waiting' => 'بانتظار كفيل',
                                        ])->default('waiting'),
                                    Forms\Components\Select::make('branch_id')
                                        ->label('الفرع')
                                        ->relationship('branch', 'name')
                                        ->required(),
                                ]),
                            ]),

                        // 2. التعليم والصحة
                        Forms\Components\Tabs\Tab::make('التعليم والصحة')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Select::make('education_status')
                                        ->label('الحالة التعليمية')
                                        ->options(['studying' => 'يدرس', 'stopped' => 'منقطع', 'not_enrolled' => 'غير ملتحق']),
                                    Forms\Components\TextInput::make('school_name')->label('اسم المدرسة'),
                                    Forms\Components\TextInput::make('academic_level')->label('الصف الدراسي'),
                                    Forms\Components\TextInput::make('school_phone')->label('تلفون المدرسة'),
                                ]),
                                Forms\Components\Textarea::make('health_status')
                                    ->label('الحالة الصحية')
                                    ->rows(2),
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('talents')->label('المواهب'),
                                    Forms\Components\TextInput::make('quran_memorization')->label('حفظ القرآن'),
                                ]),
                                
                                Forms\Components\FileUpload::make('academic_result')
                                    ->label('صورة النتيجة المدرسية الجديدة')
                                    ->image()
                                    ->disk('public')
                                    ->directory('orphans-results')
                                    ->columnSpanFull()
                                    ->nullable(),

                                // حقل رسالة الشكر بخط اليتيم
                                Forms\Components\FileUpload::make('thank_you_letter')
                                    ->label('رسالة شكر للكافل بخط اليتيم')
                                    ->image()
                                    ->disk('public')
                                    ->directory('orphans-thanks')
                                    ->columnSpanFull()
                                    ->nullable(),
                            ]),

                        // 3. بيانات الأسرة
                        Forms\Components\Tabs\Tab::make('بيانات الأسرة')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\Fieldset::make('بيانات والد اليتيم')
                                    ->schema([
                                        Forms\Components\TextInput::make('father_death_cause')->label('سبب الوفاة'),
                                        Forms\Components\DatePicker::make('father_death_date')->label('تاريخ الوفاة'),
                                        Forms\Components\TextInput::make('father_job_before')->label('عمله قبل الوفاة'),
                                    ])->columns(3),
                                
                                Forms\Components\Fieldset::make('بيانات والدة اليتيم')
                                    ->schema([
                                        Forms\Components\TextInput::make('mother_name')->label('اسم الأم'),
                                        Forms\Components\Select::make('mother_status')
                                            ->label('حالة الأم')
                                            ->options(['alive' => 'على قيد الحياة', 'deceased' => 'متوفية']),
                                        Forms\Components\TextInput::make('mother_job')->label('عمل الأم'),
                                        Forms\Components\TextInput::make('mother_income')->label('دخل الأم')->numeric(),
                                    ])->columns(2),
                            ]),

                        // 4. المعيل 
                        Forms\Components\Tabs\Tab::make('المعيل ')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('guardian_name')->label('اسم المعيل')->required(),
                                    Forms\Components\TextInput::make('guardian_relation')->label('صلة القرابة'),
                                    Forms\Components\TextInput::make('guardian_card_id')->label('رقم البطاقة'),
                                    Forms\Components\TextInput::make('guardian_phone')->label('رقم التلفون'),
                                ]),
                            ]),

                        // 5. المرفقات الرسمية
                        Forms\Components\Tabs\Tab::make('المرفقات الرسمية')
                            ->icon('heroicon-o-paper-clip')
                            ->schema([
                                Forms\Components\Repeater::make('attachments')
                                    ->label('رفع المستندات')
                                    ->relationship('attachments')
                                    ->schema([
                                        Forms\Components\Select::make('document_type')
                                            ->label('نوع المستند')
                                            ->options([
                                                'birth_certificate' => 'شهادة ميلاد',
                                                'death_certificate' => 'شهادة وفاة الوالد',
                                                'id_card' => 'هوية المعيل',
                                                'other' => 'أخرى',
                                            ]),
                                        Forms\Components\FileUpload::make('file_path')
                                            ->label('الملف')
                                            ->directory('orphan-attachments')
                                            ->multiple(false)
                                            ->required(),
                                    ])->columns(3)->columnSpanFull(),
                            ]),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')->label('الصورة')->circular(),
                Tables\Columns\TextColumn::make('name')->label('اسم اليتيم')->searchable(),
                
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('العمر')
                    ->formatStateUsing(fn ($state): string => Carbon::parse($state)->age . ' سنوات')
                    ->sortable(),

                Tables\Columns\TextColumn::make('academic_level')->label('الصف'),
                Tables\Columns\TextColumn::make('quran_memorization')->label('حفظ القرآن'),
                Tables\Columns\TextColumn::make('guardian_name')->label('المعيل'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'waiting',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('documents')
                    ->label('المستندات')
                    ->icon('heroicon-o-paper-clip')
                    ->color('warning')
                    ->action(fn () => null)
                    ->modalHeading(fn (Orphan $record) => 'مستندات: ' . $record->name)
                    ->modalButton('إغلاق')
                    ->modalContent(function (Orphan $record) {
                        return view('filament.orphan-documents', ['orphan' => $record]);
                    }),

                Tables\Actions\Action::make('print_form')
                    ->label('طباعة تقرير')
                    ->icon('heroicon-s-document-text')
                    ->color('primary')
                    ->url(fn (Orphan $record) => route('orphans.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('print_allowance_statement')
                    ->label('كشف صرف مستحقات الأيتام المطور')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('months_count')
                            ->label('حدد عدد أشهر الصرف')
                            ->options([
                                1 => 'شهر واحد (1)',
                                2 => 'شهرين (2)',
                                3 => 'ثلاثة أشهر (3)',
                                4 => 'أربعة أشهر (4)',
                                6 => 'ستة أشهر (6)',
                                12 => 'سنة كاملة (12)',
                            ])
                            ->default(1)
                            ->required(),
                            
                        Forms\Components\TextInput::make('notes')
                            ->label('بيان أو ملاحظات الكشف')
                            ->placeholder('مثال: صرف مستحقات الربع الأول لعام 2026')
                    ])
                    ->action(function (Collection $records, array $data) {
                        $monthsCount = (int) $data['months_count'];
                        $notes = $data['notes'] ?? '';
                        $systemName = Setting::getValue('site_name') ?? Setting::getValue('org_name') ?? 'منصة كفيل لرعاية وكفالة الأيتام';
                        $orgName = Setting::getValue('org_name') ?? 'منصة كفيل';
                        $siteLogo = Setting::getValue('site_logo') ?? '';

                        $htmlContent = view('reports.allowance_statement', [
                            'orphans' => $records,
                            'monthsCount' => $monthsCount,
                            'notes' => $notes,
                            'systemName' => $systemName,
                            'orgName' => $orgName,
                            'systemLogo' => $siteLogo,
                        ])->render();

                        $fileName = 'كشف_صرف_مستحقات_' . date('Y-m-d') . '.html';

                        return response()->streamDownload(function () use ($htmlContent) {
                            echo $htmlContent;
                        }, $fileName);
                    })
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrphans::route('/'),
            'create' => Pages\CreateOrphan::route('/create'),
            'view' => Pages\ViewOrphan::route('/{record}'),
            'edit' => Pages\EditOrphan::route('/{record}/edit'),
        ];
    }
}