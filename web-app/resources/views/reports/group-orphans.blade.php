@extends('reports.layout')

@section('title', 'السجل الشامل لبيانات الأيتام')
@section('report_title', 'السجل الشامل لبيانات الأيتام')

@section('content')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>اسم اليتيم</th>
                <th>العمر</th>
                <th>الجنس</th>
                <th>المحافظة</th>
                <th>الفرع</th>
                <th>حفظ القرآن</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $record->name }}</td>
                    <td>{{ $record->birth_date ? \Carbon\Carbon::parse($record->birth_date)->age : '---' }}</td>
                    <td>{{ $record->gender == 'male' ? 'ذكر' : ($record->gender == 'female' ? 'أنثى' : $record->gender) }}</td>
                    <td>{{ $record->address_gov ?? '---' }}</td>
                    <td>{{ $record->branch?->name ?? 'غير متوفر' }}</td>
                    <td>{{ $record->quran_memorization ?? '---' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="color: #dc3545; padding: 30px; font-weight: bold;">
                        لا توجد أي بيانات أيتام مسجلة في النظام
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="stats-container">
        <div class="stat-box info-box">
            إجمالي عدد الأيتام: {{ count($records) }} يتيم
        </div>
    </div>
@endsection