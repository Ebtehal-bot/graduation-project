<?php

namespace App\Filament\Pages;

use App\Helpers\SettingsHelper;
use App\Models\Setting;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\HtmlString;

class SettingsPage extends Page implements HasForms
{
    public static function canView(): bool
    {
        if ($user = auth()->user()) {
            return $user->hasRole('super_admin');
        }
        return false;
    }
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $slug = 'settings';

    public static function getNavigationLabel(): string
    {
        return __('sidebar.settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.nav_group.system_management');
    }

    protected static string $view = 'filament.pages.settings';

    public array $data = [];

    public string $selectedBackupFile = '';

    private array $credentialKeys = [
        'backup_external_disk_path',
    ];

    public function mount(): void
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        foreach ($this->credentialKeys as $key) {
            if (isset($settings[$key]) && !empty($settings[$key])) {
                try {
                    $settings[$key] = Crypt::decryptString($settings[$key]);
                } catch (\Exception $e) {
                    //
                }
            }
        }

        $this->form->fill($settings);
    }

    protected function getFormSchema(): array
    {
        return [
            Tabs::make('settings')
                ->tabs([
                    $this->generalTab(),
                    $this->organizationTab(),
                    $this->notificationsTab(),
                    $this->applicationTab(),
                    $this->appearanceTab(),
                    $this->aboutTab(),
                    $this->backupTab(),
                ])

        ];
    }

    protected function generalTab(): Tab
    {
        return Tab::make('general')
            ->label('الإعدادات العامة')
            ->icon('heroicon-o-cog')
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('اسم النظام')
                                    ->required()
                                    ->default('منصة كفيل لرعاية وكفالة الأيتام'),
                                TextInput::make('site_email')
                                    ->label('البريد الإلكتروني الرسمي')
                                    ->email()
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('site_phone')
                                    ->label('رقم الهاتف')
                                    ->required(),
                                TextInput::make('site_whatsapp')
                                    ->label('رقم الواتساب')
                                    ->required(),
                            ]),
                        TextInput::make('site_address')
                            ->label('العنوان')
                            ->columnSpanFull(),
                        Textarea::make('site_description')
                            ->label('وصف النظام')
                            ->rows(3)
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('site_logo')
                                    ->label('شعار النظام')
                                    ->image()
                                    ->directory('settings')
                                    ->maxSize(1024),
                                FileUpload::make('site_favicon')
                                    ->label('أيقونة النظام (Favicon)')
                                    ->image()
                                    ->directory('settings')
                                    ->maxSize(512),
                            ]),
                    ]),
                Section::make('روابط التواصل الاجتماعي')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('site_facebook')
                                    ->label('فيسبوك')
                                    ->url()
                                    ->placeholder('https://facebook.com/...'),
                                TextInput::make('site_twitter')
                                    ->label('تويتر')
                                    ->url()
                                    ->placeholder('https://twitter.com/...'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('site_instagram')
                                    ->label('إنستغرام')
                                    ->url()
                                    ->placeholder('https://instagram.com/...'),
                                TextInput::make('site_youtube')
                                    ->label('يوتيوب')
                                    ->url()
                                    ->placeholder('https://youtube.com/...'),
                            ]),
                    ]),
            ]);
    }

    protected function organizationTab(): Tab
    {
        return Tab::make('organization')
            ->label('المؤسسة')
            ->icon('heroicon-o-office-building')
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('org_name')
                                    ->label('اسم المؤسسة')
                                    ->required(),
                                TextInput::make('org_manager')
                                    ->label('مدير المؤسسة'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('org_email')
                                    ->label('البريد الرسمي')
                                    ->email(),
                                TextInput::make('org_phone')
                                    ->label('الهاتف الرسمي'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('org_website')
                                    ->label('الموقع الإلكتروني')
                                    ->url(),
                                TextInput::make('org_address')
                                    ->label('العنوان'),
                            ]),
                    ]),
            ]);
    }

    protected function notificationsTab(): Tab
    {
        return Tab::make('notifications')
            ->label('الإشعارات')
            ->icon('heroicon-o-bell')
            ->schema([
                Card::make()
                    ->schema([
                        Toggle::make('notifications_enabled')
                            ->label('تفعيل الإشعارات')
                            ->default(true),
                    ]),
                Section::make('أنواع الإشعارات')
                    ->schema([
                        Toggle::make('notifications_sponsorship')
                            ->label('إشعارات الكفالات')
                            ->default(true),
                        Toggle::make('notifications_payment')
                            ->label('إشعارات المدفوعات')
                            ->default(true),
                        Toggle::make('notifications_orphan_updates')
                            ->label('إشعارات تحديثات الأيتام')
                            ->default(true),
                        Toggle::make('notifications_system')
                            ->label('إشعارات النظام')
                            ->default(true),
                    ]),
            ]);
    }

    protected function applicationTab(): Tab
    {
        $currentLocale = app()->getLocale();

        $switcherHtml = '
            <div style="display:flex;gap:12px;margin-top:4px;">
                <a href="' . url('/lang/ar') . '"
                   style="flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 20px;border-radius:8px;text-decoration:none;font-size:15px;font-weight:600;border:2px solid ' . ($currentLocale === 'ar' ? '#10b981' : '#e5e7eb') . ';background:' . ($currentLocale === 'ar' ? '#ecfdf5' : '#ffffff') . ';color:' . ($currentLocale === 'ar' ? '#065f46' : '#374151') . ';transition:all 0.15s;"
                   onmouseover="this.style.borderColor=\'#10b981\';this.style.background=\'#ecfdf5\'"
                   onmouseout="this.style.borderColor=\'' . ($currentLocale === 'ar' ? '#10b981' : '#e5e7eb') . '\';this.style.background=\'' . ($currentLocale === 'ar' ? '#ecfdf5' : '#ffffff') . '\'">
                    <span style="font-size:22px;">🇸🇦</span>
                    العربية
                    ' . ($currentLocale === 'ar' ? '<span style="font-size:14px;background:#10b981;color:#fff;padding:2px 8px;border-radius:10px;font-weight:500;">مفعلة</span>' : '') . '
                </a>
                <a href="' . url('/lang/en') . '"
                   style="flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 20px;border-radius:8px;text-decoration:none;font-size:15px;font-weight:600;border:2px solid ' . ($currentLocale === 'en' ? '#10b981' : '#e5e7eb') . ';background:' . ($currentLocale === 'en' ? '#ecfdf5' : '#ffffff') . ';color:' . ($currentLocale === 'en' ? '#065f46' : '#374151') . ';transition:all 0.15s;"
                   onmouseover="this.style.borderColor=\'#10b981\';this.style.background=\'#ecfdf5\'"
                   onmouseout="this.style.borderColor=\'' . ($currentLocale === 'en' ? '#10b981' : '#e5e7eb') . '\';this.style.background=\'' . ($currentLocale === 'en' ? '#ecfdf5' : '#ffffff') . '\'">
                    <span style="font-size:22px;">🇬🇧</span>
                    English
                    ' . ($currentLocale === 'en' ? '<span style="font-size:14px;background:#10b981;color:#fff;padding:2px 8px;border-radius:10px;font-weight:500;">Active</span>' : '') . '
                </a>
            </div>
        ';

        return Tab::make('application')
            ->label('اللغة')
            ->icon('heroicon-o-translate')
            ->schema([
                Card::make()
                    ->schema([
                        Placeholder::make('language_switcher')
                            ->label('اختيار لغة النظام')
                            ->content(new HtmlString($switcherHtml)),
                    ]),
                Card::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('app_timezone')
                                    ->label('المنطقة الزمنية')
                                    ->options([
                                        'UTC' => 'UTC',
                                        'Asia/Aden' => 'آسيا/عدن (UTC+3)',
                                        'Asia/Riyadh' => 'آسيا/الرياض (UTC+3)',
                                        'Africa/Cairo' => 'أفريقيا/القاهرة (UTC+2)',
                                    ])
                                    ->default('Asia/Aden'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('app_date_format')
                                    ->label('تنسيق التاريخ')
                                    ->default('Y-m-d')
                                    ->helperText('مثال: Y-m-d'),
                                TextInput::make('app_currency')
                                    ->label('العملة الافتراضية')
                                    ->default('YER'),
                            ]),
                    ]),
            ]);
    }

    protected function appearanceTab(): Tab
    {
        return Tab::make('appearance')
            ->label('إعدادات المظهر')
            ->icon('heroicon-o-color-swatch')
            ->schema([
                Card::make()
                    ->schema([
                        Section::make('وضع السمة')
                            ->schema([
                                Select::make('appearance_theme_mode')
                                    ->label('وضع السمة')
                                    ->options([
                                        'light' => 'فاتح',
                                        'dark' => 'داكن',
                                        'system' => 'افتراضي',
                                    ])
                                    ->default('light')
                                    ->helperText('اختر وضع السمة الافتراضي للوحة التحكم'),
                            ]),
                        Section::make('اللون الأساسي')
                            ->schema([
                                Select::make('appearance_primary_color')
                                    ->label('اللون الأساسي')
                                    ->options([
                                        'default' => 'افتراضي',
                                        'blue' => 'أزرق',
                                        'green' => 'أخضر',
                                        'emerald' => 'زمردي',
                                        'red' => 'أحمر',
                                        'orange' => 'برتقالي',
                                        'purple' => 'بنفسجي',
                                        'gray' => 'رمادي',
                                    ])
                                    ->default('green')
                                    ->helperText('اختر اللون الأساسي للوحة التحكم'),
                            ]),
                        Section::make('الشعار')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        FileUpload::make('appearance_logo_normal')
                                            ->label('الشعار العادي')
                                            ->image()
                                            ->directory('settings/logos')
                                            ->maxSize(1024),
                                        FileUpload::make('appearance_logo_dark')
                                            ->label('شعار الوضع الداكن')
                                            ->image()
                                            ->directory('settings/logos')
                                            ->maxSize(1024)
                                            ->helperText('يظهر عند تفعيل الوضع الداكن'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    protected function aboutTab(): Tab
    {
        return Tab::make('about')
            ->label('حول النظام')
            ->icon('heroicon-o-information-circle')
            ->schema([
                Card::make()
                    ->schema([
                        Placeholder::make('about_system_info')
                            ->label('')
                            ->content(
                                'منصة كفيل لرعاية وكفالة الأيتام هي منصة متكاملة لإدارة الأيتام والكفلاء والكفالات ومتابعة التقارير والإشعارات بطريقة احترافية وآمنة.'
                            ),
                    ]),
                Section::make('معلومات إضافية')
                    ->schema([
                        Textarea::make('about_system')
                            ->label('وصف النظام')
                            ->rows(5)
                            ->default(
                                'منصة كفيل لرعاية وكفالة الأيتام هي منصة متكاملة لإدارة الأيتام والكفلاء والكفالات ومتابعة التقارير والإشعارات بطريقة احترافية وآمنة.'
                            ),
                    ]),
            ]);
    }

    protected function backupTab(): Tab
    {
        $lastBackup = Setting::getValue('backup_last_date');
        $backupStatus = Setting::getValue('backup_status', 'لم يتم إنشاء نسخة احتياطية بعد');
        $nextBackup = Setting::getValue('backup_next_date', 'لم يتم الجدولة بعد');
        $storageDestination = Setting::getValue('backup_storage_destination', 'local');
        $backupSize = Setting::getValue('backup_last_size', 'لا يوجد');

        $backupService = app(\App\Services\BackupService::class);
        $localStatus = $backupService->verifyProvider('local');
        $externalStatus = $backupService->verifyProvider('external');

        return Tab::make('backup')
            ->label('تخزين واستعادة النسخ الاحتياطي')
            ->icon('heroicon-o-archive')
            ->schema([
                Card::make()
                    ->schema([
                        Placeholder::make('backup_status_info')
                            ->label('حالة آخر نسخة احتياطية')
                            ->content($backupStatus),
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('backup_last_date_info')
                                    ->label('آخر تاريخ')
                                    ->content($lastBackup ?? 'لا يوجد'),
                                Placeholder::make('backup_size_info')
                                    ->label('الحجم')
                                    ->content($backupSize),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('backup_next_date_info')
                                    ->label('التاريخ القادم')
                                    ->content($nextBackup),
                                Placeholder::make('backup_storage_destination_info')
                                    ->label('الوجهة الحالية')
                                    ->content($this->getStorageLabel($storageDestination)),
                            ]),
                    ]),
                Section::make('حالة التخزين')
                    ->description('حالة الاتصال الفعلية لكل مزود تخزين')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('status_local')
                                    ->label('التخزين المحلي')
                                    ->content($this->statusBadge($localStatus)),
                                Placeholder::make('status_external')
                                    ->label('قرص خارجي / USB')
                                    ->content($this->statusBadge($externalStatus)),
                            ]),
                    ]),
                Section::make('الإعدادات')
                    ->schema([
                        Toggle::make('backup_auto_enabled')
                            ->label('النسخ الاحتياطي التلقائي')
                            ->default(false),
                        Select::make('backup_frequency')
                            ->label('تكرار النسخ الاحتياطي')
                            ->options([
                                'daily' => 'يومي',
                                'weekly' => 'أسبوعي',
                                'monthly' => 'شهري',
                                'yearly' => 'سنوي',
                            ])
                            ->default('yearly'),
                    ]),
                Section::make('إعدادات الاحتفاظ بالنسخ')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('backup_retention_count')
                                    ->label('الاحتفاظ بآخر (N) نسخة')
                                    ->numeric()
                                    ->default(30)
                                    ->minValue(1)
                                    ->maxValue(365)
                                    ->suffix('نسخة'),
                                Toggle::make('backup_retention_auto_delete')
                                    ->label('الحذف التلقائي للنسخ القديمة')
                                    ->default(false),
                            ]),
                    ]),
                Section::make('إعدادات التخزين')
                    ->description('اختر وجهة التخزين وأدخل بيانات الاعتماد للمزود المطلوب')
                    ->schema([
                        Select::make('backup_storage_destination')
                            ->label('وجهة التخزين')
                            ->options([
                                'local' => 'تخزين محلي',
                                'local_external' => 'محلي + قرص خارجي / USB',
                            ])
                            ->default('local')
                            ->reactive()
                            ->afterStateUpdated(function ($set) {
                                foreach ($this->credentialKeys as $key) {
                                    $set($key, null);
                                }
                            }),
                        ...$this->externalDiskFields(),
                    ]),
                Section::make('إشعارات النسخ الاحتياطي')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('backup_notify_success')
                                    ->label('إشعار عند نجاح النسخ الاحتياطي')
                                    ->default(true),
                                Toggle::make('backup_notify_failure')
                                    ->label('إشعار عند فشل النسخ الاحتياطي')
                                    ->default(true),
                            ]),
                    ]),
                Section::make('استعادة نسخة احتياطية')
                    ->description('اختر نسخة احتياطية لاستعادة النظام إليها')
                    ->schema([
                        Placeholder::make('restore_backup')
                            ->label(' ')
                            ->content(new HtmlString($this->getRestoreBackupHtml($backupService))),
                    ]),
            ]);
    }

    private function statusBadge(array $status): string
    {
        $icon = match ($status['status']) {
            'ACTIVE' => '🟢',
            'FAILED' => '🔴',
            'NOT_CONFIGURED' => '⚪',
            default => '⚪',
        };
        return $icon . ' ' . $status['status'] . ' - ' . $status['message'];
    }

    private function getStorageLabel(string $destination): string
    {
        $labels = [
            'local' => 'تخزين محلي',
            'local_external' => 'محلي + قرص خارجي / USB',
        ];
        return $labels[$destination] ?? $destination;
    }

    private function externalDiskFields(): array
    {
        return [
            Grid::make(1)
                ->schema([
                    TextInput::make('backup_external_disk_path')
                        ->label('مسار القرص الخارجي')
                        ->placeholder('مثال: D:\\ أو E:\\Backup')
                        ->helperText('اتركه فارغاً للكشف التلقائي عن الأقراص المتصلة')
                        ->visible(fn ($get) => $get('backup_storage_destination') === 'local_external')
                        ->required(fn ($get) => $get('backup_storage_destination') === 'local_external'),
                ]),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            $group = match (true) {
                str_starts_with($key, 'org_') => 'organization',
                str_starts_with($key, 'contact_') => 'contact',
                str_starts_with($key, 'notifications_') => 'notifications',
                str_starts_with($key, 'app_') => 'application',
                str_starts_with($key, 'about_') => 'about',
                str_starts_with($key, 'appearance_') => 'appearance',
                str_starts_with($key, 'security_') => 'security',
                str_starts_with($key, 'backup_') => 'backup',
                default => 'general',
            };

            $type = match (true) {
                in_array($key, [
                    'notifications_enabled', 'notifications_sponsorship',
                    'notifications_orphan_updates', 'notifications_system',
                    'backup_auto_enabled', 'backup_annual_enabled',
                    'backup_retention_auto_delete', 'backup_notify_success',
                    'backup_notify_failure',
                ]) => 'boolean',
                in_array($key, [
                    'backup_retention_years',
                    'backup_retention_count',
                ]) => 'integer',
                default => 'string',
            };

            if (in_array($key, $this->credentialKeys) && !empty($value)) {
                $value = Crypt::encryptString($value);
            }

            Setting::setValue($key, $value, $group, $type);
        }

        SettingsHelper::clearCache();
        Cache::forget('app_locale');
        Cache::forget('app_settings');

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }

    private function getRestoreBackupHtml($backupService): string
    {
        $backups = $backupService->listBackups();

        if (empty($backups)) {
            return '<p class="text-sm text-gray-500">لا توجد نسخ احتياطية متاحة للاستعادة.</p>';
        }

        $options = '<option value="">-- اختر ملف --</option>';
        foreach ($backups as $b) {
            $selected = $b['filename'] === $this->selectedBackupFile ? ' selected' : '';
            $options .= '<option value="' . e($b['filename']) . '"' . $selected . '>'
                . e($b['filename']) . ' (' . e($b['size']) . ' - ' . e($b['created_at']) . ')</option>';
        }

        return '
        <div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">ملف النسخة الاحتياطية</label>
                <select wire:model="selectedBackupFile" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    ' . $options . '
                </select>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" wire:click="restoreBackup" wire:loading.attr="disabled" x-on:click="if (!confirm(\'تحذير: سيتم استبدال قاعدة البيانات الحالية بالكامل بالنسخة الاحتياطية. هل أنت متأكد من استمرار عملية الاستعادة؟\')) { $event.preventDefault(); }"
                    class="inline-flex items-center gap-2 rounded-lg bg-danger-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-danger-500 focus:outline-none focus:ring-2 focus:ring-danger-500 focus:ring-offset-2 disabled:opacity-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    استعادة النسخة الاحتياطية
                </button>
                <span wire:loading wire:target="restoreBackup" class="text-sm text-gray-500">جاري الاستعادة...</span>
            </div>
        </div>';
    }

    public function restoreBackup(): void
    {
        if (empty($this->selectedBackupFile)) {
            Notification::make()
                ->title('الرجاء اختيار ملف النسخة الاحتياطية')
                ->danger()
                ->send();
            return;
        }

        try {
            $service = app(\App\Services\BackupService::class);
            $result = $service->restore($this->selectedBackupFile);

            if ($result['success']) {
                Notification::make()
                    ->title($result['message'])
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title($result['message'])
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('فشلت عملية الاستعادة')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }
}
