<?php

namespace App\Exports;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivitiesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $fromDate;
    protected $toDate;
    private $rowNumber = 0;

    public function __construct($fromDate, $toDate)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function query()
    {
        return Activity::query()
            ->with(['regions', 'cities', 'activityStatus', 'statusSpecificSector', 'creator', 'parcels.parcelType', 'parcels.unit', 'beneficiaries.beneficiaryType'])
            ->when($this->fromDate, function ($query) {
                $query->where('start_date', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($query) {
                $query->where('start_date', '<=', $this->toDate);
            })
            ->orderBy('start_date', 'desc');
    }

    public function headings(): array
    {
        return [
            __('#'),
            __('Activity Name'),
            __('Description'),
            __('Start Date'),
            __('End Date'),
            __('Sector'),
            __('Status'),
            __('Region'),
            __('City'),
            __('Cost (USD)'),
            __('Cost (NIS)'),
            __('Total Parcels'),
            __('Parcels Details'),
            __('Total Beneficiaries'),
            __('Beneficiaries Details'),
            __('Created By'),
            __('Created At'),
        ];
    }

    public function map($activity): array
    {
        $this->rowNumber++;

        $parcelsDetails = $activity->parcels->map(function($p) {
            return ($p->parcelType->status_name ?? '-') . ': ' . $p->distributed_parcels_count . ' ' . ($p->unit->status_name ?? '');
        })->implode(' | ');

        $beneficiariesDetails = $activity->beneficiaries->map(function($b) {
            return ($b->beneficiaryType->status_name ?? '-') . ': ' . $b->beneficiaries_count;
        })->implode(' | ');

        return [
            $this->rowNumber,
            $activity->name,
            $activity->description,
            $activity->start_date,
            $activity->end_date,
            $activity->statusSpecificSector->status_name ?? '-',
            $activity->status_info['name'] ?? '-',
            $activity->regions->region_name ?? '-',
            $activity->cities->city_name ?? '-',
            $activity->cost,
            $activity->cost_nis,
            $activity->parcels->sum('distributed_parcels_count'),
            $parcelsDetails,
            $activity->beneficiaries->sum('beneficiaries_count'),
            $beneficiariesDetails,
            $activity->creator->name ?? '-',
            $activity->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
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
