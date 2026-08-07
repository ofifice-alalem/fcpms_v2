<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PerformanceReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return collect($this->rows);
    }

    public function headings(): array
    {
        return [
            'تاريخ اليوم',
            'اسم الاستشاري',
            'الرقم الوظيفي',
            'موقع العمل المزار',
            'المدينة',
            'وقت الدخول',
            'وقت الخروج',
            'حالة الزيارة',
            'الملاحظات',
        ];
    }
}
