<?php

namespace App\Exports;

use App\Models\activityBeneficiaryName;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActivityBeneficiaryNamesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $activityId;

    public function __construct($activityId)
    {
        $this->activityId = $activityId;
    }

    public function query()
    {
        return activityBeneficiaryName::query()
            ->where('activity_id', $this->activityId)
            ->with(['displacementCamp', 'status']);
    }

    public function headings(): array
    {
        return [
            __('Full Name'),
            __('Receipt Date'),
            __('Receive Method'),
            __('Phone'),
            __('Identity Number'),
            __('Displacement Camp'),
            __('Received By Name'),
        ];
    }

    public function map($beneficiary): array
    {
        return [
            $beneficiary->full_name,
            $beneficiary->receipt_date,
            $beneficiary->status->status_name ?? $beneficiary->receive_method,
            $beneficiary->phone,
            $beneficiary->identity_number,
            $beneficiary->displacementCamp->name ?? '-',
            $beneficiary->receive_by_name,
        ];
    }
}
