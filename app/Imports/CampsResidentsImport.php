<?php

namespace App\Imports;

use App\Enums\GlobalSystemConstant;
use App\Models\displacementCamp;
use App\Models\displacementCampResident;
use App\Models\Status;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CampsResidentsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function rules(): array
    {
        return [
            'identity_number' => 'required|numeric|unique:displacement_camp_residents,identity_number',
            'full_name' => 'required',
            'camp_name' => 'required|exists:displacement_camps,name',
            'status' => 'required|exists:statuses,status_name',
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

        // Handle Status
        $status = null;
        if (isset($row['status'])) {
            if (is_numeric($row['status'])) {
                $status = Status::find($row['status']);
            } else {
                $status = Status::where('status_name', $row['status'])->first();
            }
        }

        // Handle Gender
        $gender = null;
        if (isset($row['gender'])) {
            if (is_numeric($row['gender'])) {
                $gender = ($row['gender']);
            } else {

                $gender =  GlobalSystemConstant::options()->where('type', 'gender')->first()['value'];
            }
        }

        // Parse birth date
        try {
            if (isset($row['birth_date']) && is_numeric($row['birth_date'])) {
                $birthDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['birth_date']);
            } else {
                $birthDate = isset($row['birth_date']) ? Carbon::parse($row['birth_date']) : null;
            }
        } catch (\Exception $e) {
            $birthDate = null;
        }

        return new displacementCampResident([
            'displacement_camp_id' => $camp ? $camp->id : null,
            'resident_type'     => $status ? $status->id : null,
            'identity_number'   => $row['identity_number'],
            'full_name'         => $row['full_name'],
            'birth_date'        => $birthDate,
            'phone'             => $row['phone'] ?? null,
            'gender'            => $gender ? $gender : null,
            'activation'        => 1, // Default to active
        ]);
    }
}
