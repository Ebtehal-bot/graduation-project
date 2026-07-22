@php
    $primaryTypes = [
        'birth_certificate' => 'شهادة الميلاد',
        'death_certificate' => 'شهادة وفاة الوالد',
        'id_card' => 'هوية المعيل',
    ];

    // Query attachments directly to avoid JSON column shadowing the relationship
    $orphan = $orphan ?? null;
    $attachmentsList = $orphan && $orphan->id
        ? \App\Models\Attachment::where('orphan_id', $orphan->id)->get()
        : collect();

    // Collect attachments grouped by type, safely normalized
    $docs = [];
    foreach ($attachmentsList as $a) {
        $path = \App\Models\Attachment::normalizeFile($a->file_path);
        if (!$path) continue;
        $type = $a->document_type;
        $docs[$type] = [
            'path' => $path,
            'url' => \App\Models\Attachment::getFileUrl($path),
            'canView' => \App\Models\Attachment::canViewInline($path),
            'id' => $a->id,
        ];
    }
@endphp

<div style="padding:12px 0;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr>
                <th style="text-align:right;padding:10px 8px;border-bottom:2px solid #ddd;font-size:14px;">المستند</th>
                <th style="text-align:center;padding:10px 8px;border-bottom:2px solid #ddd;font-size:14px;">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <!-- القسم المطور: الوثائق المدرسية ورسائل الشكر المسجلة في جدول اليتيم -->
            @if($orphan)
                <!-- 1. نتيجة اليتيم الدراسية -->
                <tr>
                    <td style="padding:12px 8px;border-bottom:1px solid #eee;font-size:14px;font-weight: bold;color: #1e293b;">
                        صورة النتيجة المدرسية الجديدة
                    </td>
                    <td style="text-align:center;padding:12px 8px;border-bottom:1px solid #eee;">
                        @if($orphan->academic_result)
                            <div style="display:flex;gap:8px;justify-content:center;">
                                <a href="{{ asset('storage/' . $orphan->academic_result) }}" target="_blank"
                                   style="display:inline-block;padding:6px 16px;background:#3b82f6;color:#fff;border-radius:4px;text-decoration:none;font-size:13px;font-weight:bold;">
                                    عرض النتيجة
                                </a>
                            </div>
                        @else
                            <span style="color:#999;font-size:13px;">غير متوفر</span>
                        @endif
                    </td>
                </tr>

                <!-- 2. رسالة شكر واحدة فقط بخط اليتيم لكافل محدد -->
                <tr>
                    <td style="padding:12px 8px;border-bottom:1px solid #eee;font-size:14px;font-weight: bold;color: #1e293b;">
                        رسالة شكر للكافل بخط اليتيم
                    </td>
                    <td style="text-align:center;padding:12px 8px;border-bottom:1px solid #eee;">
                        @if($orphan->thank_you_letter)
                            <div style="display:flex;gap:8px;justify-content:center;">
                                <a href="{{ asset('storage/' . $orphan->thank_you_letter) }}" target="_blank"
                                   style="display:inline-block;padding:6px 16px;background:#ff9800;color:#fff;border-radius:4px;text-decoration:none;font-size:13px;font-weight:bold;">
                                    عرض الرسالة
                                </a>
                            </div>
                        @else
                            <span style="color:#999;font-size:13px;">غير متوفر</span>
                        @endif
                    </td>
                </tr>

                <!-- خط فاصل رمزي لتمييز الملفات الحالية عن المرفقات العامة -->
                <tr>
                    <td colspan="2" style="background: #f8fafc; padding: 4px 8px; border-bottom:1px solid #eee;"></td>
                </tr>
            @endif

            <!-- المستندات الأساسية (شهادة الميلاد، وفاة، هوية) -->
            @foreach($primaryTypes as $type => $label)
                @php $doc = $docs[$type] ?? null; @endphp
                <tr>
                    <td style="padding:12px 8px;border-bottom:1px solid #eee;font-size:14px;">{{ $label }}</td>
                    <td style="text-align:center;padding:12px 8px;border-bottom:1px solid #eee;">
                        @if($doc)
                            <div style="display:flex;gap:8px;justify-content:center;">
                                @if($doc['canView'])
                                    <a href="{{ $doc['url'] }}" target="_blank"
                                       style="display:inline-block;padding:6px 16px;background:#3b82f6;color:#fff;border-radius:4px;text-decoration:none;font-size:13px;font-weight:bold;">
                                        عرض
                                    </a>
                                @endif
                                <a href="{{ route('attachments.download', $doc['id']) }}"
                                   style="display:inline-block;padding:6px 16px;background:#22c55e;color:#fff;border-radius:4px;text-decoration:none;font-size:13px;font-weight:bold;">
                                    تحميل
                                </a>
                            </div>
                        @else
                            <span style="color:#999;font-size:13px;">غير متوفر</span>
                        @endif
                    </td>
                </tr>
            @endforeach

            <!-- المستندات المرفقة الأخرى المضافة عبر الـ Repeater -->
            @foreach($attachmentsList as $a)
                @php
                    $path = \App\Models\Attachment::normalizeFile($a->file_path);
                    $type = $a->document_type;
                @endphp
                @continue(!$path || isset($primaryTypes[$type]))
                <tr>
                    <td style="padding:12px 8px;border-bottom:1px solid #eee;font-size:14px;">
                        {{ $a->document_type ?? 'مستند' }}
                    </td>
                    <td style="text-align:center;padding:12px 8px;border-bottom:1px solid #eee;">
                        <div style="display:flex;gap:8px;justify-content:center;">
                            @php
                                $url = \App\Models\Attachment::getFileUrl($path);
                                $canView = \App\Models\Attachment::canViewInline($path);
                            @endphp
                            @if($canView)
                                <a href="{{ $url }}" target="_blank"
                                   style="display:inline-block;padding:6px 16px;background:#3b82f6;color:#fff;border-radius:4px;text-decoration:none;font-size:13px;font-weight:bold;">
                                    عرض
                                </a>
                            @endif
                            <a href="{{ route('attachments.download', $a->id) }}"
                               style="display:inline-block;padding:6px 16px;background:#22c55e;color:#fff;border-radius:4px;text-decoration:none;font-size:13px;font-weight:bold;">
                                تحميل
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>