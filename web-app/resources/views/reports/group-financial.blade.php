@extends('reports.layout')

@section('title', 'السجل الشامل للمدفوعات')
@section('report_title', 'السجل الشامل للمدفوعات')

@section('content')
    @php $grandTotal = 0; @endphp

    <table>
        <thead>
            <tr>
                <th>اسم اليتيم</th>
                <th>رقم السند</th>
                <th>تاريخ الدفع</th>
                <th>المبلغ (ريال يمني)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->sponsorship->orphan->name ?? '---' }}</td>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->date }}</td>
                    <td>{{ number_format($payment->amount) }} ريال</td>
                </tr>
                @php $grandTotal += $payment->amount; @endphp
            @empty
                <tr>
                    <td colspan="4" style="color: #dc3545; padding: 30px; font-weight: bold;">
                        لا توجد أي مدفوعات مسجلة في النظام
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="stats-container">
        <div class="stat-box total-box">
            إجمالي التحصيل الكلي: {{ number_format($grandTotal) }} ريال
        </div>
    </div>
@endsection
