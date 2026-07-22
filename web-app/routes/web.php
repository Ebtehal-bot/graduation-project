<?php

use Illuminate\Support\Facades\Route;
use App\Models\Setting;
use App\Models\Sponsorship;
use App\Models\Payment;
use App\Models\Orphan;
use App\Models\Sponsor;
use App\Models\Branch; // إضافة موديل الفروع لاستخدامه في التقرير
use App\Exports\SponsorshipsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\OrphanReportController;
use Barryvdh\DomPDF\Facade\Pdf; // استدعاء حزمة الـ PDF

Route::get('/', function () {
    $settings = Setting::all()->pluck('value', 'key')->toArray();

    $settings = (object) [
        'phone' => $settings['site_phone'] ?? null,
        'email' => $settings['site_email'] ?? null,
        'whatsapp' => $settings['site_whatsapp'] ?? null,
        'facebook' => $settings['site_facebook'] ?? null,
        'twitter' => $settings['site_twitter'] ?? null,
        'youtube' => $settings['site_youtube'] ?? null,
        'instagram' => $settings['site_instagram'] ?? null,
    ];

    return view('welcome', compact('settings'));
});

Route::get('/admin/register', \App\Http\Livewire\Auth\Register::class)->name('filament.register');

Route::get('/lang/{locale}', function ($locale) {
    if (!in_array($locale, ['ar', 'en'])) {
        abort(400);
    }
    session()->put('locale', $locale);
    \App\Models\Setting::setValue('site_locale', $locale, 'application', 'string');
    return redirect()->back();
})->name('language.switch');

Route::prefix('reports')->group(function () {

    Route::get('/orphan-yearly-report/{record}', function ($recordId) {
        $orphan = Orphan::with(['payments', 'sponsorship.sponsor'])->findOrFail($recordId);
        $year = request('year', date('Y'));
        $payments = $orphan->payments;
        $systemName = \App\Models\Setting::getValue('site_name', 'منصة كفيل لرعاية وكفالة الأيتام');
        $orgName = \App\Models\Setting::getValue('org_name', 'منصة كفيل');
        $systemLogo = \App\Models\Setting::getValue('site_logo', '');
        $orgManager = \App\Models\Setting::getValue('org_manager', '');
        $orgEmail = \App\Models\Setting::getValue('org_email', '');
        $orgPhone = \App\Models\Setting::getValue('org_phone', '');
        $orgWebsite = \App\Models\Setting::getValue('org_website', '');
        $orgAddress = \App\Models\Setting::getValue('org_address', '');
        return view('reports.orphan_yearly_payments', compact('orphan', 'year', 'payments', 'systemName', 'orgName', 'systemLogo', 'orgManager', 'orgEmail', 'orgPhone', 'orgWebsite', 'orgAddress'));
    })->name('orphan.yearly.report');

    Route::get('/orphan-form-empty', function () {
        return view('reports.orphan-form-empty');
    })->name('orphans.form.empty');

    Route::get('/orphan-form-filled', function () {
        $recordId = request('record');
        $orphan = Orphan::with('sponsorship.sponsor')->findOrFail($recordId);
        return view('reports.orphan-form-filled', compact('orphan'));
    })->name('orphans.form.filled');

    Route::get('/sponsorship-form-empty', function () {
        return view('reports.sponsorship_form');
    })->name('sponsorship.form.empty');

    Route::get('/sponsorship-form/{record}', function ($recordId) {
        $orphan = Orphan::with(['sponsorship.sponsor', 'attachments'])->findOrFail($recordId);
        return view('reports.sponsorship_form', compact('orphan'));
    })->name('sponsorship.form.filled');

    Route::get('/orphan-form/{record}', function ($recordId) {
        $orphan = Orphan::with('sponsorship.sponsor')->findOrFail($recordId);
        return view('reports.orphan_registration_form', compact('orphan'));
    })->name('orphans.print');

    Route::get('/export-excel', function () {
        $ids = request()->input('records');
        $records = Sponsorship::whereIn('id', (array)$ids)->with(['orphan.branch', 'sponsor'])->get();
        return Excel::download(new SponsorshipsExport($records), 'تقرير-الأيتام.xlsx');
    })->name('orphans.export_excel');

    Route::get('/orphan-detailed/{record}', function (Orphan $record) {
        return view('reports.orphan-detailed', ['record' => $record]);
    })->name('orphans.detailed');

    Route::get('/general-report-all', [OrphanReportController::class, 'generateReport'])->name('orphans.general_pdf');

    Route::get('/group-financial', function () {
        $payments = Payment::with('sponsorship.orphan')->get();
        return view('reports.group-financial', compact('payments'));
    })->name('payments.general_report');

    Route::get('/group-sponsors', function () {
        $sponsors = Sponsor::sponsorOnly()->withCount('sponsorships')->get();
        return view('reports.group-sponsors', compact('sponsors'));
    })->name('sponsors.group_report');

    Route::get('/sponsor/{record}', function (Sponsor $record) {
        abort_if($record->user_id && \App\Models\User::where('id', $record->user_id)->whereIn('role', ['super_admin', 'supervisor', 'employee'])->exists(), 404);
        $sponsor = $record->load('sponsorships.orphan');
        $sponsorships = $sponsor->sponsorships;
        $orphans = $sponsorships->map(function ($s) {
            $orphan = $s->orphan;
            if (!$orphan) return null;
            $orphan->sponsorship_amount = $s->monthly_amount;
            $orphan->sponsorship_status = $s->status;
            return $orphan;
        })->filter();
        $totalOrphans = $orphans->count();
        $totalMonthly = $orphans->sum('sponsorship_amount');
        $totalAnnual = $totalMonthly * 12;
        $systemName = \App\Models\Setting::getValue('site_name', 'منصة كفيل لرعاية وكفالة الأيتام');
        $orgName = \App\Models\Setting::getValue('org_name', 'منصة كفيل');
        $systemLogo = \App\Models\Setting::getValue('site_logo', '');
        $orgManager = \App\Models\Setting::getValue('org_manager', '');
        $orgEmail = \App\Models\Setting::getValue('org_email', '');
        $orgPhone = \App\Models\Setting::getValue('org_phone', '');
        $orgWebsite = \App\Models\Setting::getValue('org_website', '');
        $orgAddress = \App\Models\Setting::getValue('org_address', '');
        return view('reports.sponsor_report', compact(
            'sponsor', 'sponsorships', 'orphans',
            'totalOrphans', 'totalMonthly', 'totalAnnual',
            'systemName', 'orgName', 'systemLogo',
            'orgManager', 'orgEmail', 'orgPhone', 'orgWebsite', 'orgAddress'
        ));
    })->name('sponsor.report');

    // الإضافة الجديدة والمخصصة لتصدير PDF للفروع والمحافظات
    Route::get('/branch-pdf/{record}', function (Branch $record) {
        // جلب الأيتام التابعين لهذا الفرع مع بيانات كفالاتهم
        $orphans = Orphan::where('branch_id', $record->id)->with('sponsorship.sponsor')->get();
        
        $systemName = \App\Models\Setting::getValue('site_name', 'منصة كفيل لرعاية وكفالة الأيتام');
        $orgName = \App\Models\Setting::getValue('org_name', 'منصة كفيل');
        
        // عرض ملف الـ Blade المخصص للتقرير (تأكد من وجود الملف لاحقاً في المجلد)
        return view('reports.branch_orphans_report', compact('record', 'orphans', 'systemName', 'orgName'));
    })->name('branches.export-pdf');

    // مسار معالج التقارير الديناميكي الذكي المحدث بالكامل لإرسال الشعار والاسم للبطاقات والكشوفات
    Route::get('/dynamic-print', function () {
        $type = request('type', 'distribution');
        $title = request('title', 'كشف صرف المستحقات');
        $branchId = request('branch');
        $status = request('status', 'all');
        $gender = request('gender', 'all');
        $columns = explode(',', request('columns', 'name,amount,signature'));

        // الاستقبال الجديد للمتغيرات المضافة من واجهة التقارير (عدد الأشهر والملاحظات)
        $monthsCount = (int) request('months_count', 1);
        $notes = request('notes', '');
        $minAmount = request('min_amount', '');
        $generateDifferences = filter_var(request('generate_differences', false), FILTER_VALIDATE_BOOLEAN);

        // فحص جلب الاسم والشعار بأكثر من شكل للتأكد من الوصول للمفتاح الفعلي بقاعدة البيانات
        $systemName = \App\Models\Setting::getValue('site_name') 
            ?? \App\Models\Setting::getValue('org_name') 
            ?? 'منصة كفيل لرعاية وكفالة الأيتام';

        $orgName = \App\Models\Setting::getValue('org_name') 
            ?? 'منصة كفيل';

        $siteLogo = \App\Models\Setting::getValue('site_logo') 
            ?? \App\Models\Setting::getValue('logo') 
            ?? '';

        $logoUrl = $siteLogo ? $siteLogo : null;

        // 1. تقرير الأداء المقارن بين كل الفروع والمحافظات لمجلس الإدارة
        if ($type === 'branches_performance') {
            $branches = Branch::withCount('orphans')->get();
            return view('reports.template_performance', compact('title', 'branches', 'systemName', 'orgName'));
        }

        // 2. بناء استعلام الأيتام للتقارير المتبقية (كشوفات صرف، بطاقات، إنذارات كفالة)
        $query = Orphan::with(['sponsorship.sponsor', 'branch']);
        
        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }
        if ($gender && $gender !== 'all') {
            $query->where('gender', $gender); 
        }

        // فلترة حالات الكفالة والإنذارات الموشكة على الانتهاء
        if ($type === 'expiry') {
            $query->whereHas('sponsorship', function($q) {
                $q->where('status', 'inactive')->orWhere('end_date', '<=', date('Y-m-d'));
            });
        } elseif ($status === 'active') {
            $query->whereHas('sponsorship', function($q) { 
                $q->where('status', 'active'); 
            });
        } elseif ($status === 'inactive') {
            $query->whereDoesntHave('sponsorship');
        }

        $differencesOrphans = collect();
        $differencesTotal = 0;

        $orphans = $query->get();

        // إنشاء كشف الفروقات الإضافي — يُظهر الأيتام المكفولين ذوي كفالة أعلى من المبلغ المحدد
        if ($generateDifferences && $minAmount !== '' && is_numeric($minAmount)) {
            $threshold = (float) $minAmount;
            $differencesOrphans = $orphans->filter(function($o) use ($threshold) {
                $amt = $o->sponsorship->monthly_amount ?? 0;
                return $amt > $threshold;
            });
            $differencesTotal = $differencesOrphans->sum(function($o) use ($threshold, $monthsCount) {
                return max(0, (($o->sponsorship->monthly_amount ?? 0) - $threshold) * $monthsCount);
            });
        }

        // التوجيه إلى قالب كروت العضوية مع تمرير الشعار والاسم المضمونين
        if ($type === 'cards') {
            return view('reports.template_cards', compact('title', 'orphans', 'systemName', 'logoUrl'));
        }

        // تمرير المتغيرات الجديدة (monthsCount و notes) إلى قالب كشوفات الصرف المعتمد
        return view('reports.dynamic_orphans_template', compact(
            'title', 'orphans', 'columns', 'systemName', 'orgName', 'logoUrl',
            'monthsCount', 'notes', 'minAmount', 'generateDifferences',
            'differencesOrphans', 'differencesTotal'
        ));
    })->name('reports.dynamic.print');

});

Route::get('/sponsors/group-report', function () {
    $sponsors = Sponsor::sponsorOnly()->withCount('sponsorships')->get();
    return view('reports.group-sponsors', compact('sponsors'));
})->name('sponsors.export');

Route::get('/attachments/{attachment}/download', function (\App\Models\Attachment $attachment) {
    $filePath = \App\Models\Attachment::safeFilePath($attachment->file_path);
    abort_if(!$filePath, 404, 'File path is empty.');
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    abort_unless($disk->exists($filePath), 404);
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    $filename = match ($attachment->document_type) {
        'birth_certificate' => 'شهادة_ميلاد.' . $ext,
        'death_certificate' => 'شهادة_وفاة_الوالد.' . $ext,
        'id_card' => 'هوية_المعيل.' . $ext,
        default => 'مستند.' . $ext,
    };
    return $disk->download($filePath, $filename);
})->name('attachments.download');