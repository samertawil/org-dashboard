<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\Status;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function rules(): array
    {
        return [
            'identity_number' => 'required|unique:students,identity_number',
            'full_name' => 'required',
        ];
    }

    public function model(array $row)
    {
        // Handle Student Group
        $studentGroup = null;
        if (isset($row['student_group'])) {
            if (is_numeric($row['student_group'])) {
                $studentGroup = StudentGroup::find($row['student_group']);
            } else {
                $studentGroup = StudentGroup::where('name', $row['student_group'])->first();
            }
        }

        // Handle Status
        $status = null;
        if (isset($row['status'])) {
            if (is_numeric($row['status'])) {
                 $status = Status::find($row['status']);
            } else {
                 // Assuming Status model has a 'status_name' or similar field. 
                 $status = Status::where('status_name', $row['status'])->first();
            }
        }
        
         // Parse birth date
         try {
             // Excel numeric date handling or string parsing
             if (isset($row['birth_date']) && is_numeric($row['birth_date'])) {
                 $birthDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['birth_date']);
             } else {
                 $birthDate = isset($row['birth_date']) ? Carbon::parse($row['birth_date']) : null;
             }
         } catch (\Exception $e) {
             $birthDate = null;
         }

        return new Student([
            'identity_number'   => $row['identity_number'],
            'full_name'         => $row['full_name'],
            'birth_date'        => $birthDate,
            'gender'            => $row['gender'] ?? null,
            'enrollment_type'   => $row['enrollment_type'] ?? 'sat_mon_wed', 
            'student_groups_id' => $studentGroup ? $studentGroup->id : null,
            'status_id'         => $status ? $status->id : null, 
            'parent_phone'      => $row['parent_phone'] ?? null,
            'notes'             => $row['notes'] ?? null,
            'activation'        => 1, // Default to active
             'added_type' => 1, // Assuming 1 for imported or manual
        ]);
    }
}
