<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentFiltterExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $filters;
    private $rowNumber = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return Student::query()
            ->with(['studentGroup', 'status'])
            ->when(!empty($this->filters['searchIdentityNumber']), fn($q) => $q->where('identity_number', $this->filters['searchIdentityNumber']))
            ->when(!empty($this->filters['searchStudentName']), fn($q) => $q->where('id', $this->filters['searchStudentName']))
            ->when(!empty($this->filters['searchStudentGroupName']), fn($q) => $q->where('student_groups_id', $this->filters['searchStudentGroupName']))
            ->when(!empty($this->filters['searchEnrollment']), fn($q) => $q->where('enrollment_type', $this->filters['searchEnrollment']))
            ->when(!empty($this->filters['searchActivation']), fn($q) => $q->where('activation', $this->filters['searchActivation']))
            ->when(!empty($this->filters['searchBatchNo']), fn($q) => $q->whereHas('studentGroup', fn($sq) => $sq->where('batch_no', $this->filters['searchBatchNo'])))
            ->when(!empty($this->filters['searchRegionId']), fn($q) => $q->whereHas('studentGroup', fn($sq) => $sq->where('region_id', $this->filters['searchRegionId'])))
            ->when(!empty($this->filters['searchCityId']), fn($q) => $q->whereHas('studentGroup', fn($sq) => $sq->where('city_id', $this->filters['searchCityId'])))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($student) {
                $this->rowNumber++;
                return [
                    'sequence' => $this->rowNumber,
                    'full_name' => $student->full_name,
                    'identity_number' => $student->identity_number,
                    'birth_date' => $student->birth_date,
                    'gender' => $student->gender == 2 ? 'Male' : 'Female',
                    'activation' => $student->activation == 1 ? 'Active' : 'Inactive',
                    'enrollment_type' => $student->enrollment_type,
                    'group' => $student->studentGroup?->name, 
                    'region_id' => $student->studentGroup?->region->region_name, 
                    'city_id' => $student->studentGroup?->city->city_name,     
                    'status' => $student->status?->status_name,
                    'batch_no' => $student->studentGroup?->batch_no,
                    'created_at' => $student->created_at->format('Y-m-d H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Sequence',
            'Full Name',
            'Identity Number',
            'Birth Date',
            'Gender',
            'Activation',
            'Enrollment Type',
            'Group',
            'Region',
            'City',
            'Status',
            'Batch No',
            'Created At',
        ];
    }
}
