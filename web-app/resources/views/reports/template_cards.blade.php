<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كروت تعريف الأيتام المطور</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            padding: 30px; 
            direction: rtl; 
            background-color: #f3f4f6;
        }
        .no-print-btn { 
            background: #10b981; 
            color: white; 
            padding: 12px 25px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            transition: background 0.2s;
        }
        .no-print-btn:hover { background: #059669; }

        /* شبكة الكروت المتطورة */
        .cards-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 30px; 
        }

        /* تصميم الكرت الفخم */
        .card { 
            border: 1px solid #e5e7eb; 
            border-radius: 16px; 
            position: relative; 
            background: #ffffff; 
            height: 240px; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border-top: 6px solid #0284c7; /* شريط علوي ملون يعطي فخامة */
        }

        /* الترويسة العلوية للكرت */
        .card-header { 
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 15px; 
            background: #f8fafc;
            border-bottom: 1px solid #f1f5f9; 
        }
        .card-header .title-area {
            text-align: right;
        }
        .card-header h3 {
            margin: 0;
            font-size: 13px;
            color: #1e293b;
            font-weight: 800;
        }
        .card-header p {
            margin: 2px 0 0 0;
            font-size: 10px;
            color: #64748b;
        }
        .card-header .logo-img {
            height: 35px;
            width: auto;
            object-fit: contain;
        }

        /* محتوى الكرت الرئيسي */
        .card-body {
            padding: 15px;
            display: flex;
            justify-content: space-between;
        }
        
        /* منطقة البيانات */
        .info-area {
            flex: 1;
            padding-left: 10px;
        }
        .info-row {
            margin-bottom: 8px;
            font-size: 13px;
            color: #334155;
            display: flex;
        }
        .info-row strong {
            color: #0f172a;
            min-width: 90px;
            display: inline-block;
        }
        .info-row span {
            color: #475569;
        }

        /* حالة الكفالة كشريط ملصق احترافي */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-active { background-color: #dcfce7; color: #15803d; }
        .badge-inactive { background-color: #fef3c7; color: #b45309; }

        /* منطقة الصورة الشخصية المحدثة */
        .photo-container { 
            width: 90px; 
            height: 115px; 
            border: 2px solid #e2e8f0; 
            border-radius: 8px;
            overflow: hidden;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        }
        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-placeholder {
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
        }

        /* التذييل السفلي: باركود رقمي وهمي للمظهر الاحترافي */
        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #0f172a;
            color: #94a3b8;
            padding: 6px 15px;
            font-size: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .barcode-simulation {
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 4px;
            color: #ffffff;
            font-weight: bold;
        }

        @media print { 
            .no-print { display: none; } 
            body { background-color: #fff; padding: 0; }
            .card { box-shadow: none; border: 1px solid #000; page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: left;">
        <button class="no-print-btn" onclick="window.print()">🖨️ طباعة كروت العضوية الفخمة</button>
    </div>

    <div class="cards-grid">
        @foreach($orphans as $orphan)
        <div class="card">
            
            @php
                $displaySystemName = $systemName ?? \App\Models\Setting::getValue('site_name') ?? \App\Models\Setting::getValue('org_name') ?? 'منصة كفيل لرعاية وكفالة الأيتام';
                $dbLogo = \App\Models\Setting::getValue('site_logo') ?? \App\Models\Setting::getValue('logo');
            @endphp

            <div class="card-header">
                <div class="title-area">
                    <h3>{{ $displaySystemName }}</h3>
                    <p>بطاقة تعريفية إلكترونية موحدة</p>
                </div>
                
                @if($dbLogo)
                    <img class="logo-img" 
                         src="{{ asset('storage/' . $dbLogo) }}" 
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($displaySystemName) }}&background=0284c7&color=fff&rounded=true';" 
                         alt="الشعار">
                @else
                    <img class="logo-img" src="https://ui-avatars.com/api/?name={{ urlencode($displaySystemName) }}&background=0284c7&color=fff&rounded=true" alt="شعار النظام">
                @endif
            </div>

            <div class="card-body">
                <div class="info-area">
                    <div class="info-row">
                        <strong>اسم اليتيم:</strong> 
                        <span style="font-weight: bold; color: #0284c7;">{{ $orphan->name }}</span>
                    </div>
                    <div class="info-row">
                        <strong>تاريخ الميلاد:</strong> 
                        <span>{{ $orphan->birth_date ?? 'غير مسجل' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>النطاق الجغرافي:</strong> 
                        <span>{{ $orphan->branch->name ?? 'المركز الرئيسي' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>حالة الاستحقاق:</strong> 
                        @if($orphan->sponsorship)
                            <span class="badge badge-active">مكفول ومستحق</span>
                        @else
                            <span class="badge badge-inactive">بانتظار كفيل</span>
                        @endif
                    </div>
                </div>

                <div class="photo-container">
                    @if($orphan->photo)
                        <img src="{{ asset('storage/' . $orphan->photo) }}" alt="صورة المستفيد">
                    @else
                        <div class="photo-placeholder">بانتظار<br>الصورة</div>
                    @endif
                </div>
            </div>

            <div class="card-footer">
                <div>تاريخ الإصدار: {{ date('Y-m-d') }}</div>
                <div class="barcode-simulation">||| | |||| || | || || {{ $orphan->id }}</div>
            </div>

        </div>
        @endforeach
    </div>

</body>
</html>