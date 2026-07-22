@extends('reports.layout')

@section('title', 'السجل الشامل لبيانات الكفلاء')
@section('report_title', 'السجل الشامل لبيانات الكفلاء')

@section('content')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>اسم الكفيل</th>
                <th>رقم الهاتف</th>
                <th>البريد الإلكتروني</th>
                <th>عدد الكفالات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sponsors as $index => $sponsor)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $sponsor->name }}</td>
                    <td>{{ $sponsor->phone ?? '---' }}</td>
                    <td>{{ $sponsor->email ?? '---' }}</td>
                    <td>{{ $sponsor->sponsorships_count ?? $sponsor->sponsorships->count() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="color: #dc3545; padding: 30px; font-weight: bold;">
                        لا توجد أي بيانات كفلاء مسجلة في النظام
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="stats-container">
        <div class="stat-box info-box">
            إجمالي عدد الكفلاء: {{ count($sponsors) }} كفيل
        </div>
    </div>
@endsection
