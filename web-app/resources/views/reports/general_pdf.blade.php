<!DOCTYPE html>
<html lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { margin: 15px; }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            direction: ltr; 
            text-align: left; 
        }
        .header-section { text-align: center; margin-bottom: 25px; }
        .date-line { font-size: 14px; margin-top: 8px; }

        table { width: 100%; border-collapse: collapse; direction: ltr; margin-top: 10px; }
        th { 
            background-color: #1a237e; color: white; padding: 12px 5px; 
            text-align: center; border: 1px solid #1a237e; font-size: 12px;
        }
        td { 
            padding: 10px 5px; border: 1px solid #ddd; font-size: 14px;
            text-align: center; vertical-align: middle;
        }
        .rtl-text { direction: rtl; display: inline-block; }

        .no-print { margin-bottom: 30px; }
        .btn-print { padding: 12px 25px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-print:hover { background: #218838; }
        @media print { 
            .no-print { display: none !important; } 
            body { padding: 0; }
            table { box-shadow: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">طباعة التقرير (PDF)</button>
        <p style="color: #666; font-size: 14px;">سيتم إخفاء هذا الزر تلقائياً عند الطباعة</p>
    </div>

    <div class="header-section">
        @if (!empty($systemLogo))
            <img src="{{ asset('storage/' . $systemLogo) }}" alt="{{ $systemName }}"
                 style="max-height: 60px; margin-bottom: 10px;">
        @endif
        <h2 style="margin: 0; color: #1a237e;">{{ $systemName }}</h2>
        <h3 style="margin: 5px 0; color: #555; font-size: 14px;">{{ $orgName }}</h3>
        <hr style="border: 1px solid #1a237e; margin: 10px 0;">
        <h3 style="margin: 0; color: #333;">{{ $pdfTitle }}</h3>
        <div class="date-line">
            <span class="rtl-text">{{ $dateLabel }}</span> {{ $todayDate }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%">{{ $labels['status'] }}</th>
                <th style="width: 12%">{{ $labels['amount'] }}</th>
                <th style="width: 13%">{{ $labels['branch'] }}</th>
                <th style="width: 20%">{{ $labels['expiry'] }}</th>
                <th style="width: 20%">{{ $labels['sponsor'] }}</th>
                <th style="width: 20%">{{ $labels['name'] }}</th>
                <th style="width: 5%">#</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $record)
            <tr>
                <td class="center" style="color: {{ $record->expiry_color }}; font-weight: bold;">
                    {{ $record->status_fix }}
                </td>
                <td class="center">
                    {{ number_format($record->monthly_amount ?? 0) }} {{ $labels['currency'] }}
                </td>
                <td>
                    @php
                        $branchName = '---';
                        if ($record->branch) {
                            $rawName = $record->branch->name;
                            if (is_array($rawName)) {
                                $branchName = $rawName[app()->getLocale()] ?? reset($rawName);
                            } elseif (is_string($rawName) && is_array(json_decode($rawName, true))) {
                                $jsonData = json_decode($rawName, true);
                                $branchName = $jsonData[app()->getLocale()] ?? reset($jsonData);
                            } else {
                                $branchName = $rawName;
                            }
                        }

                        if (!empty($branchName) && $branchName !== '---' && app()->getLocale() === 'ar') {
                            $branchName = '<bdo dir="rtl">' . $branchName . '</bdo>';
                        }
                    @endphp
                    {!! $branchName ?: '---' !!}
                </td>
                <td class="center" style="color: {{ $record->expiry_color }}; font-weight: bold;">
                    {{ $record->expiry_status }}
                </td>
                <td>{{ $record->sponsor_fix }}</td>
                <td>{{ $record->name_fix }}</td>
                <td class="center">{{ $index + 1 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $settings = (object)[
            'manager' => $orgManager,
            'phone' => $orgPhone,
            'email' => $orgEmail,
            'website' => $orgWebsite,
            'address' => $orgAddress,
        ];
    @endphp
    <div style="width: 100%; margin-top: 40px; padding-top: 15px; border-top: 2px solid #333; direction: rtl !important;">
        <table style="width: 100%; border-collapse: collapse; direction: rtl !important; table-layout: fixed;">
            <tr>
                @if(!empty($settings->manager))
                    <td style="text-align: center; padding: 5px; font-size: 13px; font-weight: bold; color: #222; font-family: 'DejaVu Sans', sans-serif; direction: rtl;">
                        <span dir="rtl">مدير المؤسسة:</span>
                        <span dir="rtl">{{ $settings->manager }}</span>
                    </td>
                @endif
                @if(!empty($settings->phone))
                    <td style="text-align: center; padding: 5px; font-size: 13px; font-weight: bold; color: #222; font-family: 'DejaVu Sans', sans-serif; direction: rtl;">
                        <span dir="rtl">الهاتف:</span>
                        <span dir="ltr">{{ $settings->phone }}</span>
                    </td>
                @endif
                @if(!empty($settings->email))
                    <td style="text-align: center; padding: 5px; font-size: 13px; font-weight: bold; color: #222; font-family: 'DejaVu Sans', sans-serif; direction: rtl;">
                        <span dir="rtl">البريد:</span>
                        <span dir="ltr">{{ $settings->email }}</span>
                    </td>
                @endif
            </tr>
            <tr>
                @if(!empty($settings->website))
                    <td style="text-align: center; padding: 5px; font-size: 13px; font-weight: bold; color: #222; font-family: 'DejaVu Sans', sans-serif; direction: rtl;">
                        <span dir="rtl">الموقع:</span>
                        <span dir="ltr">{{ $settings->website }}</span>
                    </td>
                @endif
                @if(!empty($settings->address))
                    <td style="text-align: center; padding: 5px; font-size: 13px; font-weight: bold; color: #222; font-family: 'DejaVu Sans', sans-serif; direction: rtl;">
                        <span dir="rtl">العنوان:</span>
                        <span dir="rtl">{{ $settings->address }}</span>
                    </td>
                @endif
            </tr>
        </table>
    </div>
</body>
</html>
