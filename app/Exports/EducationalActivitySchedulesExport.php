<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EducationalActivitySchedulesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $query;
    private $rowNumber = 0;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return [
            'م.',
            'اليوم/التاريخ',
            'مجال النشاط',
            'الفئة',
            'اسم النشاط',
            'الوصف',
            'مجموعة الطلاب',
            'المجموعات المسندة',
            'المنشط / المعلم',
            'وقت البدء',
            'وقت الانتهاء',
            'ملاحظات',
            'الحالة',
        ];
    }

    public function map($schedule): array
    {
        $this->rowNumber++;

        $daysArabic = [
            'Sunday' => 'الأحد',
            'Monday' => 'الاثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
            'Saturday' => 'السبت'
        ];

        $dayDateStr = '';
        if ($schedule->period_start) {
            $dayEnglish = $schedule->period_start->format('l');
            $dayArabic = $daysArabic[$dayEnglish] ?? $dayEnglish;
            $dayDateStr = $dayArabic . ' ' . $schedule->period_start->format('Y/m/d');
        }

        $startTimeStr = $schedule->period_start ? $schedule->period_start->format('H:i') : '';
        $endTimeStr = $schedule->period_end ? $schedule->period_end->format('H:i') : '';
        $groupName = $schedule->group?->name ?? '';

        return [
            $this->rowNumber,
            $dayDateStr,
            $schedule->activityDomain?->status_name ?? '',
            $schedule->target_category,
            $schedule->activity_name,
            $schedule->activity_description,
            $groupName,
            $schedule->periodGroups?->status_name ?? '',
            $schedule->employee?->full_name ?? '',
            $startTimeStr,
            $endTimeStr,
            $schedule->notes,
            $schedule->activation == 1 ? __('Active') : __('Inactive'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Bold header row and set size
        $sheet->getStyle('1')->getFont()->setBold(true)->setSize(11);
        
        // Background color for header row (light blue/gray like in the photo: #DCE6F1)
        $sheet->getStyle('A1:M1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('DCE6F1');

        $highestRow = $sheet->getHighestRow();

        // Center align everything
        $sheet->getStyle('A1:M' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:M' . $highestRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        
        // Add borders to headers and active rows
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'A6A6A6'],
                ],
            ],
        ];
        
        $sheet->getStyle('A1:M' . $highestRow)->applyFromArray($styleArray);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}
