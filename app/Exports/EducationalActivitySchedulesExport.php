<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EducationalActivitySchedulesExport implements FromArray, ShouldAutoSize, WithStyles, WithEvents
{
    protected $query;

    // Will be populated during collection build
    private array $pivotRows    = [];
    private array $timeSlots    = [];
    private array $headings     = [];
    private bool  $built        = false;

    private array $daysArabic = [
        'Sunday'    => 'الأحد',
        'Monday'    => 'الاثنين',
        'Tuesday'   => 'الثلاثاء',
        'Wednesday' => 'الأربعاء',
        'Thursday'  => 'الخميس',
        'Friday'    => 'الجمعة',
        'Saturday'  => 'السبت',
    ];

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * Build the pivot structure once.
     * Each unique (date + student_group + period_group) becomes one row.
     * Each unique time-slot becomes a dynamic column.
     */
    private function build(): void
    {
        if ($this->built) {
            return;
        }

        $schedules = $this->query->orderBy('period_start', 'asc')->get();

        // Step 1 – collect all unique time slots (sorted)
        $timeSlotsSet = [];
        foreach ($schedules as $schedule) {
            if ($schedule->period_start && $schedule->period_end) {
                $slot = $schedule->period_start->format('H:i')
                    . ' - '
                    . $schedule->period_end->format('H:i');
                $timeSlotsSet[$slot] = true;
            }
        }
        ksort($timeSlotsSet);
        $this->timeSlots = array_keys($timeSlotsSet);

        // Step 2 – group schedules by (date + student_group + period_group)
        $grouped = [];
        foreach ($schedules as $schedule) {
            if (! $schedule->period_start) {
                continue;
            }

            $dateStr       = $schedule->period_start->format('Y/m/d');
            $dayEn         = $schedule->period_start->format('l');
            $dayAr         = $this->daysArabic[$dayEn] ?? $dayEn;
            $fullDate      = $dayAr . ' ' . $dateStr;

            // If period_end date differs from period_start date, show date range
            if ($schedule->period_end && $schedule->period_end->format('Y/m/d') !== $dateStr) {
                $endDateStr  = $schedule->period_end->format('Y/m/d');
                $endDayEn    = $schedule->period_end->format('l');
                $endDayAr    = $this->daysArabic[$endDayEn] ?? $endDayEn;
                $fullDate   .= ' إلى ' . $endDayAr . ' ' . $endDateStr;
            }

            $groupName     = $schedule->group?->name ?? '';
            $periodGrpName = $schedule->periodGroups?->status_name ?? '';
            $periodGrpDesc = $schedule->periodGroups?->description ?? '';

            // Display: "status_name (description)" or just "status_name" if no description
            $periodGrpDisplay = $periodGrpName;
            if ($periodGrpDesc) {
                $periodGrpDisplay = $periodGrpName . ' // ' . ' (' . $periodGrpDesc . ')';
            }

            // One row per (date + student_group + period_group)
            $rowKey = $dateStr . '||' . $groupName . '||' . $periodGrpName;

            if (! isset($grouped[$rowKey])) {
                $grouped[$rowKey] = [
                    'sort'        => $schedule->period_start->timestamp,
                    'date'        => $fullDate,
                    'group'       => $groupName,
                    'period_grp'  => $periodGrpDisplay,
                    'domain'      => $schedule->activityDomain?->status_name ?? '',
                    'category'    => $schedule->target_category ?? '',
                    'slots'       => [],
                ];
            }

            // Fill in the time-slot cell with the activity name
            if ($schedule->period_start && $schedule->period_end) {
                $slot = $schedule->period_start->format('H:i')
                    . ' - '
                    . $schedule->period_end->format('H:i');

                // If multiple activities share the same slot in the same row, join with " / "
                if (isset($grouped[$rowKey]['slots'][$slot]) && $grouped[$rowKey]['slots'][$slot] !== '') {
                    $grouped[$rowKey]['slots'][$slot] .= ' / ' . $schedule->activity_name;
                } else {
                    $grouped[$rowKey]['slots'][$slot] = $schedule->activity_name;
                }
            }
        }

        // Sort rows by date (ascending), then by period_grp name
        uasort($grouped, function ($a, $b) {
            if ($a['sort'] !== $b['sort']) {
                return $a['sort'] <=> $b['sort'];
            }
            return strcmp($a['period_grp'], $b['period_grp']);
        });

        // Step 3 – build headings row
        // Fixed columns: م. | اليوم/التاريخ | مجموعة الطلاب | المجموعة الزمنية | مجال النشاط | الفئة
        // Dynamic columns: one per unique time-slot
        $this->headings = array_merge(
            ['م.', 'اليوم/التاريخ', 'مجموعة الطلاب', 'المجموعة العمرية', 'مجال النشاط', 'الفئة'],
            $this->timeSlots
        );

        // Step 4 – build data rows
        $rowNumber = 0;
        foreach ($grouped as $row) {
            $rowNumber++;
            $dataRow = [
                $rowNumber,
                $row['date'],
                $row['group'],
                $row['period_grp'],
                $row['domain'],
                $row['category'],
            ];

            foreach ($this->timeSlots as $slot) {
                $dataRow[] = $row['slots'][$slot] ?? '';
            }

            $this->pivotRows[] = $dataRow;
        }

        $this->built = true;
    }


    public function array(): array
    {
        $this->build();

        // Prepend the headings row as the first array row
        return array_merge([$this->headings], $this->pivotRows);
    }

    public function styles(Worksheet $sheet)
    {
        $this->build();

        $totalColumns = count($this->headings);
        $lastCol      = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);
        $highestRow   = $sheet->getHighestRow();

        // Bold header row
        $sheet->getStyle('1')->getFont()->setBold(true)->setSize(11);

        // Header background colour (light blue)
        $sheet->getStyle('A1:' . $lastCol . '1')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('DCE6F1');

        // Center align all cells
        $sheet->getStyle('A1:' . $lastCol . $highestRow)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        // Thin borders on all cells
        $sheet->getStyle('A1:' . $lastCol . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['argb' => 'A6A6A6'],
                ],
            ],
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}
