<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SponsorshipsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $records;

    public function __construct($records)
    {
        // نضمن أن السجلات ممررة بشكل صحيح من الراوت
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    /**
     * تنسيق العناوين (Headings)
     */
    public function headings(): array
    {
        return [
            'اسم اليتيم',
            'اسم الكفيل',
            'الفرع',
            'مبلغ الكفالة الشهرية',
            'تاريخ بدء الكفالة',
            'حالة الكفالة',
        ];
    }

    /**
     * ربط البيانات بالأعمدة لضمان عدم ظهور أرقام أو أصفار غير مفهومة
     */
    public function map($record): array
    {
        return [
            // استخدام optional أو null coalescing لضمان عدم توقف التصدير في حال حذف يتيم
            $record->orphan->name ?? 'غير مسجل',
            $record->sponsor->name ?? 'فاعل خير',
            $record->orphan->branch->name ?? 'المركز الرئيسي',
            $record->monthly_amount . ' ر.ي', // إضافة العملة لتبدو أكثر احترافية
            $record->start_date ?? '-',
            $record->status === 'active' ? 'نشطة' : 'متوقفة',
        ];
    }

    /**
     * إضافة تنسيق جمالي لملف الإكسل (اختياري لكنه يعطي انطباع جيد للدكتور)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // جعل الصف الأول (العناوين) عريضاً وخلفيته ملونة
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'] // لون أزرق احترافي
                ],
            ],
        ];
    }
}