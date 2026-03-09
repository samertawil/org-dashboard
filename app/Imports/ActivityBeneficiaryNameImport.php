<?php

namespace App\Imports;

use App\Models\Activity;
use App\Models\activityBeneficiaryName;
use App\Models\displacementCamp;
use App\Models\Status;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ActivityBeneficiaryNameImport implements ToModel, WithHeadingRow, WithValidation
{
    public function rules(): array
    {
        return [
            'activity_id' => 'required|exists:activities,id',
            'identity_number' => 'required|numeric', // We'll handle unique validation carefully based on activity_id
            'full_name' => 'required',
            'camp_name' => 'nullable|exists:displacement_camps,name',
            'receive_method' => 'nullable|exists:statuses,status_name',
            'receipt_date' => 'required',
        ];
    }

    public function model(array $row)
    {
        // Handle Camp
        $camp = null;
        if (isset($row['camp_name'])) {
            if (is_numeric($row['camp_name'])) {
                $camp = displacementCamp::find($row['camp_name']);
            } else {
                $camp = displacementCamp::where('name', $row['camp_name'])->first();
            }
        }

        // Handle Status / Receive Method
        $receiptMethod = null;
        if (isset($row['receive_method'])) {
            if (is_numeric($row['receive_method'])) {
                $receiptMethod = Status::find($row['receive_method']);
            } else {
                $receiptMethod = Status::where('status_name', $row['receive_method'])->first();
            }
        }

        // Parse receipt date
        try {
            if (isset($row['receipt_date']) && is_numeric($row['receipt_date'])) {
                $receiptDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['receipt_date']);
            } else {
                $receiptDate = isset($row['receipt_date']) ? Carbon::parse($row['receipt_date']) : null;
            }
        } catch (\Exception $e) {
            $receiptDate = null;
        }

        // Check uniqueness before returning to prevent import failures
        $exists = activityBeneficiaryName::where('activity_id', $row['activity_id'])
            ->where('identity_number', $row['identity_number'])
            ->exists();

        if ($exists) {
            return null; // Skip if already exists
        }

        return new activityBeneficiaryName([
            'activity_id'           => $row['activity_id'],
            'displacement_camps_id' => $camp ? $camp->id : null,
            'identity_number'       => $row['identity_number'],
            'full_name'             => $row['full_name'],
            'phone'                 => $row['phone'] ?? null,
            'receipt_date'          => $receiptDate,
            'receive_method'        => $receiptMethod ? $receiptMethod->id : null,
            'receive_by_name'       => $row['receive_by_name'] ?? null,
        ]);
    }
}
