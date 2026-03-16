<?php

namespace App\Imports;

use App\Models\Activity;
use App\Models\activityBeneficiaryName;
use App\Models\displacementCamp;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
 

class ActivityBeneficiaryNameImport implements ToModel, WithHeadingRow, WithValidation
{
    public function rules(): array
    {
        return [
            'activity_id' => 'required',
            'identity_number' => [
                'required',
                'integer',
                'min_digits:9',
                'max_digits:9',
            ],
            'full_name' => 'required',
             'camp_name' => 'nullable|exists:displacement_camps,id',
         
            'receipt_date' => 'required',
        ];
    }

    public function model(array $row)
    {

        // Handle Activit Id
        $camp = null;
        if (isset($row['activity_id'])) {
            if (is_numeric($row['activity_id'])) {
               
                $activityData = Activity::find($row['activity_id']);
            } else {
 
                $activityData = Activity::where('name', $row['activity_id'])->first();
            }
        }

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
            
            throw ValidationException::withMessages([
                'identity_number' =>  [$row['identity_number'].' - '.__('The identity number has already been taken for this activity.')]
            ]);
        }

        return new activityBeneficiaryName([
            'activity_id'           => $activityData ? $activityData->id : null,
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
